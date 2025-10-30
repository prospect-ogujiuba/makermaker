<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;

class ModuleCreate extends Command
{
    protected $command = [
        'module:create {name} {?--description=}',
        'Create a new module structure',
        'This command creates a new module directory with the standard structure and module.json metadata file.',
    ];

    public function exec()
    {
        $name = $this->getArgument('name');
        $description = $this->getOption('description') ?: "Description for {$name} module";
        
        if (!$name) {
            $this->error('Module name is required');
            $this->line('Usage: php galaxy module:create <name> [--description="Module description"]');
            return;
        }
        
        // Convert name to proper formats
        $slug = $this->toKebabCase($name);
        $pascalName = $this->toPascalCase($name);
        $namespace = "MakerMaker\\Modules\\{$pascalName}";
        
        $this->info("Creating module: {$pascalName} ({$slug})");
        $this->line('');
        
        try {
            $modulePath = MAKERMAKER_PLUGIN_DIR . "modules/{$slug}";
            
            // Check if module already exists
            if (is_dir($modulePath)) {
                throw new \Exception("Module '{$slug}' already exists");
            }
            
            // Create module directory structure
            $directories = [
                '',
                'Controllers',
                'Models',
                'Auth',
                'Http/Fields',
                'resources',
                'migrations',
                'views',
            ];
            
            foreach ($directories as $dir) {
                $path = $modulePath . ($dir ? '/' . $dir : '');
                mkdir($path, 0755, true);
                $this->line("  ✓ Created directory: {$slug}/" . ($dir ?: '(root)'));
            }
            
            $this->line('');
            
            // Create module.json
            $metadata = [
                'name' => $pascalName,
                'slug' => $slug,
                'namespace' => $namespace,
                'description' => $description,
                'version' => '1.0.0',
                'author' => get_bloginfo('name'),
                'active' => false,
                'dependencies' => [],
                'conflicts' => [],
                'files' => [
                    'controllers' => [],
                    'models' => [],
                    'policies' => [],
                    'fields' => [],
                    'resources' => [],
                    'migrations' => [],
                    'views' => []
                ],
                'autoload' => [
                    'psr-4' => [
                        "{$namespace}\\" => "."
                    ]
                ],
                'capabilities' => []
            ];
            
            $metadataPath = $modulePath . '/module.json';
            file_put_contents(
                $metadataPath,
                json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
            
            $this->success("  ✓ Created module.json");
            $this->line('');
            
            $this->line('');
            $this->success("✓ Module '{$slug}' created successfully!");
            $this->line('');
            
            // Next steps
            $this->info('Next steps:');
            $this->line("  1. Add your controllers, models, and resources to: modules/{$slug}/");
            $this->line("  2. Use make:crud command with --module={$slug} flag:");
            $this->line("     php galaxy make:crud Product --module={$slug}");
            $this->line("  3. Update module.json with dependencies and capabilities");
            $this->line("  4. Enable the module:");
            $this->line("     php galaxy module:enable {$slug}");
            $this->line('');
            
        } catch (\Exception $e) {
            $this->error('Failed to create module: ' . $e->getMessage());
            $this->line('');
        }
    }
    
    private function generateReadme(string $name, string $slug, string $description, string $namespace): string
    {
        return <<<MD
# {$name} Module

{$description}

## Information

- **Slug**: `{$slug}`
- **Namespace**: `{$namespace}`
- **Version**: 1.0.0

## Installation

Enable this module:

```bash
php galaxy module:enable {$slug}
```

## Usage

[Add usage instructions here]

## Components

### Controllers
[List controllers here]

### Models
[List models here]

### Resources
[List resources here]

## Dependencies

[List any module dependencies here]

## Configuration

[Add any configuration notes here]

## Uninstallation

To disable this module and keep data:

```bash
php galaxy module:disable {$slug} --keep-data
```

To disable and remove all data:

```bash
php galaxy module:disable {$slug}
```
MD;
    }
    
    private function toKebabCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }
    
    private function toPascalCase(string $string): string
    {
        return str_replace(['-', '_', ' '], '', ucwords($string, '-_ '));
    }
}
