<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;
use MakerMaker\Services\ModuleService;

class ModuleEnable extends Command
{
    protected $command = [
        'module:enable {slug}',
        'Enable a module and run its migrations',
        'This command enables a module by copying its files to the app directories, running migrations, and registering capabilities.',
    ];

    public function exec()
    {
        $slug = $this->getArgument('slug');
        
        if (!$slug) {
            $this->error('Module slug is required');
            $this->line('Usage: php galaxy module:enable <slug>');
            return;
        }
        
        $this->info("Enabling module: {$slug}");
        $this->line('');
        
        try {
            $service = new ModuleService();
            $results = $service->enableModule($slug);
            
            // Display results
            $this->success("✓ Module '{$slug}' enabled successfully!");
            $this->line('');
            
            // Show copied files
            if (!empty($results['actions']['files'])) {
                $this->info('Files copied:');
                foreach ($results['actions']['files'] as $file) {
                    $this->line("  ✓ {$file}");
                }
                $this->line('');
            }
            
            // Show executed migrations
            if (!empty($results['actions']['migrations'])) {
                $this->info('Migrations executed:');
                foreach ($results['actions']['migrations'] as $migration) {
                    $this->line("  ✓ {$migration}");
                }
                $this->line('');
            }
            
            // Show registered capabilities
            if (!empty($results['actions']['capabilities'])) {
                $this->info('Capabilities registered:');
                foreach ($results['actions']['capabilities'] as $cap) {
                    $this->line("  ✓ {$cap}");
                }
                $this->line('');
            }
            
            $this->line('');
            $this->success('Module is now active and ready to use!');
            $this->line('');
            
        } catch (\Exception $e) {
            $this->error('Failed to enable module: ' . $e->getMessage());
            $this->line('');
            
            // Provide helpful suggestions
            if (strpos($e->getMessage(), 'not found') !== false) {
                $this->info('Available modules:');
                $this->line('Run: php galaxy module:list');
            } elseif (strpos($e->getMessage(), 'already enabled') !== false) {
                $this->info('This module is already active.');
            } elseif (strpos($e->getMessage(), 'requires') !== false) {
                $this->info('Make sure all dependencies are enabled first.');
            } elseif (strpos($e->getMessage(), 'conflicts') !== false) {
                $this->info('Disable conflicting modules first.');
            }
            
            $this->line('');
        }
    }
}
