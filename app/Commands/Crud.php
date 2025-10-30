<?php

namespace MakerMaker\Commands;

use TypeRocket\Console\Command;
use TypeRocket\Utility\File;

class Crud extends Command
{
	protected $command = [
		'make:crud {name} {?--module=} {?--template=standard} {?--force}',
		'Generate a complete CRUD setup with Model, Controller, Policy, Fields, Views, and Resource',
		'Options: --module=shop, --template=simple|standard|api-ready',
	];

	public function exec()
	{
		$name = $this->getArgument('name');
		$module = $this->getOption('module');
		$template = $this->getOption('template') ?: 'standard';
		$force = $this->getOption('force');

		if (!$name) {
			$this->error('Name argument is required');
			return;
		}

		// Validate template option
		$validTemplates = ['simple', 'standard', 'api-ready'];
		if (!in_array($template, $validTemplates)) {
			$this->error("Invalid template: {$template}. Valid options: " . implode(', ', $validTemplates));
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

		$titleCase = toTitleCase($pascalCase);
		$pluralTitleCase = toTitleCase($pluralClass);

		$this->info("Generating CRUD files for: {$pascalCase}");
		$this->info("Template variant: {$template}");
		$this->info("Table name: {$pluralSnakeCase}");

		// Determine base paths based on module flag
		if ($module) {
			$base_path = MAKERMAKER_PLUGIN_DIR . "modules/{$module}";
			$this->info("Module: {$module}");
			$this->ensureModuleStructure($base_path, $module);
			$appNamespace = 'MakerMaker\\Modules\\' . $this->toPascalCase($module);
		} else {
			$base_path = MAKERMAKER_PLUGIN_DIR . 'app';
			$appNamespace = $this->getGalaxyMakeNamespace();
		}

		// Generate each component
		$results = [];

		try {
			$results['migration'] = $this->generateMigration($pascalCase, $pluralSnakeCase, $force, $template);
			$results['model'] = $this->generateModel($pascalCase, $pluralSnakeCase, $appNamespace, $base_path, $force, $template);
			$results['policy'] = $this->generatePolicy($pascalCase, $snakeCase, $appNamespace, $base_path, $force, $template);
			$results['fields'] = $this->generateFields($pascalCase, $pluralSnakeCase, $appNamespace, $base_path, $force, $template);
			$results['controller'] = $this->generateController($pascalCase, $variable, $pluralVariable, $pluralSnakeCase, $appNamespace, $base_path, $force, $template);
			$results['views'] = $this->generateViews($pluralSnakeCase, $pascalCase, $pluralClass, $variable, $titleCase, $pluralTitleCase, $appNamespace, $module, $force, $template);

			// Generate resource file
			$results['resource'] = $this->generateResourceFile($pascalCase, $variable, $snakeCase, $pluralTitle, $module, $force, $template);

			// Update plugin file or module metadata
			if ($module) {
				$this->updateModuleMetadata($module, $pascalCase, $snakeCase);
			} else {
				$this->updatePluginFile($snakeCase, $pascalCase);
			}

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
			$this->info('Template variant: ' . $template);
			if ($module) {
				$this->info('Module: ' . $module);
			}
			$this->line('');
			$this->info('Next steps:');
			$this->line('1. Run migrations: php galaxy migrate:up');
			$this->line('2. Customize the generated files as needed');
			$this->line('3. Add fields to your migration, model fillable, and form view');

			if ($template === 'api-ready') {
				$this->line('4. Configure REST routes in your routes file');
			}
		} catch (\Exception $e) {
			$this->error('Error: ' . $e->getMessage());
		}
	}

	/**
	 * Ensure module directory structure exists
	 */
	protected function ensureModuleStructure($path, $module)
	{
		$dirs = ['Controllers', 'Models', 'Auth', 'Http/Fields', 'resources'];
		foreach ($dirs as $dir) {
			$full_path = "{$path}/{$dir}";
			if (!is_dir($full_path)) {
				mkdir($full_path, 0755, true);
				$this->info("Created directory: modules/{$module}/{$dir}");
			}
		}
	}

	/**
	 * Update module metadata file
	 */
	protected function updateModuleMetadata($module, $pascalCase, $snakeCase, array $results = [])
	{
		$metadataFile = MAKERMAKER_PLUGIN_DIR . "modules/{$module}/module.json";

		// Load existing metadata or create new
		if (file_exists($metadataFile)) {
			$metadata = json_decode(file_get_contents($metadataFile), true);
		} else {
			// Create default metadata structure
			$metadata = [
				'name' => ucwords(str_replace(['-', '_'], ' ', $module)),
				'slug' => $module,
				'namespace' => 'MakerMaker\\Modules\\' . $this->toPascalCase($module),
				'description' => 'Module description',
				'version' => '1.0.0',
				'author' => get_bloginfo('name') ?: 'Your Name',
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
						'MakerMaker\\Modules\\' . $this->toPascalCase($module) . '\\' => '.'
					]
				],
				'capabilities' => []
			];
		}

