<?php

namespace rafaelnuansa\MapCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
// Import the multi-select prompt
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;

class RNCrudCommand extends Command
{
    protected $signature = 'make:crud {name : The name of the model} 
                            {--fields= : Define fields} 
                            {--s|soft-delete : Use SoftDeletes}
                            {--m|no-migration : Skip migration}
                            {--force : Overwrite existing files}';

    protected $description = 'Generate advanced CRUD with interactive multi-select and ORM relations';

    public function handle()
    {
        $inputName = $this->argument('name');
        $fieldsInput = $this->option('fields');
        $force = $this->option('force');
        $softDelete = $this->option('soft-delete');
        $noMigration = $this->option('no-migration');

        // 1. Interactive Selection using Laravel Prompts (Arrow Keys + Space)
        $type = select(
            label: 'Select Controller type?',
            options: ['Web (Blade)', 'API (JSON)'],
            default: 'Web (Blade)'
        );
        $isApi = ($type === 'API (JSON)');

        // 2. Multi-select for files
        $options = ['Model', 'Controller', 'Routes'];
        if (!$noMigration) $options[] = 'Migration';
        if (!$isApi) $options[] = 'Views (Blade)';

        $selectedFiles = multiselect(
            label: 'Which files would you like to generate? (Space to select, Enter to confirm)',
            options: $options,
            default: $options, // Select all by default
            required: true
        );

        // Parsing Name & Folders
        $nameParts = explode('/', $inputName);
        $modelName = Str::studly(end($nameParts));
        $subFolder = count($nameParts) > 1 ? implode('/', array_slice($nameParts, 0, -1)) : '';

        $tableName = Str::plural(Str::snake($modelName));
        $variableName = Str::camel($modelName);
        $fields = $this->parseFields($fieldsInput);

        $paths = $this->getPaths($modelName, $subFolder, $tableName);

        // 3. Execution
        if (in_array('Migration', $selectedFiles)) {
            $this->generateMigration($tableName, $fields, $paths['Migration'], $force, $softDelete);
        }

        if (in_array('Model', $selectedFiles)) {
            $this->generateModel($modelName, $fields, $paths['Model'], $force, $softDelete);
        }

        if (in_array('Controller', $selectedFiles)) {
            $stubType = $isApi ? 'controller.api' : 'controller';
            $namespace = $subFolder ? "App\Http\Controllers\\" . str_replace('/', '\\', $subFolder) : "App\Http\Controllers";
            $this->generateController($modelName, $variableName, $tableName, $namespace, $stubType, $paths['Controller'], $fields, $force);
        }

        if (in_array('Views (Blade)', $selectedFiles) && !$isApi) {
            $this->generateViews($tableName, $modelName, $fields, $force);
        }

        if (in_array('Routes', $selectedFiles)) {
            $this->appendRoute($modelName, $tableName, $subFolder, $isApi);
        }

        $this->newLine();
        $this->info("Successfully generated CRUD for $modelName!");

        if (in_array('Migration', $selectedFiles) && $this->confirm("Would you like to run php artisan migrate?", true)) {
            $this->call('migrate');
        }
    }

    protected function getPaths($model, $folder, $table)
    {
        $controllerName = "{$model}Controller";
        $cPath = $folder ? "Http/Controllers/{$folder}/{$controllerName}.php" : "Http/Controllers/{$controllerName}.php";

        return [
            'Model'      => base_path("app/Models/{$model}.php"),
            'Controller' => base_path("app/{$cPath}"),
            'Migration'  => base_path("database/migrations/" . date('Y_m_d_His') . "_create_{$table}_table.php"),
        ];
    }

    protected function parseFields($input)
    {
        if (!$input) return [['name' => 'name', 'type' => 'string', 'is_relation' => false]];

        $fields = [];
        foreach (explode(',', $input) as $pair) {
            $parts = explode(':', $pair);
            $name = trim($parts[0]);
            $isRelation = str_ends_with($name, '_id');

            $fields[] = [
                'name' => $name,
                'type' => $isRelation ? 'foreignId' : ($parts[1] ?? 'string'),
                'is_relation' => $isRelation,
                'relation_name' => $isRelation ? Str::camel(str_replace('_id', '', $name)) : null,
                'related_model' => $isRelation ? Str::studly(str_replace('_id', '', $name)) : null,
            ];
        }
        return $fields;
    }

