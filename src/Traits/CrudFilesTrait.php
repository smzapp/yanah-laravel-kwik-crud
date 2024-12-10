<?php

namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Str;
use Yanah\LaravelKwik\App\Exceptions\FileExistsException;
use Illuminate\Support\Facades\File;

trait CrudFilesTrait 
{
    private $modelName;
    private $commandsDir;
  
    public function getPluralModel()
    {
        return Str::plural($this->modelName);
    }

    public function getSingularModel()
    {
        return Str::singular($this->modelName);
    }

    public function generateFiles($name, $dir)
    {
        $this->commandsDir = $dir;
        $this->modelName   = $name;

        $this->generateCreateCrud();

        $this->generateEditCrud();

        $this->generateListCrud();
    }


    public function generateCreateCrud()
    {
        $fileName = "{$this->getSingularModel()}Create.php";

        $directory = app_path("CrudKwik/{$this->getPluralModel()}");
        $path = "{$directory}/{$fileName}";

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path)) {
            throw new FileExistsException("File already exists at {$path}!");
        }

        $stub = File::get($this->commandsDir. '/stubs/crudcreate.stub');
        $content = str_replace(
            ['{{modelPlural}}', '{{modelName}}'],
            [$this->getPluralModel() , $this->getSingularModel()],
            $stub
        );

        File::put($path, $content);
        $this->info("{$fileName} created at {$path}");
    }

    public function generateEditCrud()
    {
        $fileName = "{$this->getSingularModel()}Edit.php";

        $path = app_path("CrudKwik/{$this->getPluralModel()}/{$fileName}");

        if (File::exists($path)) {
            throw new FileExistsException("File already exists at {$path}!");
        }

        $stub = File::get($this->commandsDir.'/stubs/crudedit.stub');
        $content = str_replace(
            ['{{modelPlural}}', '{{modelName}}'],
            [$this->getPluralModel() , $this->getSingularModel()],
            $stub
        );
        File::put($path, $content);
        $this->info("{$fileName} created at {$path}");
    }

    public function generateListCrud()
    {
        $fileName = "{$this->getSingularModel()}List.php";

        $path = app_path("CrudKwik/{$this->getPluralModel()}/{$fileName}");

        if (File::exists($path)) {
            throw new FileExistsException("File already exists at {$path}!");
        }

        $stub = File::get($this->commandsDir.'/stubs/crudlist.stub');
        $content = str_replace(
            ['{{modelPlural}}', '{{modelName}}'],
            [$this->getPluralModel() , $this->getSingularModel()],
            $stub
        );
        File::put($path, $content);
        $this->info("{$fileName} created at {$path}");
    }
}