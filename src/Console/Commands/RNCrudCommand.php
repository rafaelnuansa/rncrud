<?php

namespace rafaelnuansa\MapCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RNCrudCommand extends Command
{
    protected $signature = 'make:crud {name}';
    protected $description = 'Generate CRUD lengkap termasuk Model, Controller, Migration, dan Routing';

    public function handle()
    {
        $name = $this->argument('name');
        $modelName = Str::studly($name);
        $tableName = Str::plural(Str::snake($name));
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";

        $this->info("Memulai proses generate CRUD untuk: $modelName");

        // Definisikan lokasi file relatif terhadap root project
        $paths = [
            'Model'      => "app/Models/{$modelName}.php",
            'Controller' => "app/Http/Controllers/{$modelName}Controller.php",
            'Migration'  => "database/migrations/{$migrationName}",
            'Route'      => "routes/web.php (Updated)",
        ];

        // 1. Generate Files
        $this->generateFromStub($modelName, 'model', base_path($paths['Model']));
        $this->generateFromStub($modelName, 'controller', base_path($paths['Controller']));
        $this->generateFromStub($modelName, 'migration', base_path($paths['Migration']));

        // 2. Tambahkan Routing
        $this->appendRoute($modelName, $tableName);

        // 3. Tampilkan Ringkasan dalam Tabel
        $this->newLine();
        $this->line("Ringkasan File Terbuat:");
        
        $tableData = [];
        foreach ($paths as $type => $location) {
            $tableData[] = [$type, $location, 'Created'];
        }
        
        $this->table(['Tipe', 'Lokasi File', 'Status'], $tableData);

        $this->newLine();
        $this->info("Semua proses selesai! Silakan cek file Anda.");
    }

    protected function generateFromStub($name, $stubName, $targetPath)
    {
        $stubPath = __DIR__ . "/../../stubs/{$stubName}.stub";
        $customStubPath = base_path("stubs/vendor/rncrud/{$stubName}.stub");
        $finalStubPath = File::exists($customStubPath) ? $customStubPath : $stubPath;

        if (!File::exists($finalStubPath)) {
            $this->error("Error: Stub untuk {$stubName} tidak ditemukan!");
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