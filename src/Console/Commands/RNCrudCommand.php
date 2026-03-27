<?php

namespace rafaelnuansa\MapCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RNCrudCommand extends Command
{
    // Menambahkan opsi --force agar user bisa menimpa file secara sengaja
    protected $signature = 'make:crud {name} {--force}';
    protected $description = 'Generate complete CRUD including Model, Controller, Migration, and Routing';

    public function handle()
    {
        $name = $this->argument('name');
        $force = $this->option('force');

        // 1. Validasi Input
        if (!preg_match('/^[a-z_][a-z0-9_]*$/i', $name)) {
            $this->error("Error: Invalid name '$name'. Please use only alphanumeric characters.");
            return;
        }

        $modelName = Str::studly($name);
        $tableName = Str::plural(Str::snake($name));
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";

        // Definisikan path target
        $paths = [
            'Model'      => base_path("app/Models/{$modelName}.php"),
            'Controller' => base_path("app/Http/Controllers/{$modelName}Controller.php"),
            'Migration'  => base_path("database/migrations/{$migrationName}"),
        ];

        // 2. Validasi Keberadaan File (Kecuali jika --force digunakan)
        if (!$force) {
            foreach (['Model', 'Controller'] as $type) {
                if (File::exists($paths[$type])) {
                    $this->error("Error: {$type} already exists at {$paths[$type]}.");
                    $this->info("Use --force to overwrite the existing files.");
                    return;
                }
            }
        }

        $this->info("Starting CRUD generation for: $modelName");

        // 3. Proses Generate File
        $this->generateFromStub($modelName, 'model', $paths['Model']);
        $this->generateFromStub($modelName, 'controller', $paths['Controller']);
        $this->generateFromStub($modelName, 'migration', $paths['Migration']);

        // 4. Tambahkan Routing
        $this->appendRoute($modelName, $tableName);

        // 5. Tampilkan Tabel Ringkasan
        $this->newLine();
        $this->line("File Generation Summary:");
        
        $tableData = [
            ['Model', "app/Models/{$modelName}.php", 'Created'],
            ['Controller', "app/Http/Controllers/{$modelName}Controller.php", 'Created'],
            ['Migration', "database/migrations/{$migrationName}", 'Created'],
            ['Route', "routes/web.php", 'Updated'],
        ];
        
        $this->table(['Type', 'File Location', 'Status'], $tableData);

        // 6. Fitur Auto-Migrate
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
            $this->error("Error: Stub for {$stubName} not found.");
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