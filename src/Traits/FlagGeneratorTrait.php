<?php

namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Str;

trait FlagGeneratorTrait 
{
    protected function createMigration($name)
    {
        $name = Str::plural($name);
        
        $migrationName = 'create_' . strtolower($name) . '_table';
    
        $this->call('make:migration', [
            'name' => $migrationName,
            '--create' => strtolower($name), 
        ]);
    
        $this->info("Migration created for {$name} model");
    }
}