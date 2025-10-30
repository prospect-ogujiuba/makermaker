<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;
use MakerMaker\Services\ModuleService;

class ModuleInfo extends Command
{
    protected $command = [
        'module:info {slug}',
        'Display detailed information about a module',
        'This command shows comprehensive details about a module including files, dependencies, capabilities, and status.',
    ];

    public function exec()
    {
        $slug = $this->getArgument('slug');
        
        if (!$slug) {
            $this->error('Module slug is required');
            $this->line('Usage: php galaxy module:info <slug>');
            return;
        }
        
        try {
            $service = new ModuleService();
            $metadata = $service->getModuleMetadata($slug);
            
            if (!$metadata) {
                $this->error("Module '{$slug}' not found");
                $this->line('');
                $this->info('Available modules:');
                $this->line('Run: php galaxy module:list');
                return;
            }
            
            $this->line('');
            $this->line('═══════════════════════════════════════════════════');
            
            // Header
            $status = $metadata['active'] ? '✓ ACTIVE' : '○ INACTIVE';
            $color = $metadata['active'] ? 'success' : 'warning';
            $this->{$color}("  {$metadata['name']} ({$slug})");
            $this->line("  {$status} - Version {$metadata['version']}");
            
            $this->line('═══════════════════════════════════════════════════');
            $this->line('');
            
            // Description
            $this->info('Description:');
            $this->line("  {$metadata['description']}");
            $this->line('');
            
            // Author
            if (!empty($metadata['author'])) {
                $this->info('Author:');
                $this->line("  {$metadata['author']}");
                $this->line('');
            }
            
            // Dependencies
            if (!empty($metadata['dependencies'])) {
                $this->info('Dependencies:');
                foreach ($metadata['dependencies'] as $dep) {
                    $depMeta = $service->getModuleMetadata($dep);
                    $depStatus = $depMeta && $depMeta['active'] ? '✓' : '✗';
                    $this->line("  {$depStatus} {$dep}");
                }
                $this->line('');
            }
            
            // Conflicts
            if (!empty($metadata['conflicts'])) {
                $this->error('Conflicts with:');
                foreach ($metadata['conflicts'] as $conflict) {
                    $this->line("  ! {$conflict}");
                }
                $this->line('');
            }
            
            // Namespace
            if (!empty($metadata['namespace'])) {
                $this->info('Namespace:');
                $this->line("  {$metadata['namespace']}");
                $this->line('');
            }
            
            // Capabilities
            if (!empty($metadata['capabilities'])) {
                $this->info('Capabilities:');
                foreach ($metadata['capabilities'] as $cap) {
                    $this->line("  • {$cap}");
                }
                $this->line('');
            }
            
            // Files Summary
            $this->info('Files:');
            
            $fileTypes = [
                'controllers' => 'Controllers',
                'models' => 'Models',
                'policies' => 'Policies',
                'fields' => 'Fields',
                'resources' => 'Resources',
                'migrations' => 'Migrations',
                'views' => 'Views'
            ];
            
            foreach ($fileTypes as $type => $label) {
                if (!empty($metadata['files'][$type])) {
                    $count = is_array($metadata['files'][$type]) ? count($metadata['files'][$type]) : 0;
                    $this->line("  • {$label}: {$count}");
                }
            }
            
            $this->line('');
            
            // Detailed file listing if requested
            $this->line('───────────────────────────────────────────────────');
            $this->info('Detailed File Listing:');
            $this->line('');
            
            foreach ($fileTypes as $type => $label) {
                if (empty($metadata['files'][$type])) {
                    continue;
                }
                
                $this->success($label . ':');
                
                if ($type === 'views' && is_array($metadata['files'][$type])) {
                    foreach ($metadata['files'][$type] as $dir => $files) {
                        foreach ($files as $file) {
                            $this->line("  • {$dir}/{$file}");
                        }
                    }
                } else {
                    foreach ($metadata['files'][$type] as $file) {
                        $this->line("  • {$file}");
                    }
                }
                
                $this->line('');
            }
            
            // Action suggestions
            $this->line('═══════════════════════════════════════════════════');
            if ($metadata['active']) {
                $this->line('To disable this module:');
                $this->line("  php galaxy module:disable {$slug}");
                $this->line("  php galaxy module:disable {$slug} --keep-data");
            } else {
                $this->line('To enable this module:');
                $this->line("  php galaxy module:enable {$slug}");
            }
            $this->line('');
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->line('');
        }
    }
}
