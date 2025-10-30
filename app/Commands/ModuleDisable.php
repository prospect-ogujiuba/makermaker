<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;
use MakerMaker\Services\ModuleService;

class ModuleDisable extends Command
{
    protected $command = [
        'module:disable {slug} {?--keep-data}',
        'Disable a module and optionally remove its data',
        'This command disables a module by removing its files from app directories. Use --keep-data to preserve database tables.',
    ];

    public function exec()
    {
        $slug = $this->getArgument('slug');
        $keepData = $this->getOption('keep-data');
        
        if (!$slug) {
            $this->error('Module slug is required');
            $this->line('Usage: php galaxy module:disable <slug> [--keep-data]');
            return;
        }
        
        $this->warning("Disabling module: {$slug}");
        
        if ($keepData) {
            $this->info('Database tables will be preserved');
        } else {
            $this->error('⚠ WARNING: Database tables will be dropped!');
        }
        
        $this->line('');
        
        // Confirmation prompt
        if (!$this->confirm('Are you sure you want to disable this module?')) {
            $this->line('Operation cancelled');
            return;
        }
        
        try {
            $service = new ModuleService();
            $results = $service->disableModule($slug, $keepData);
            
            // Display results
            $this->success("✓ Module '{$slug}' disabled successfully!");
            $this->line('');
            
            // Show removed files
            if (!empty($results['actions']['files'])) {
                $this->info('Files removed:');
                foreach ($results['actions']['files'] as $file) {
                    $this->line("  ✓ {$file}");
                }
                $this->line('');
            }
            
            // Show migration status
            if (!empty($results['actions']['migrations'])) {
                $this->info('Migrations rolled back:');
                foreach ($results['actions']['migrations'] as $migration) {
                    $this->line("  ✓ {$migration}");
                }
                $this->line('');
            } elseif (!empty($results['actions']['data_kept'])) {
                $this->success('Database tables preserved');
                $this->line('');
            }
            
            // Show removed capabilities
            if (!empty($results['actions']['capabilities_removed'])) {
                $this->info('Capabilities removed:');
                foreach ($results['actions']['capabilities_removed'] as $cap) {
                    $this->line("  ✓ {$cap}");
                }
                $this->line('');
            }
            
            $this->line('');
            $this->success('Module has been deactivated');
            $this->line('');
            
        } catch (\Exception $e) {
            $this->error('Failed to disable module: ' . $e->getMessage());
            $this->line('');
            
            // Provide helpful suggestions
            if (strpos($e->getMessage(), 'not found') !== false) {
                $this->info('Available modules:');
                $this->line('Run: php galaxy module:list');
            } elseif (strpos($e->getMessage(), 'not enabled') !== false) {
                $this->info('This module is already inactive.');
            } elseif (strpos($e->getMessage(), 'depends on it') !== false) {
                $this->info('Disable dependent modules first.');
            }
            
            $this->line('');
        }
    }
    
}