    protected function generateMigration($tableName, $fields, $path, $force, $softDelete)
    {
        if (File::exists($path) && !$force) {
            $this->warn("Migration already exists, skipping...");
            return;
        }

        $schemaFields = "";
        foreach ($fields as $field) {
            if ($field['is_relation']) {
                $schemaFields .= "\$table->foreignId('{$field['name']}')->constrained()->onDelete('cascade');\n            ";
            } else {
                $schemaFields .= "\$table->{$field['type']}('{$field['name']}');\n            ";
            }
        }

        if ($softDelete) {
            $schemaFields .= "\$table->softDeletes();\n            ";
        }

        $stub = File::get(__DIR__ . "/../../stubs/migration.stub");
        $content = str_replace(['{{tableName}}', '{{fields}}'], [$tableName, $schemaFields], $stub);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        $this->line("<info>Created Migration:</info> {$path}");
    }

    protected function generateModel($modelName, $fields, $path, $force, $softDelete)
    {
        if (File::exists($path) && !$force) {
            $this->warn("Model already exists, skipping...");
            return;
        }

        $relations = "";
        foreach ($fields as $field) {
            if ($field['is_relation']) {
                $relations .= "\n    public function {$field['relation_name']}()\n    {\n        return \$this->belongsTo({$field['related_model']}::class);\n    }\n";
            }
        }

        $useSoftDeletes = $softDelete ? "use Illuminate\Database\Eloquent\SoftDeletes;\n" : "";
        $traitSoftDeletes = $softDelete ? "    use SoftDeletes;\n" : "";

        $stub = File::get(__DIR__ . "/../../stubs/model.stub");
        $content = str_replace(
            ['{{modelName}}', '{{relations}}', '{{useSoftDeletes}}', '{{traitSoftDeletes}}'], 
            [$modelName, $relations, $useSoftDeletes, $traitSoftDeletes], 
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        $this->line("<info>Created Model:</info> app/Models/{$modelName}.php");
    }

    protected function generateController($model, $var, $table, $namespace, $stubName, $path, $fields, $force)
    {
        if (File::exists($path) && !$force) {
            $this->warn("Controller already exists, skipping...");
            return;
        }

        $validationRules = "";
        foreach ($fields as $field) {
            $validationRules .= "'{$field['name']}' => 'required',\n            ";
        }

        $stub = File::get(__DIR__ . "/../../stubs/{$stubName}.stub");
        $content = str_replace(
            ['{{modelName}}', '{{variableName}}', '{{tableName}}', '{{namespace}}', '{{validationRules}}'],
            [$model, $var, $table, $namespace, $validationRules],
            $stub
        );

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        $this->line("<info>Created Controller:</info> {$path}");
    }

    protected function generateViews($table, $model, $fields, $force)
    {
        $viewPath = resource_path("views/{$table}");
        File::ensureDirectoryExists($viewPath);

        $tableHeaders = "";
        $tableData = "";
        foreach ($fields as $field) {
            $label = Str::title(str_replace('_', ' ', $field['name']));
            $tableHeaders .= "<th class=\"border p-2 text-left\">{$label}</th>\n                ";
            $tableData .= "<td class=\"border p-2\">{{ \$item->{$field['name']} }}</td>\n                ";
        }

        $formInputs = "";
        foreach ($fields as $field) {
            $label = Str::title(str_replace('_', ' ', $field['name']));
            $formInputs .= "
        <div class=\"mb-4\">
            <label class=\"block font-bold\">{$label}</label>
            <input type=\"text\" name=\"{$field['name']}\" value=\"{{ isset(\${$table}) ? \${$table}->{$field['name']} : old('{$field['name']}') }}\" class=\"border p-2 w-full\" required>
        </div>\n";
        }

        $views = ['index', 'create', 'edit', 'show'];
        foreach ($views as $view) {
            $target = "{$viewPath}/{$view}.blade.php";
            if (File::exists($target) && !$force) continue;

            $stubFile = __DIR__ . "/../../stubs/views/{$view}.stub";
            if (!File::exists($stubFile)) continue;

            $content = File::get($stubFile);
            $content = str_replace(
                ['{{tableName}}', '{{modelName}}', '{{tableHeaders}}', '{{tableData}}', '{{formInputs}}'],
                [$table, $model, $tableHeaders, $tableData, $formInputs],
                $content
            );
            File::put($target, $content);
        }
    }

    protected function appendRoute($model, $table, $subFolder, $isApi)
    {
        $file = $isApi ? 'routes/api.php' : 'routes/web.php';
        $path = base_path($file);

        $namespacePrefix = $subFolder ? str_replace('/', '\\', $subFolder) . "\\" : "";
        $controllerNamespace = "App\Http\Controllers\\{$namespacePrefix}{$model}Controller";
        $routeLine = "\nRoute::resource('$table', \\$controllerNamespace::class);";
        if (!Str::contains(File::get($path), "Route::resource('$table'")) {
            File::append($path, $routeLine);
            $this->line("<info>Updated Routes:</info> {$file}");
        }
    }
}