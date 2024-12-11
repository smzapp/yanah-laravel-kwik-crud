<?php

namespace Yanah\LaravelKwik\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Yanah\LaravelKwik\Traits\FlagGeneratorTrait;
use Yanah\LaravelKwik\App\Exceptions\FileExistsException;

class InstallationCommand extends Command
{
    use FlagGeneratorTrait;

    protected $signature = 'kwik:install {--client=}';
    
    protected $description = 'Package Installation';

    // Add custom route folder
    // generate config file automatically
    // publish to Kernel.php
    // Create vue scafold for base of VUEJS: BaseCrud inside Pages & Pagination
    public function handle()
    {
        $client = $this->option('client');

        try  {
            if($client == 'reactjs') {
                // comming soon
                $this->info("ReactJS scaffold is coming soon!");
            } else {
                $this->generateVueFiles();
            }

            $this->info("Kwik installation completed!");
            
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function generateVueFiles()
    {
        $filesToGenerate = [
            [
                'fileName' => 'BasePage.vue',
                'targetPath' => resource_path('js/Pages/BasePage.vue'),
                'stubPath' => __DIR__ . '/stubs/vue/basepage_vue.stub',
            ],
            [
                'fileName' => 'BaseCrudLayout.vue',
                'targetPath' => resource_path('js/Layouts/BaseLayout.vue'),
                'stubPath' => __DIR__ . '/stubs/vue/baselayout_vue.stub',
            ],
        ];
    
        foreach ($filesToGenerate as $file) {
            $this->generateClientFiles($file['fileName'], $file['targetPath'], $file['stubPath']);
        }
    }

    protected function generateClientFiles(string $fileName, string $targetPath, string $stubPath)
    {
        $directory = dirname($targetPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
            $this->info("Directory created: {$directory}");
        }
    
        if (File::exists($targetPath)) {
            $this->error("File already exists: {$targetPath}");
            return;
        }
    
        if (!File::exists($stubPath)) {
            throw new \Exception("Stub file not found: {$stubPath}");
        }
    
        $stubContent = File::get($stubPath);
    
        File::put($targetPath, $stubContent);
    
        $this->info("Vue file created: {$targetPath}");
    }
}
