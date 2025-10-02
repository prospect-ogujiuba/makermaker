<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;
use TypeRocket\Utility\File;
use TypeRocket\Utility\Str;

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
        $variable = lcfirst($pascalCase);
        $pluralVariable = $this->pluralize($variable);
        $pluralClass = $this->toPascalCase($pluralSnakeCase);

        $this->info("Generating CRUD files for: {$pascalCase}");
        $this->info("Table name: {$pluralSnakeCase}");

        // Get namespace from config
        $appNamespace = $this->getGalaxyMakeNamespace();

        // Generate each component
        $results = [];

        try {
            $results['migration'] = $this->generateMigration($pascalCase, $pluralSnakeCase, $force);
            $results['model'] = $this->generateModel($pascalCase, $pluralSnakeCase, $appNamespace, $force);
            $results['policy'] = $this->generatePolicy($pascalCase, $snakeCase, $appNamespace, $force);
            $results['fields'] = $this->generateFields($pascalCase, $pluralSnakeCase, $appNamespace, $force);
            $results['controller'] = $this->generateController($pascalCase, $variable, $pluralVariable, $pluralSnakeCase, $appNamespace, $force);
            $results['views'] = $this->generateViews($pluralSnakeCase, $pascalCase, $pluralClass, $variable, $appNamespace, $force);

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
            $this->line('2. Run migrations: php galaxy migrate:up');
            $this->line('3. Customize the generated files as needed');
            $this->line('4. Add fields to your migration, model fillable, and form view');
        } catch (\Exception $e) {
            $this->error('Error generating CRUD: ' . $e->getMessage());
        }
    }

    protected function generateMigration($className, $tableName, $force = false)
    {
        $migrationName = "create_{$tableName}_table";
        $timestamp = time();
        $fileName = "{$timestamp}.{$migrationName}.sql";

        $root = \TypeRocket\Core\Config::get('paths.migrations');

        if (!file_exists($root)) {
            mkdir($root, 0755, true);
        }

        $migrationFile = $root . '/' . $fileName;

        if (file_exists($migrationFile) && !$force) {
            throw new \Exception("Migration file already exists: {$fileName}");
        }

        $tags = ['{{table_name}}', '{{description}}', '{{comment}}'];
        $replacements = [
            $tableName,
            "Create {$tableName} table",
            ucfirst($className) . ' table with audit fields'
        ];

        $template = $this->getTemplatePath('Migration.txt');
        $file = new File($template);
        $result = $file->copyTemplateFile($migrationFile, $tags, $replacements);

        if (!$result) {
            throw new \Exception("Failed to generate Migration");
        }

        return "database/migrations/{$fileName}";
    }

    protected function generateModel($className, $tableName, $appNamespace, $force = false)
    {
        $app_path = \TypeRocket\Core\Config::get('paths.app');
        $modelFile = $app_path . '/Models/' . $className . '.php';

        if (file_exists($modelFile) && !$force) {
            throw new \Exception("Model file already exists: {$className}.php");
        }

        $tags = ['{{namespace}}', '{{class}}', '{{table_name}}'];
        $replacements = [
            $appNamespace . '\\Models',
            $className,
            $tableName
        ];

        $template = $this->getTemplatePath('Model.txt');
        $file = new File($template);
        $result = $file->copyTemplateFile($modelFile, $tags, $replacements);

        if (!$result) {
            throw new \Exception("Failed to generate Model");
        }

        return "app/Models/{$className}.php";
    }

    protected function generatePolicy($className, $snakeCase, $appNamespace, $force = false)
    {
        $policyName = $className . 'Policy';
        $app_path = \TypeRocket\Core\Config::get('paths.app');
        $policyFile = $app_path . '/Policies/' . $policyName . '.php';

        if (file_exists($policyFile) && !$force) {
            throw new \Exception("Policy file already exists: {$policyName}.php");
        }

        // Create Policies directory if it doesn't exist
        $policiesDir = $app_path . '/Policies';
        if (!is_dir($policiesDir)) {
            mkdir($policiesDir, 0755, true);
        }

        $capability = $this->pluralize($snakeCase);

        $tags = ['{{namespace}}', '{{class}}', '{{capability}}'];
        $replacements = [
            $appNamespace . '\\Policies',
            $policyName,
            $capability
        ];

        $template = $this->getTemplatePath('Policy.txt');
        $file = new File($template);
        $result = $file->copyTemplateFile($policyFile, $tags, $replacements);

        if (!$result) {
            throw new \Exception("Failed to generate Policy");
        }

        return "app/Policies/{$policyName}.php";
    }

    protected function generateFields($className, $tableName, $appNamespace, $force = false)
    {
        $fieldsName = $className . 'Fields';
        $app_path = \TypeRocket\Core\Config::get('paths.app');
        $fieldsFile = $app_path . '/Http/Fields/' . $fieldsName . '.php';

        if (file_exists($fieldsFile) && !$force) {
            throw new \Exception("Fields file already exists: {$fieldsName}.php");
        }

        $tags = ['{{namespace}}', '{{class}}', '{{table_name}}'];
        $replacements = [
            $appNamespace . '\\Http\\Fields',
            $fieldsName,
            $tableName
        ];

        $template = $this->getTemplatePath('Fields.txt');
        $file = new File($template);
        $result = $file->copyTemplateFile($fieldsFile, $tags, $replacements);

        if (!$result) {
            throw new \Exception("Failed to generate Fields");
        }

        return "app/Http/Fields/{$fieldsName}.php";
    }

    protected function generateController($className, $variable, $pluralVariable, $viewPath, $appNamespace, $force = false)
    {
        $controllerName = $className . 'Controller';
        $app_path = \TypeRocket\Core\Config::get('paths.app');
        $controllerFile = $app_path . '/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile) && !$force) {
            throw new \Exception("Controller file already exists: {$controllerName}.php");
        }

        $routeName = $this->toSnakeCase($className);

        $tags = [
            '{{namespace}}',
            '{{class}}',
            '{{variable}}',
            '{{plural_variable}}',
            '{{view_path}}',
            '{{route_name}}',
            '{{app_namespace}}'
        ];
        $replacements = [
            $appNamespace . '\\Controllers',
            $className,
            $variable,
            $pluralVariable,
            $viewPath,
            $routeName,
            $appNamespace
        ];

        $template = $this->getTemplatePath('Controller.txt');
        $file = new File($template);
        $result = $file->copyTemplateFile($controllerFile, $tags, $replacements);

        if (!$result) {
            throw new \Exception("Failed to generate Controller");
        }

        return "app/Controllers/{$controllerName}.php";
    }

    protected function generateViews($viewPath, $className, $pluralClass, $variable, $appNamespace, $force = false)
    {
        // Use the plugin's defined view path
        $pluginViewsPath = defined('TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH')
            ? TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH
            : __DIR__ . '/../../resources/views';

        $viewsDir = "{$pluginViewsPath}/{$viewPath}";

        // Create directory if it doesn't exist
        if (!is_dir($viewsDir)) {
            mkdir($viewsDir, 0755, true);
        }

        $indexFile = "{$viewsDir}/index.php";
        $formFile = "{$viewsDir}/form.php";

        $generatedFiles = [];

        // Generate index view
        if (!file_exists($indexFile) || $force) {
            $tags = ['{{class}}', '{{app_namespace}}'];
            $replacements = [$className, $appNamespace];

            $template = $this->getTemplatePath('ViewIndex.txt');
            $file = new File($template);
            $result = $file->copyTemplateFile($indexFile, $tags, $replacements);

            if (!$result) {
                throw new \Exception("Failed to generate Index view");
            }
            $generatedFiles[] = "resources/views/{$viewPath}/index.php";
        }

        // Generate form view
        if (!file_exists($formFile) || $force) {
            $tags = [
                '{{class}}',
                '{{plural_class}}',
                '{{variable}}',
                '{{app_namespace}}'
            ];
            $replacements = [
                $className,
                $pluralClass,
                $variable,
                $appNamespace
            ];

            $template = $this->getTemplatePath('ViewForm.txt');
            $file = new File($template);
            $result = $file->copyTemplateFile($formFile, $tags, $replacements);

            if (!$result) {
                throw new \Exception("Failed to generate Form view");
            }
            $generatedFiles[] = "resources/views/{$viewPath}/form.php";
        }

        return $generatedFiles;
    }

    protected function getTemplatePath($templateName)
    {
        // Check if running from plugin context
        $pluginTemplatePath = defined('MAKERMAKER_PLUGIN_DIR')
            ? MAKERMAKER_PLUGIN_DIR . '/inc/templates/' . $templateName
            : __DIR__ . '/../../inc/templates/' . $templateName;

        if (file_exists($pluginTemplatePath)) {
            return $pluginTemplatePath;
        }

        throw new \Exception("Template file not found: {$templateName}");
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