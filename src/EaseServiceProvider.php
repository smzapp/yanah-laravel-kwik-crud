<?php

namespace Yanah\LaravelKwik;

use Illuminate\Support\ServiceProvider;
use Yanah\LaravelKwik\Console\Commands\GenerateResource;
use Yanah\LaravelKwik\Console\Commands\InstallationCommand;

class EaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfigurations();

        if ($this->app->runningInConsole()) {
            $this->commands([
                // Display config ease dynamically 

                // kwik:install -> This should generate config/ease.php

                InstallationCommand::class,
                
                GenerateResource::class,
            ]);
        }
    }

    private function publishConfigurations()
    {
        $this->publishes([
            __DIR__ . '/Config/main-config.php' => config_path('kwik/main-config.php'),
            __DIR__ . '/Config/crud-config.php' => config_path('kwik/crud-config.php'),
        ], 'kwikconfig');
    }

    // public function register()
    // {
    //     $this->mergeConfigFrom(__DIR__ . '/Config/main-config.php', 'kwik.main');
    //     $this->mergeConfigFrom(__DIR__ . '/Config/crud-config.php', 'kwik.crud');
    // }
}
