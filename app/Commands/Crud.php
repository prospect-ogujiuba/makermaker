<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;
use TypeRocket\Utility\File;
use TypeRocket\Utility\Str;

class Crud extends Command
{
	protected $command = [
		'make:crud {name} {?--force}',
		'Generate a complete CRUD setup with Model, Controller, Policy, Fields, Views, and Resource',
		'This command creates all necessary files for a CRUD operation including Model, Controller, Policy, Fields, basic view templates, and resource registration.',
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
		$pluralSnakeCase = pluralize($snakeCase);
		$variable = lcfirst($pascalCase);
		$pluralVariable = pluralize($variable);
		$pluralClass = $this->toPascalCase($pluralSnakeCase);
		$pluralTitle = ucwords(str_replace('_', ' ', $pluralSnakeCase));

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

			// Generate resource file
			$results['resource'] = $this->generateResourceFile($pascalCase, $variable, $snakeCase, $pluralTitle, $force);

			// Update MakermakerTypeRocketPlugin.php
			$this->updatePluginFile($snakeCase, $pascalCase);

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
			$this->error('Error: ' . $e->getMessage());
		}
	}

	/**
	 * Generate Resource File
	 */
	protected function generateResourceFile($className, $variable, $snakeCase, $pluralTitle, $force = false)
    {
        $resourceFile = MAKERMAKER_PLUGIN_DIR . 'inc/resources/' . $snakeCase . '.php';

        if (file_exists($resourceFile) && !$force) {
            throw new \Exception("Resource file already exists: {$snakeCase}.php");
        }

        // Create resources directory if it doesn't exist
        $resourcesDir = MAKERMAKER_PLUGIN_DIR . 'inc/resources';
        if (!is_dir($resourcesDir)) {
            mkdir($resourcesDir, 0755, true);
        }

        // Generate plural snake case for capabilities
        $pluralSnakeCase = pluralize($snakeCase);

        $tags = ['{{class}}', '{{singular}}', '{{variable}}', '{{plural_title}}', '{{plural_snake}}'];
        $replacements = [$className, $snakeCase, $variable, $pluralTitle, $pluralSnakeCase];

        // Use the resource template
        $template = $this->getTemplatePath('Resource.txt');
        $file = new File($template);
        $result = $file->copyTemplateFile($resourceFile, $tags, $replacements);

        if (!$result) {
            throw new \Exception("Failed to generate Resource file");
        }

        return "inc/resources/{$snakeCase}.php";
    }

	/**
	 * Update MakermakerTypeRocketPlugin.php file
	 */
	protected function updatePluginFile($snakeCase, $pascalCase)
	{
		$pluginFile = MAKERMAKER_PLUGIN_DIR . 'app/MakermakerTypeRocketPlugin.php';

		if (!file_exists($pluginFile)) {
			throw new \Exception("Plugin file not found: MakermakerTypeRocketPlugin.php");
		}

		$content = file_get_contents($pluginFile);

		// 1. Add resource to $resources array
		$resourcesPattern = '/\$resources\s*=\s*\[\s*([^]]*)\];/';
		if (preg_match($resourcesPattern, $content, $matches)) {
			$currentResources = $matches[1];

			// Check if resource already exists
			if (strpos($currentResources, "'{$snakeCase}'") === false) {
				// Add new resource to array
				$newResources = rtrim($currentResources);
				if (!empty(trim($newResources))) {
					$newResources .= ",\n            ";
				}
				$newResources .= "'{$snakeCase}'";

				$newResourcesArray = '$resources = [' . "\n            " . trim($newResources) . "\n        ];";
				$content = preg_replace($resourcesPattern, $newResourcesArray, $content);

				$this->success("Added '{$snakeCase}' to resources array");
			} else {
				$this->warning("Resource '{$snakeCase}' already exists in resources array");
			}
		}

		// 2. Add policy association - specifically in the policies() method
		// Look for the policies() function and its return statement
		$policiesPattern = '/(public\s+function\s+policies\(\)\s*\{[^}]*?return\s+\[)([^]]*?)(\];)/s';

		if (preg_match($policiesPattern, $content, $matches)) {
			$beforeReturn = $matches[1];
			$currentPolicies = $matches[2];
			$afterReturn = $matches[3];

			$modelClass = '\\MakerMaker\\Models\\' . $pascalCase;
			$policyClass = '\\MakerMaker\\Auth\\' . $pascalCase . 'Policy';

			// Check if policy already exists
			if (strpos($currentPolicies, $modelClass) === false) {
				// Add new policy to array
				$policiesArray = trim($currentPolicies);
				if (!empty($policiesArray) && !str_ends_with(trim($policiesArray), ',')) {
					$policiesArray .= ",";
				}
				if (!empty($policiesArray)) {
					$policiesArray .= "\n            ";
				}
				$policiesArray .= "'{$modelClass}' => '{$policyClass}',";

				// Rebuild the policies method part
				$newPoliciesSection = $beforeReturn . "\n            " . $policiesArray . "\n        " . $afterReturn;

				// Replace only the policies() method
				$content = preg_replace($policiesPattern, $newPoliciesSection, $content, 1);

				$this->success("Added policy association for {$pascalCase}");
			} else {
				$this->warning("Policy association for {$pascalCase} already exists");
			}
		} else {
			$this->error("Could not find policies() method in plugin file");
		}

		// Save the modified content
		file_put_contents($pluginFile, $content);
		$this->success("Updated MakermakerTypeRocketPlugin.php");
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
			ucfirst($className) . ' table'
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
		$policyFile = $app_path . '/Auth/' . $policyName . '.php';

		if (file_exists($policyFile) && !$force) {
			throw new \Exception("Policy file already exists: {$policyName}.php");
		}

		// Create Auth directory if it doesn't exist
		$authDir = $app_path . '/Auth';
		if (!is_dir($authDir)) {
			mkdir($authDir, 0755, true);
		}

		$capability = pluralize($snakeCase);

		$tags = ['{{namespace}}', '{{class}}', '{{capability}}'];
		$replacements = [
			$appNamespace . '\\Auth',
			$policyName,
			$capability
		];

		$template = $this->getTemplatePath('Policy.txt');
		$file = new File($template);
		$result = $file->copyTemplateFile($policyFile, $tags, $replacements);

		if (!$result) {
			throw new \Exception("Failed to generate Policy");
		}

		return "app/Auth/{$policyName}.php";
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
}
