<?php
namespace Yanah\LaravelKwik\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Yanah\LaravelKwik\Traits\FlagGeneratorTrait;
use Yanah\LaravelKwik\Traits\CrudFilesTrait;
use Yanah\LaravelKwik\App\Exceptions\FileExistsException;

class CrudResource extends Command
{
    use FlagGeneratorTrait, CrudFilesTrait;

    protected $signature = 'kwik:crud {name} {--only=}';
    protected $description = 'Generate a controller, model for a resource'; // add migration, request validation, resource

    public function handle()
    {
        $name  = $this->argument('name');
        $only  = $this->option('only') ? explode(',', $this->option('only')) : [];
        try  {
            if (empty($only) || in_array('controller', $only)) {
                $this->generateController($name);
            }
            if (empty($only) || in_array('model', $only)) {
                $this->generateModel($name);
            }
            
            if (empty($only) || in_array('crudfiles', $only)) {
                $this->generateFiles($name, __DIR__);
            }

            $this->info("Resources for {$name} generated successfully!");
            
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function generateController($name)
    {
        $path = app_path("Http/Controllers/{$name}Controller.php");
        if (File::exists($path)) {
            throw new FileExistsException("Controller already exists at {$path}!");
        }

        $this->setModelName($name);

        $stub = File::get(__DIR__.'/stubs/controller.stub');
        $content = str_replace(
            ['{{className}}', '{{modelName}}', '{{modelPlural}}', '{{modelSlug}}'],
            ["{$name}Controller", $name, $this->getPluralModel(), strtolower($this->getPluralModel())],
            $stub
        );
        File::put($path, $content);
        $this->info("Controller created at {$path}");
    }

    protected function generateModel($name, $createMigration = true)
    {
        $path = app_path("Models/{$name}.php");
        if (File::exists($path)) {
            throw new FileExistsException("Model already exists at {$path}!");
        }

        $stub = File::get(__DIR__.'/stubs/model.stub');
        $content = str_replace('{{ className }}', $name, $stub);
        File::put($path, $content);
        $this->info("Model created at {$path}");

        if ($createMigration) {
            $this->createMigration($name);
        }
    }
}
