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

        $this->info("🚀 Memulai proses generate CRUD untuk $modelName...");

        // 1. Generate Model, Migration, Controller menggunakan Stub
        $this->generateFromStub($modelName, 'model', app_path("Models/{$modelName}.php"));
        $this->generateFromStub($modelName, 'controller', app_path("Http/Controllers/{$modelName}Controller.php"));
        
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
        $this->generateFromStub($modelName, 'migration', database_path("migrations/{$migrationName}"));

        // 2. Tambahkan Routing otomatis ke web.php
        $this->appendRoute($modelName, $tableName);

        $this->info("✅ Semua file berhasil di-generate!");
    }

    protected function generateFromStub($name, $stubName, $targetPath)
    {
        $stubPath = __DIR__ . "/../../stubs/{$stubName}.stub";
        
        // Cek jika user punya custom stub
        $customStubPath = base_path("stubs/vendor/rncrud/{$stubName}.stub");
        $finalStubPath = File::exists($customStubPath) ? $customStubPath : $stubPath;

        $content = File::get($finalStubPath);
        $content = str_replace(
            ['{{modelName}}', '{{variableName}}', '{{tableName}}'],
            [$name, Str::camel($name), Str::plural(Str::snake($name))],
            $content
        );

        // Pastikan direktori tujuan ada
        File::ensureDirectoryExists(dirname($targetPath));
        File::put($targetPath, $content);
    }

    protected function appendRoute($modelName, $tableName)
    {
        $routePath = base_path('routes/web.php');
        $controllerNamespace = "App\Http\Controllers\\{$modelName}Controller";
        
        // Kode yang akan ditambahkan
        $routeLine = "\nRoute::resource('$tableName', \\$controllerNamespace::class);";

        // Cek apakah route sudah ada agar tidak duplikat
        $currentContent = File::get($routePath);
        if (!Str::contains($currentContent, $routeLine)) {
            File::append($routePath, $routeLine);
            $this->info("📍 Route resource '$tableName' telah ditambahkan ke web.php");
        } else {
            $this->warn("⚠️ Route untuk $tableName sudah ada, dilewati.");
        }
    }
}