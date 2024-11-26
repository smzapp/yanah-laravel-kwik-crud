<?php

namespace Yanah\LaravelKwik\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Yanah\LaravelKwik\Traits\FlagGeneratorTrait;

class InstallationCommand extends Command
{
    use FlagGeneratorTrait;

    protected $signature = 'kwik:install';
    
    protected $description = 'Package Installation'; // add migration, request validation, resource

    public function handle()
    {
        // Add custom route folder

        // generate config file automatically

        // publish to Kernel.php

        // Create vue scafold for base of VUEJS: BaseCrud inside Pages & Pagination
    }

}
