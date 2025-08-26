<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;

class Crud extends Command
{
    protected $command = [
        'make:crud {name} {?--force}',
        'Generate a complete CRUD setup with Model, Controller, Policy, Fields, and Views',
        'This command creates all necessary files for a CRUD operation including Model, Controller, Policy, Fields and basic view templates.',
    ];

    public function exec()
    {
        $name = $this->getArgument('name');
        $force = $this->getOption('force');

        if (!$name) {
            $this->error('Name argument is required');
            return;
        }

        // Convert name to different cases
        $pascalCase = $this->toPascalCase($name);
        $snakeCase = $this->toSnakeCase($name);
        $pluralSnakeCase = $this->pluralize($snakeCase);

        $this->info("Generating CRUD files for: {$pascalCase}");
        $this->info("Plural snake case: {$pluralSnakeCase}");

        // Generate each component
        $results = [];

        try {
            $results['model'] = $this->generateModel($pascalCase, $force);
            $results['controller'] = $this->generateController($pascalCase, $force);
            $results['policy'] = $this->generatePolicy($pascalCase, $force);
            $results['fields'] = $this->generateFields($pascalCase, $force);
            $results['views'] = $this->generateViews($pluralSnakeCase, $pascalCase, $force);
            $results['migration'] = $this->generateMigration($pascalCase, $force);

            // Summary
            $this->line('');
            $this->success('✓ CRUD generation completed successfully!');
            $this->line('');
            $this->info('Generated files:');

            foreach ($results as $type => $files) {
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $this->line("  - {$file}");
                    }
                } else {
                    $this->line("  - {$files}");
                }
            }

            $this->line('');
            $this->info('Next steps:');
            $this->line('1. Add routes for your controller in your routes file');
            $this->line('2. Run migrations if you created database tables');
            $this->line('3. Customize the generated files as needed');
        } catch (\Exception $e) {
            $this->error('Error generating CRUD: ' . $e->getMessage());
        }
    }

    protected function generateModel($name, $force = false)
    {
        $command = "php galaxy_makermaker make:model base {$name}";
        if ($force) {
            $command .= " --force";
        }

        exec($command, $output, $return);

        if ($return === 0) {
            return "app/Models/{$name}.php";
        }

        throw new \Exception("Failed to generate Model: " . implode("\n", $output));
    }

    protected function generateController($name, $force = false)
    {
        $command = "php galaxy_makermaker make:controller base {$name}";
        if ($force) {
            $command .= " --force";
        }

        exec($command, $output, $return);

        if ($return === 0) {
            return "app/Controllers/{$name}Controller.php";
        }

        throw new \Exception("Failed to generate Controller: " . implode("\n", $output));
    }

    protected function generatePolicy($name, $force = false)
    {
        $command = "php galaxy_makermaker make:policy {$name}Policy";
        if ($force) {
            $command .= " --force";
        }

        exec($command, $output, $return);

        if ($return === 0) {
            return "app/Policies/{$name}Policy.php";
        }

        throw new \Exception("Failed to generate Policy: " . implode("\n", $output));
    }

    protected function generateFields($name, $force = false)
    {
        $command = "php galaxy_makermaker make:fields {$name}Fields";
        if ($force) {
            $command .= " --force";
        }

        exec($command, $output, $return);

        if ($return === 0) {
            return "app/Fields/{$name}Fields.php";
        }

        throw new \Exception("Failed to generate Fields: " . implode("\n", $output));
    }

    protected function generateMigration($name, $force = false)
    {
        $migrationName = "create_{$this->toSnakeCase($name)}_table";
        $command = "php galaxy_makermaker make:migration {$migrationName}";
        if ($force) {
            $command .= " --force";
        }

        exec($command, $output, $return);

        if ($return === 0) {
            // Migration files are typically timestamped, so we return a generic path
            return "database/migrations/*_{$migrationName}.php";
        }

        throw new \Exception("Failed to generate Migration: " . implode("\n", $output));
    }

    protected function generateViews($pluralSnakeCase, $pascalCase, $force = false)
    {
        // Use the plugin's defined view path
        $pluginViewsPath = defined('TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH')
            ? TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH
            : __DIR__ . '/../../resources/views';

        $viewsDir = "{$pluginViewsPath}/{$pluralSnakeCase}";

        // Create directory if it doesn't exist
        if (!is_dir($viewsDir)) {
            mkdir($viewsDir, 0755, true);
        }

        $indexFile = "{$viewsDir}/index.php";
        $formFile = "{$viewsDir}/form.php";

        // Generate index view
        if (!file_exists($indexFile) || $force) {
            $indexContent = $this->getIndexViewTemplate($pascalCase, $pluralSnakeCase);
            file_put_contents($indexFile, $indexContent);
        }

        // Generate form view
        if (!file_exists($formFile) || $force) {
            $formContent = $this->getFormViewTemplate($pascalCase, $pluralSnakeCase);
            file_put_contents($formFile, $formContent);
        }

        return array($indexFile, $formFile);
    }

    protected function getIndexViewTemplate($pascalCase, $pluralSnakeCase)
    {
        $modelVariable = lcfirst($pascalCase);
        $pluralModelVariable = $this->pluralize($modelVariable);

        $template = "<?php\n";
        $template .= "/**\n";
        $template .= " * {$pascalCase} Index View\n";
        $template .= " * \n";
        $template .= " * This view displays a list of {$pluralModelVariable}.\n";
        $template .= " * Add your index/list functionality here.\n";
        $template .= " */\n";
        $template .= " \n";
        $template .= " tr_smart_index(\MakerMaker\Models\{$pascalCase}::class);\n";
        $template .= "?>";

        return $template;
    }

    protected function getFormViewTemplate($pascalCase, $pluralSnakeCase)
    {
        $modelVariable = ucwords($pascalCase);

        $template = "<?php\n";
        $template .= "/**\n";
        $template .= " * {$pascalCase} Form View\n";
        $template .= " * \n";
        $template .= " * This view displays a form for creating/editing {$modelVariable}.\n";
        $template .= " * Add your form fields and functionality here.\n";
        $template .= " */\n";
        $template .= "?>";

        return $template;
    }

    protected function toPascalCase($string)
    {
        return str_replace(' ', '', ucwords(str_replace(array('_', '-'), ' ', $string)));
    }

    protected function toSnakeCase($string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    protected function pluralize($word)
    {
        // Simple pluralization rules
        $irregular = array(
            'person' => 'people',
            'man' => 'men',
            'woman' => 'women',
            'child' => 'children',
            'foot' => 'feet',
            'tooth' => 'teeth',
            'mouse' => 'mice',
        );

        if (isset($irregular[$word])) {
            return $irregular[$word];
        }

        // Handle words ending in 'y'
        if (substr($word, -1) === 'y' && !in_array(substr($word, -2, 1), array('a', 'e', 'i', 'o', 'u'))) {
            return substr($word, 0, -1) . 'ies';
        }

        // Handle words ending in 's', 'sh', 'ch', 'x', 'z'
        if (preg_match('/(s|sh|ch|x|z)$/', $word)) {
            return $word . 'es';
        }

        // Handle words ending in 'f' or 'fe'
        if (substr($word, -1) === 'f') {
            return substr($word, 0, -1) . 'ves';
        }
        if (substr($word, -2) === 'fe') {
            return substr($word, 0, -2) . 'ves';
        }

        // Default: add 's'
        return $word . 's';
    }
}
