<?php

namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Str;

trait FlagGeneratorTrait 
{
    protected function createMigration($name)
    {
        $tableName = Str::plural(Str::snake($name));
        
        $lowerName = strtolower($tableName);
        
        $migrationName = 'create_' . $lowerName . '_table';

        $this->call('make:migration', [
            'name' => $migrationName,
            '--create' => $lowerName,
        ]);

        $this->info("Migration created for {$name} model");
    }
}