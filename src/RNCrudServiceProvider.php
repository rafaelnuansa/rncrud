<?php

namespace rafaelnuansa\MapCrud;

use Illuminate\Support\ServiceProvider;
use rafaelnuansa\MapCrud\Console\Commands\RNCrudCommand;

class RNCrudServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RNCrudCommand::class,
            ]);

            // Memungkinkan user memodifikasi template dengan php artisan vendor:publish
            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs/vendor/rncrud'),
            ], 'rncrud-stubs');
        }
    }

    public function register()
    {
        //
    }
}