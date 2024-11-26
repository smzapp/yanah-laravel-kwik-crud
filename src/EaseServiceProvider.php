<?php

namespace Yanah\LaravelKwik;

use Illuminate\Support\ServiceProvider;
use Yanah\LaravelKwik\Console\Commands\GenerateResource;
use Yanah\LaravelKwik\Console\Commands\InstallationCommand;

class EaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Display config ease dynamically 

                // kwik:install -> This should generate config/ease.php

                InstallationCommand::class,
                
                GenerateResource::class,
            ]);
        }
    }
}