		// Add files from generation results
		foreach ($results as $type => $files) {
			if (!isset($metadata['files'][$type])) {
				continue;
			}

			if (is_array($files)) {
				foreach ($files as $file) {
					$filename = basename($file);
					if (!in_array($filename, $metadata['files'][$type])) {
						$metadata['files'][$type][] = $filename;
					}
				}
			} else {
				$filename = basename($files);
				if (!in_array($filename, $metadata['files'][$type])) {
					$metadata['files'][$type][] = $filename;
				}
			}
		}

		// Add controller
		$controllerFile = "{$pascalCase}Controller.php";
		if (!in_array($controllerFile, $metadata['files']['controllers'])) {
			$metadata['files']['controllers'][] = $controllerFile;
		}

		// Add model
		$modelFile = "{$pascalCase}.php";
		if (!in_array($modelFile, $metadata['files']['models'])) {
			$metadata['files']['models'][] = $modelFile;
		}

		// Add policy
		$policyFile = "{$pascalCase}Policy.php";
		if (!in_array($policyFile, $metadata['files']['policies'])) {
			$metadata['files']['policies'][] = $policyFile;
		}

		// Add fields
		$fieldsFile = "{$pascalCase}Fields.php";
		if (!in_array($fieldsFile, $metadata['files']['fields'])) {
			$metadata['files']['fields'][] = $fieldsFile;
		}

		// Add resource
		$resourceFile = "{$snakeCase}.php";
		if (!in_array($resourceFile, $metadata['files']['resources'])) {
			$metadata['files']['resources'][] = $resourceFile;
		}

		// Add capability
		$capability = 'manage_' . pluralize($snakeCase);
		if (!in_array($capability, $metadata['capabilities'])) {
			$metadata['capabilities'][] = $capability;
		}

		// Save updated metadata
		file_put_contents(
			$metadataFile,
			json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
		);

