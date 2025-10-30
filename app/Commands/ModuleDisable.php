<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;
use MakerMaker\Services\ModuleService;

class ModuleDisable extends Command
{
    protected $command = [
        'module:disable {slug} {?--keep-data} {?--force}',
        'Disable a module and optionally remove its data',
        'This command disables a module by removing its files from app directories. Use --keep-data to preserve database tables. Use --force to skip safety check.',
    ];

    public function exec()
    {
        $slug = $this->getArgument('slug');
        $keepData = $this->getOption('keep-data');
        $force = $this->getOption('force');
        
        if (!$slug) {
            $this->error('Module slug is required');
            $this->line('Usage: php galaxy module:disable <slug> [--keep-data] [--force]');
            return;
        }
        
        $this->warning("Disabling module: {$slug}");
        $this->line('');
        
        // Safety check - require --force for data-destructive operations
        if (!$keepData && !$force) {
            $this->error('WARNING: This will DROP database tables!');
            $this->line('');
            $this->info('To proceed with data removal, use:');
            $this->line("  php galaxy module:disable {$slug} --force");
            $this->line('');
            $this->info('To disable module but keep database tables, use:');
            $this->line("  php galaxy module:disable {$slug} --keep-data");
            $this->line('');
            return;
        }
        
        if ($keepData) {
            $this->info('Database tables will be preserved');
        } else {
            $this->error('WARNING: Database tables will be dropped (--force flag used)');
        }
        
        $this->line('');
        
        try {
            $service = new ModuleService();
            $results = $service->disableModule($slug, $keepData);
            
            // Display summary first
            $this->success("Module '{$slug}' disabled successfully!");
            $this->line('');
            
            if (!empty($results['summary'])) {
                $this->info('Summary:');
                if (isset($results['summary']['files_removed'])) {
                    $this->line("  - Files removed: {$results['summary']['files_removed']}");
                }
                if (isset($results['summary']['migrations_rolled_back'])) {
                    $this->line("  - Migrations rolled back: {$results['summary']['migrations_rolled_back']}");
                }
                if (isset($results['summary']['capabilities_removed'])) {
                    $this->line("  - Capabilities removed: {$results['summary']['capabilities_removed']}");
                }
                $this->line('');
            }
            
            // Show removed files
            if (!empty($results['actions']['files'])) {
                $this->info('Files removed:');
                foreach ($results['actions']['files'] as $file) {
                    $this->line("  * {$file}");
                }
                $this->line('');
            } else {
                $this->warning('No files were removed (module may be partially disabled)');
                $this->line('');
            }
            
            // Show migration status
            if (!empty($results['actions']['migrations'])) {
                $this->info('Migrations rolled back:');
                foreach ($results['actions']['migrations'] as $migration) {
                    $this->line("  * {$migration}");
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
                    $this->line("  * {$cap}");
                }
                $this->line('');
            }
            
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