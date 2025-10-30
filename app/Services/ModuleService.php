<?php

namespace MakerMaker\Services;

use Exception;

class ModuleService
{
    private string $modulesPath;
    private string $registryPath;
    private string $appPath;
    
    public function __construct()
    {
        $this->modulesPath = MAKERMAKER_PLUGIN_DIR . 'modules';
        $this->registryPath = MAKERMAKER_PLUGIN_DIR . 'inc/modules/registry.json';
        $this->appPath = MAKERMAKER_PLUGIN_DIR . 'app';
        
        $this->ensureRegistryExists();
    }
    
    /**
     * Get all available modules
     */
    public function getAllModules(): array
    {
        $modules = [];
        
        if (!is_dir($this->modulesPath)) {
            return $modules;
        }
        
        $dirs = glob($this->modulesPath . '/*', GLOB_ONLYDIR);
        
        foreach ($dirs as $dir) {
            $slug = basename($dir);
            $metadata = $this->getModuleMetadata($slug);
            
            if ($metadata) {
                $modules[$slug] = $metadata;
            }
        }
        
        return $modules;
    }
    
    /**
     * Get active modules from registry
     */
    public function getActiveModules(): array
    {
        $registry = $this->readRegistry();
        return $registry['active'] ?? [];
    }
    
    /**
     * Get module metadata
     */
    public function getModuleMetadata(string $slug): ?array
    {
        $metadataPath = $this->modulesPath . "/{$slug}/module.json";
        
        if (!file_exists($metadataPath)) {
            return null;
        }
        
        $metadata = json_decode(file_get_contents($metadataPath), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON in module metadata for '{$slug}'");
        }
        
        // Add active status from registry
        $activeModules = $this->getActiveModules();
        $metadata['active'] = in_array($slug, $activeModules);
        
        return $metadata;
    }
    
    /**
     * Enable a module
     */
    public function enableModule(string $slug): array
    {
        $metadata = $this->getModuleMetadata($slug);
        
        if (!$metadata) {
            throw new Exception("Module '{$slug}' not found");
        }
        
        if ($metadata['active']) {
            throw new Exception("Module '{$slug}' is already enabled");
        }
        
        // Check dependencies
        $this->checkDependencies($metadata);
        
        // Check conflicts
        $this->checkConflicts($metadata);
        
        $results = [
            'module' => $slug,
            'actions' => []
        ];
        
        // Copy files to app directories
        $results['actions']['files'] = $this->copyModuleFiles($slug, $metadata);
        
        // Run migrations
        $results['actions']['migrations'] = $this->runModuleMigrations($slug, $metadata, 'up');
        
        // Register capabilities
        if (!empty($metadata['capabilities'])) {
            $this->registerCapabilities($metadata['capabilities']);
            $results['actions']['capabilities'] = $metadata['capabilities'];
        }
        
        // Update registry
        $this->addToRegistry($slug);
        
        // Clear TypeRocket cache if it exists
        $this->clearCache();
        
        return $results;
    }
    
    /**
     * Disable a module
     */
    public function disableModule(string $slug, bool $keepData = false): array
    {
        $metadata = $this->getModuleMetadata($slug);
        
        if (!$metadata) {
            throw new Exception("Module '{$slug}' not found");
        }
        
        if (!$metadata['active']) {
            throw new Exception("Module '{$slug}' is not enabled");
        }
        
        // Check if other modules depend on this one
        $this->checkDependents($slug);
        
        $results = [
            'module' => $slug,
            'actions' => []
        ];
        
        // Remove files from app directories
        $results['actions']['files'] = $this->removeModuleFiles($slug, $metadata);
        
        // Rollback migrations unless keeping data
        if (!$keepData) {
            $results['actions']['migrations'] = $this->runModuleMigrations($slug, $metadata, 'down');
            $results['actions']['data_removed'] = true;
        } else {
            $results['actions']['data_kept'] = true;
        }
        
        // Remove capabilities
        if (!empty($metadata['capabilities'])) {
            $this->removeCapabilities($metadata['capabilities']);
            $results['actions']['capabilities_removed'] = $metadata['capabilities'];
        }
        
        // Update registry
        $this->removeFromRegistry($slug);
        
        // Clear cache
        $this->clearCache();
        
        return $results;
    }
    