		$this->success("Updated module metadata: modules/{$module}/module.json");
	}

	/**
	 * Generate Resource File
	 */
	protected function generateResourceFile($className, $variable, $snakeCase, $pluralTitle, $module = null, $force = false, $template = 'standard')
	{
		if ($module) {
			$resourceFile = MAKERMAKER_PLUGIN_DIR . "modules/{$module}/resources/{$snakeCase}.php";
			$resourcesDir = MAKERMAKER_PLUGIN_DIR . "modules/{$module}/resources";
		} else {
			$resourceFile = MAKERMAKER_PLUGIN_DIR . 'inc/resources/' . $snakeCase . '.php';
			$resourcesDir = MAKERMAKER_PLUGIN_DIR . 'inc/resources';
		}

		if (file_exists($resourceFile) && !$force) {
			throw new \Exception("Resource file already exists: {$snakeCase}.php");
		}

		// Create resources directory if it doesn't exist
		if (!is_dir($resourcesDir)) {
			mkdir($resourcesDir, 0755, true);
		}

		// Generate plural snake case for capabilities
		$pluralSnakeCase = pluralize($snakeCase);

		$tags = ['{{class}}', '{{singular}}', '{{variable}}', '{{plural_title}}', '{{plural_snake}}'];
		$replacements = [$className, $snakeCase, $variable, $pluralTitle, $pluralSnakeCase];

		// Use the resource template
		$templatePath = $this->getTemplatePath('Resource.txt', $template);
		$file = new File($templatePath);
		$result = $file->copyTemplateFile($resourceFile, $tags, $replacements);

		if (!$result) {
			throw new \Exception("Failed to generate Resource file");
		}

		return $module
			? "modules/{$module}/resources/{$snakeCase}.php"
			: "inc/resources/{$snakeCase}.php";
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
				// Trim and prepare the resources array content
				$resourcesArray = trim($currentResources);

				// Add comma if content exists and doesn't already end with a comma
				if (!empty($resourcesArray) && !str_ends_with(trim($resourcesArray), ',')) {
					$resourcesArray .= ",";
				}

				// Add newline and indentation if there's existing content
				if (!empty($resourcesArray)) {
					$resourcesArray .= "\n            ";
				}

				// Add the new resource
				$resourcesArray .= "'{$snakeCase}'";

				// Rebuild the complete resources assignment
				$newResourcesArray = '$resources = [' . "\n            " . $resourcesArray . "\n        ];";
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

	protected function generateMigration($className, $tableName, $force = false, $template = 'standard')
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

		$templatePath = $this->getTemplatePath('Migration.txt', $template);
		$file = new File($templatePath);
		$result = $file->copyTemplateFile($migrationFile, $tags, $replacements);

		if (!$result) {
			throw new \Exception("Failed to generate Migration");
		}

		return "database/migrations/{$fileName}";
	}

	protected function generateModel($className, $tableName, $appNamespace, $basePath, $force = false, $template = 'standard')
	{
		$modelsDir = $basePath . '/Models';
		if (!is_dir($modelsDir)) {
			mkdir($modelsDir, 0755, true);
		}

		$modelFile = $modelsDir . '/' . $className . '.php';

		if (file_exists($modelFile) && !$force) {
			throw new \Exception("Model file already exists: {$className}.php");
		}

		$tags = ['{{namespace}}', '{{class}}', '{{table_name}}'];
		$replacements = [
			$appNamespace . '\\Models',
			$className,
			$tableName
		];

		$templatePath = $this->getTemplatePath('Model.txt', $template);
		$file = new File($templatePath);
		$result = $file->copyTemplateFile($modelFile, $tags, $replacements);

		if (!$result) {
			throw new \Exception("Failed to generate Model");
		}

		$relativePath = str_replace(MAKERMAKER_PLUGIN_DIR, '', $modelFile);
		return ltrim($relativePath, '/');
	}

	protected function generatePolicy($className, $snakeCase, $appNamespace, $basePath, $force = false, $template = 'standard')
	{
		$policyName = $className . 'Policy';
		$authDir = $basePath . '/Auth';

		if (!is_dir($authDir)) {
			mkdir($authDir, 0755, true);
		}

		$policyFile = $authDir . '/' . $policyName . '.php';

		if (file_exists($policyFile) && !$force) {
			throw new \Exception("Policy file already exists: {$policyName}.php");
		}

		$capability = pluralize($snakeCase);

		$tags = ['{{namespace}}', '{{class}}', '{{capability}}'];
		$replacements = [
			$appNamespace . '\\Auth',
			$policyName,
			$capability
		];

		$templatePath = $this->getTemplatePath('Policy.txt', $template);
		$file = new File($templatePath);
		$result = $file->copyTemplateFile($policyFile, $tags, $replacements);

		if (!$result) {
			throw new \Exception("Failed to generate Policy");
		}

		$relativePath = str_replace(MAKERMAKER_PLUGIN_DIR, '', $policyFile);
		return ltrim($relativePath, '/');
	}

	protected function generateFields($className, $tableName, $appNamespace, $basePath, $force = false, $template = 'standard')
	{
		$fieldsName = $className . 'Fields';
		$fieldsDir = $basePath . '/Http/Fields';

		if (!is_dir($fieldsDir)) {
			mkdir($fieldsDir, 0755, true);
		}

		$fieldsFile = $fieldsDir . '/' . $fieldsName . '.php';

		if (file_exists($fieldsFile) && !$force) {
			throw new \Exception("Fields file already exists: {$fieldsName}.php");
		}

		$tags = ['{{namespace}}', '{{class}}', '{{table_name}}'];
		$replacements = [
			$appNamespace . '\\Http\\Fields',
			$fieldsName,
			$tableName
		];

		$templatePath = $this->getTemplatePath('Fields.txt', $template);
		$file = new File($templatePath);
		$result = $file->copyTemplateFile($fieldsFile, $tags, $replacements);

		if (!$result) {
			throw new \Exception("Failed to generate Fields");
		}

		$relativePath = str_replace(MAKERMAKER_PLUGIN_DIR, '', $fieldsFile);
		return ltrim($relativePath, '/');
	}

	protected function generateController($className, $variable, $pluralVariable, $viewPath, $appNamespace, $basePath, $force = false, $template = 'standard')
	{
		$controllerName = $className . 'Controller';
		$controllersDir = $basePath . '/Controllers';

		if (!is_dir($controllersDir)) {
			mkdir($controllersDir, 0755, true);
		}

		$controllerFile = $controllersDir . '/' . $controllerName . '.php';

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

		$templatePath = $this->getTemplatePath('Controller.txt', $template);
		$file = new File($templatePath);
		$result = $file->copyTemplateFile($controllerFile, $tags, $replacements);

		if (!$result) {
			throw new \Exception("Failed to generate Controller");
		}

		$relativePath = str_replace(MAKERMAKER_PLUGIN_DIR, '', $controllerFile);
		return ltrim($relativePath, '/');
	}

	protected function generateViews($viewPath, $className, $pluralClass, $variable, $titleCase, $pluralTitleCase, $appNamespace, $module = null, $force = false, $template = 'standard')
	{
		// Determine views directory based on module
		if ($module) {
			$viewsDir = MAKERMAKER_PLUGIN_DIR . "modules/{$module}/resources/views/{$viewPath}";
		} else {
			$pluginViewsPath = defined('TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH')
				? TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH
				: MAKERMAKER_PLUGIN_DIR . '/resources/views';
			$viewsDir = "{$pluginViewsPath}/{$viewPath}";
		}

		// Create directory if it doesn't exist
		if (!is_dir($viewsDir)) {
			mkdir($viewsDir, 0755, true);
		}

		$indexFile = "{$viewsDir}/index.php";
		$formFile = "{$viewsDir}/form.php";

		$generatedFiles = [];

		// Generate index view
		if (!file_exists($indexFile) || $force) {
			$tags = ['{{class}}', '{{app_namespace}}', '{{title_class}}'];
			$replacements = [$className, $appNamespace, $titleCase];

			$templatePath = $this->getTemplatePath('ViewIndex.txt', $template);
			$file = new File($templatePath);
			$result = $file->copyTemplateFile($indexFile, $tags, $replacements);

			if (!$result) {
				throw new \Exception("Failed to generate Index view");
			}

			$relativePath = str_replace(MAKERMAKER_PLUGIN_DIR, '', $indexFile);
			$generatedFiles[] = ltrim($relativePath, '/');
		}

		// Generate form view
		if (!file_exists($formFile) || $force) {
			$tags = [
				'{{class}}',
				'{{plural_class}}',
				'{{variable}}',
				'{{app_namespace}}',
				'{{title_class}}',
				'{{title_plural}}'
			];
			$replacements = [
				$className,
				$pluralClass,
				$variable,
				$appNamespace,
				$titleCase,
				$pluralTitleCase
			];

			$templatePath = $this->getTemplatePath('ViewForm.txt', $template);
			$file = new File($templatePath);
			$result = $file->copyTemplateFile($formFile, $tags, $replacements);

			if (!$result) {
				throw new \Exception("Failed to generate Form view");
			}

			$relativePath = str_replace(MAKERMAKER_PLUGIN_DIR, '', $formFile);
			$generatedFiles[] = ltrim($relativePath, '/');
		}

		return $generatedFiles;
	}

	protected function getTemplatePath($templateName, $variant = 'standard')
	{
		$template_path = MAKERMAKER_PLUGIN_DIR . "inc/templates/{$variant}/{$templateName}";

		if (!file_exists($template_path)) {
			// Fallback to standard
			$template_path = MAKERMAKER_PLUGIN_DIR . "inc/templates/standard/{$templateName}";
		}

		if (!file_exists($template_path)) {
			throw new \Exception("Template not found: {$templateName}");
		}

		return $template_path;
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
