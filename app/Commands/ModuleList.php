<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;
use MakerMaker\Services\ModuleService;

class ModuleList extends Command
{
    protected $command = [
        'module:list {?--active} {?--inactive}',
        'List all modules with their status',
        'This command shows all available modules and their activation status. Use --active to show only active modules, or --inactive to show only inactive modules.',
    ];

    public function exec()
    {
        $activeOnly = $this->getOption('active');
        $inactiveOnly = $this->getOption('inactive');
        
        $service = new ModuleService();
        $modules = $service->getAllModules();
        
        if (empty($modules)) {
            $this->warning('No modules found in modules directory');
            return;
        }
        
        $this->line('');
        $this->info('Available Modules:');
        $this->line('');
        
        // Separate into active and inactive
        $activeModules = [];
        $inactiveModules = [];
        
        foreach ($modules as $slug => $metadata) {
            if ($metadata['active']) {
                $activeModules[$slug] = $metadata;
            } else {
                $inactiveModules[$slug] = $metadata;
            }
        }
        
        // Display active modules
        if (!$inactiveOnly && !empty($activeModules)) {
            $this->success('✓ ACTIVE MODULES (' . count($activeModules) . ')');
            $this->line('');
            
            foreach ($activeModules as $slug => $metadata) {
                $this->displayModule($metadata, true);
            }
        }
        
        // Display inactive modules
        if (!$activeOnly && !empty($inactiveModules)) {
            $this->line('');
            $this->warning('○ INACTIVE MODULES (' . count($inactiveModules) . ')');
            $this->line('');
            
            foreach ($inactiveModules as $slug => $metadata) {
                $this->displayModule($metadata, false);
            }
        }
        
        $this->line('');
        $this->info('Commands:');
        $this->line('  module:enable <slug>   - Enable a module');
        $this->line('  module:disable <slug>  - Disable a module');
        $this->line('  module:info <slug>     - Show detailed module information');
        $this->line('');
    }
    
    private function displayModule(array $metadata, bool $active): void
    {
        $status = $active ? '✓' : '○';
        $color = $active ? 'success' : 'warning';
        
        $this->line("  {$status} {$metadata['name']} ({$metadata['slug']}) - v{$metadata['version']}");
        $this->line("    " . $metadata['description']);
        
        // Show dependencies if any
        if (!empty($metadata['dependencies'])) {
            $this->line("    Dependencies: " . implode(', ', $metadata['dependencies']));
        }
        
        // Show conflicts if any
        if (!empty($metadata['conflicts'])) {
            $this->error("    Conflicts: " . implode(', ', $metadata['conflicts']));
        }
        
        $this->line('');
    }
}
