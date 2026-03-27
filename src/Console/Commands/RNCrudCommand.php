<?php

namespace rafaelnuansa\MapCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RNCrudCommand extends Command
{
    protected $signature = 'make:crud {name}';
    protected $description = 'Generate complete CRUD including Model, Controller, Migration, and Routing';

    public function handle()
    {
        $name = $this->argument('name');

        // 1. Validasi Input: Pastikan hanya karakter alfanumerik (mencegah error nama class)
        if (!preg_match('/^[a-z_][a-z0-9_]*$/i', $name)) {
            $this->error("Error: Invalid name '$name'. Please use only alphanumeric characters.");
            return;
        }

        $modelName = Str::studly($name);
        $tableName = Str::plural(Str::snake($name));
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";

        $this->info("Starting CRUD generation for: $modelName");

        $paths = [
            'Model'      => "app/Models/{$modelName}.php",
            'Controller' => "app/Http/Controllers/{$modelName}Controller.php",
            'Migration'  => "database/migrations/{$migrationName}",
            'Route'      => "routes/web.php (Updated)",
        ];

        // 2. Proses Generate File
        $this->generateFromStub($modelName, 'model', base_path($paths['Model']));
        $this->generateFromStub($modelName, 'controller', base_path($paths['Controller']));
        $this->generateFromStub($modelName, 'migration', base_path($paths['Migration']));

        // 3. Tambahkan Routing
        $this->appendRoute($modelName, $tableName);

        // 4. Tampilkan Tabel Ringkasan
        $this->newLine();
        $this->line("File Generation Summary:");
        
        $tableData = [];
        foreach ($paths as $type => $location) {
            $tableData[] = [$type, $location, 'Created'];
        }
        
        $this->table(['Type', 'File Location', 'Status'], $tableData);

        // 5. Fitur Auto-Migrate: Tanya user apakah ingin langsung migrate database
        $this->newLine();
        if ($this->confirm("Files generated successfully! Do you want to run 'php artisan migrate' now?", true)) {
            $this->info("Running migrations...");
            $this->call('migrate');
        }

        $this->newLine();
        $this->info("All processes completed!");
    }

    protected function generateFromStub($name, $stubName, $targetPath)
    {
        $stubPath = __DIR__ . "/../../stubs/{$stubName}.stub";
        $customStubPath = base_path("stubs/vendor/rncrud/{$stubName}.stub");
        $finalStubPath = File::exists($customStubPath) ? $customStubPath : $stubPath;

        if (!File::exists($finalStubPath)) {
            $this->error("Error: Stub for {$stubName} not found at: {$finalStubPath}");
            return;
        }

        $content = File::get($finalStubPath);
        $content = str_replace(
            ['{{modelName}}', '{{variableName}}', '{{tableName}}'],
            [$name, Str::camel($name), Str::plural(Str::snake($name))],
            $content
        );

        File::ensureDirectoryExists(dirname($targetPath));
        File::put($targetPath, $content);
    }

    protected function appendRoute($modelName, $tableName)
    {
        $routePath = base_path('routes/web.php');
        
        if (!File::exists($routePath)) {
            $this->warn("Warning: routes/web.php not found. Skipping route registration.");
            return false;
        }

        $controllerNamespace = "App\Http\Controllers\\{$modelName}Controller";
        $routeLine = "\nRoute::resource('$tableName', \\$controllerNamespace::class);";

        $currentContent = File::get($routePath);
        if (!Str::contains($currentContent, "Route::resource('$tableName'")) {
            File::append($routePath, $routeLine);
            return true;
        }
        return false;
    }
}