    /**
     * Copy module files to app directories
     */
    private function copyModuleFiles(string $slug, array $metadata): array
    {
        $copied = [];
        $modulePath = $this->modulesPath . "/{$slug}";
        
        // Map of source directories to destination directories
        $dirMap = [
            'Controllers' => 'Controllers',
            'Models' => 'Models',
            'Auth' => 'Auth',
            'Http/Fields' => 'Http/Fields',
        ];
        
        foreach ($dirMap as $sourceDir => $destDir) {
            $source = $modulePath . '/' . $sourceDir;
            $dest = $this->appPath . '/' . $destDir;
            
            if (!is_dir($source)) {
                continue;
            }
            
            if (!is_dir($dest)) {
                mkdir($dest, 0755, true);
            }
            
            $files = glob($source . '/*.php');
            
            foreach ($files as $file) {
                $filename = basename($file);
                $destFile = $dest . '/' . $filename;
                
                if (file_exists($destFile)) {
                    throw new Exception("File conflict: {$filename} already exists in {$destDir}");
                }
                
                copy($file, $destFile);
                $copied[] = $destDir . '/' . $filename;
            }
        }
        
        // Copy resources
        $resourcesSource = $modulePath . '/resources';
        $resourcesDest = MAKERMAKER_PLUGIN_DIR . 'inc/resources';
        
        if (is_dir($resourcesSource)) {
            if (!is_dir($resourcesDest)) {
                mkdir($resourcesDest, 0755, true);
            }
            
            $resources = glob($resourcesSource . '/*.php');
            foreach ($resources as $file) {
                $filename = basename($file);
                $destFile = $resourcesDest . '/' . $filename;
                
                if (file_exists($destFile)) {
                    throw new Exception("Resource conflict: {$filename} already exists");
                }
                
                copy($file, $destFile);
                $copied[] = 'resources/' . $filename;
            }
        }
        
        // Copy views
        if (!empty($metadata['files']['views'])) {
            $viewsSource = $modulePath . '/views';
            $viewsDest = TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH;
            
            foreach ($metadata['files']['views'] as $viewDir => $viewFiles) {
                $sourceViewDir = $viewsSource . '/' . $viewDir;
                $destViewDir = $viewsDest . '/' . $viewDir;
                
                if (!is_dir($sourceViewDir)) {
                    continue;
                }
                
                if (!is_dir($destViewDir)) {
                    mkdir($destViewDir, 0755, true);
                }
                
                foreach ($viewFiles as $viewFile) {
                    $source = $sourceViewDir . '/' . $viewFile;
                    $dest = $destViewDir . '/' . $viewFile;
                    
                    if (file_exists($dest)) {
                        throw new Exception("View conflict: {$viewDir}/{$viewFile} already exists");
                    }
                    
                    copy($source, $dest);
                    $copied[] = "views/{$viewDir}/{$viewFile}";
                }
            }
        }
        
        return $copied;
    }
    
    /**
     * Remove module files from app directories
     */
    private function removeModuleFiles(string $slug, array $metadata): array
    {
        $removed = [];
        
        // Remove from app directories
        $fileTypes = ['controllers', 'models', 'policies', 'fields'];
        $dirMap = [
            'controllers' => 'Controllers',
            'models' => 'Models',
            'policies' => 'Auth',
            'fields' => 'Http/Fields',
        ];
        
        foreach ($fileTypes as $type) {
            if (empty($metadata['files'][$type])) {
                continue;
            }
            
            $dir = $this->appPath . '/' . $dirMap[$type];
            
            foreach ($metadata['files'][$type] as $filename) {
                $file = $dir . '/' . $filename;
                
                if (file_exists($file)) {
                    unlink($file);
                    $removed[] = $dirMap[$type] . '/' . $filename;
                }
            }
        }
        
        // Remove resources
        if (!empty($metadata['files']['resources'])) {
            $resourcesDir = MAKERMAKER_PLUGIN_DIR . 'inc/resources';
            
            foreach ($metadata['files']['resources'] as $filename) {
                $file = $resourcesDir . '/' . $filename;
                
                if (file_exists($file)) {
                    unlink($file);
                    $removed[] = 'resources/' . $filename;
                }
            }
        }
        
        // Remove views
        if (!empty($metadata['files']['views'])) {
            $viewsDir = TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH;
            
            foreach ($metadata['files']['views'] as $viewDir => $viewFiles) {
                foreach ($viewFiles as $viewFile) {
                    $file = $viewsDir . '/' . $viewDir . '/' . $viewFile;
                    
                    if (file_exists($file)) {
                        unlink($file);
                        $removed[] = "views/{$viewDir}/{$viewFile}";
                    }
                }
                
                // Remove empty view directory
                $dir = $viewsDir . '/' . $viewDir;
                if (is_dir($dir) && count(scandir($dir)) == 2) {
                    rmdir($dir);
                }
            }
        }
        
        return $removed;
    }
    
    /**
     * Run module migrations
     */
    private function runModuleMigrations(string $slug, array $metadata, string $direction = 'up'): array
    {
        if (empty($metadata['files']['migrations'])) {
            return [];
        }
        
        $modulePath = $this->modulesPath . "/{$slug}";
        $migrationDir = $modulePath . '/migrations';
        
        if (!is_dir($migrationDir)) {
            return [];
        }
        
        $migrations = $metadata['files']['migrations'];
        $executed = [];
        
        // Sort migrations by timestamp
        sort($migrations);
        
        if ($direction === 'down') {
            $migrations = array_reverse($migrations);
        }
        
        foreach ($migrations as $migration) {
            $file = $migrationDir . '/' . $migration;
            
            if (!file_exists($file)) {
                continue;
            }
            
            $sql = file_get_contents($file);
            
            // Extract the appropriate SQL based on direction
            if ($direction === 'up') {
                preg_match('/-- >>> Up >>>(.*?)-- >>> Down >>>/s', $sql, $matches);
            } else {
                preg_match('/-- >>> Down >>>(.*?)$/s', $sql, $matches);
            }
            
            if (!empty($matches[1])) {
                $queries = trim($matches[1]);
                
                // Replace prefix placeholder
                global $wpdb;
                $queries = str_replace('{!!prefix!!}', $wpdb->prefix, $queries);
                
                // Execute
                $wpdb->query($queries);
                
                if ($wpdb->last_error) {
                    throw new Exception("Migration error in {$migration}: " . $wpdb->last_error);
                }
                
                $executed[] = $migration;
            }
        }
        
        return $executed;
    }
    
    /**
     * Check module dependencies
     */
    private function checkDependencies(array $metadata): void
    {
        if (empty($metadata['dependencies'])) {
            return;
        }
        
        $activeModules = $this->getActiveModules();
        
        foreach ($metadata['dependencies'] as $dependency) {
            if (!in_array($dependency, $activeModules)) {
                throw new Exception("Module '{$metadata['slug']}' requires '{$dependency}' to be enabled first");
            }
        }
    }
    
    /**
     * Check module conflicts
     */
    private function checkConflicts(array $metadata): void
    {
        if (empty($metadata['conflicts'])) {
            return;
        }
        
        $activeModules = $this->getActiveModules();
        
        foreach ($metadata['conflicts'] as $conflict) {
            if (in_array($conflict, $activeModules)) {
                throw new Exception("Module '{$metadata['slug']}' conflicts with '{$conflict}'. Disable '{$conflict}' first.");
            }
        }
    }
    
    /**
     * Check if other modules depend on this one
     */
    private function checkDependents(string $slug): void
    {
        $allModules = $this->getAllModules();
        $activeModules = $this->getActiveModules();
        
        foreach ($activeModules as $activeSlug) {
            if ($activeSlug === $slug) {
                continue;
            }
            
            $metadata = $allModules[$activeSlug] ?? null;
            
            if (!$metadata) {
                continue;
            }
            
            if (!empty($metadata['dependencies']) && in_array($slug, $metadata['dependencies'])) {
                throw new Exception("Cannot disable '{$slug}' - module '{$activeSlug}' depends on it");
            }
        }
    }
    
    /**
     * Register WordPress capabilities
     */
    private function registerCapabilities(array $capabilities): void
    {
        tr_roles()->updateRolesCapabilities('administrator', $capabilities);
    }
    
    /**
     * Remove WordPress capabilities
     */
    private function removeCapabilities(array $capabilities): void
    {
        tr_roles()->removeRolesCapabilities('administrator', $capabilities);
    }
    
    /**
     * Ensure registry file exists
     */
    private function ensureRegistryExists(): void
    {
        $dir = dirname($this->registryPath);
        
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        if (!file_exists($this->registryPath)) {
            $this->writeRegistry(['active' => []]);
        }
    }
    
    /**
     * Read registry
     */
    private function readRegistry(): array
    {
        if (!file_exists($this->registryPath)) {
            return ['active' => []];
        }
        
        $content = file_get_contents($this->registryPath);
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['active' => []];
        }
        
        return $data;
    }
    
    /**
     * Write registry
     */
    private function writeRegistry(array $data): void
    {
        file_put_contents(
            $this->registryPath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
    
    /**
     * Add module to registry
     */
    private function addToRegistry(string $slug): void
    {
        $registry = $this->readRegistry();
        
        if (!in_array($slug, $registry['active'])) {
            $registry['active'][] = $slug;
            $this->writeRegistry($registry);
        }
    }
    
    /**
     * Remove module from registry
     */
    private function removeFromRegistry(string $slug): void
    {
        $registry = $this->readRegistry();
        $registry['active'] = array_values(array_diff($registry['active'], [$slug]));
        $this->writeRegistry($registry);
    }
    
    /**
     * Clear TypeRocket cache
     */
    private function clearCache(): void
    {
        // Clear opcache if available
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        
        // Clear TypeRocket cache directory if it exists
        $cacheDir = MAKERMAKER_PLUGIN_DIR . 'storage/cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        
        // Force WordPress to flush rewrite rules
        \TypeRocket\Core\System::updateSiteState('flush_rewrite_rules');
    }
}
