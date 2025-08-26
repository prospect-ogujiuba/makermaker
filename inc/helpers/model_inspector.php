<?php

/**
 * TypeRocket Model Inspector
 * 
 * A comprehensive set of functions to extract all information from TypeRocket models
 * for creating smart index pages with minimal configuration.
 */

class TR_ModelInspector
{
    protected $model;
    protected $reflectionClass;
    
    public function __construct($model)
    {
        $this->model = $model;
        $this->reflectionClass = new ReflectionClass($model);
    }

    /**
     * Get comprehensive model information for smart index pages
     */
    public function getModelInfo(): array
    {
        return [
            'basic' => $this->getBasicInfo(),
            'properties' => $this->getProperties(),
            'fillable' => $this->getFillableFields(),
            'relationships' => $this->getRelationships(),
            'columns' => $this->getTableColumns(),
            'searchable' => $this->getSearchableFields(),
            'sortable' => $this->getSortableFields(),
            'filterable' => $this->getFilterableFields(),
            'display' => $this->getDisplayConfig(),
            'meta' => $this->getMetaInfo(),
        ];
    }

    /**
     * Extract basic model information
     */
    protected function getBasicInfo(): array
    {
        $className = $this->reflectionClass->getShortName();
        
        return [
            'class_name' => $className,
            'full_class_name' => get_class($this->model),
            'table_name' => $this->model->getTable(),
            'resource' => $this->getProperty('resource'),
            'id_column' => $this->model->getIdColumn(),
            'primary_key' => $this->getProperty('primary', 'id'),
            'timestamps' => $this->hasTimestamps(),
            'soft_deletes' => $this->hasSoftDeletes(),
        ];
    }

    /**
     * Get all model properties and their configurations
     */
    protected function getProperties(): array
    {
        $properties = [];
        $reflectionProperties = $this->reflectionClass->getProperties(ReflectionProperty::IS_PROTECTED);
        
        foreach ($reflectionProperties as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            
            // Skip internal properties
            if (in_array($name, ['query', 'relationships', 'errors', 'composer'])) {
                continue;
            }
            
            $properties[$name] = $property->getValue($this->model);
        }
        
        return $properties;
    }

    /**
     * Extract fillable fields with their configurations
     */
    protected function getFillableFields(): array
    {
        $fillable = $this->getProperty('fillable', []);
        $guarded = $this->getProperty('guard', []);
        $builtin = $this->getProperty('builtin', []);
        $metaless = $this->getProperty('metaless', []);
        
        return [
            'fillable' => $fillable,
            'guarded' => $guarded,
            'builtin' => $builtin,
            'metaless' => $metaless,
            'cast' => $this->getProperty('cast', []),
            'format' => $this->getProperty('format', []),
            'static' => $this->getProperty('static', []),
        ];
    }

    /**
     * Discover relationships by analyzing model methods
     */
    protected function getRelationships(): array
    {
        $relationships = [];
        $methods = $this->reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        
        foreach ($methods as $method) {
            if ($method->class !== get_class($this->model)) {
                continue;
            }
            
            $methodName = $method->getName();
            
            // Skip obvious non-relationship methods
            if (in_array($methodName, ['__construct', '__toString', 'toArray', 'toJson']) ||
                str_starts_with($methodName, 'get') ||
                str_starts_with($methodName, 'set') ||
                str_starts_with($methodName, 'is')) {
                continue;
            }
            
            try {
                // Try to execute the method to see if it returns a relationship
                $result = $method->invoke($this->model);
                
                if ($this->isRelationshipResult($result)) {
                    $relationships[$methodName] = $this->analyzeRelationship($result, $methodName);
                }
            } catch (Exception $e) {
                // Method might need parameters or have other requirements
                // Try to infer from method source code
                $relationshipInfo = $this->inferRelationshipFromMethod($method);
                if ($relationshipInfo) {
                    $relationships[$methodName] = $relationshipInfo;
                }
            }
        }
        
        return $relationships;
    }

    /**
     * Get table columns by inspecting database
     */
    protected function getTableColumns(): array
    {
        global $wpdb;
        
        $tableName = $this->model->getTable();
        $columns = [];
        
        try {
            $results = $wpdb->get_results("SHOW COLUMNS FROM `{$tableName}`");
            
            foreach ($results as $column) {
                $columns[$column->Field] = [
                    'name' => $column->Field,
                    'type' => $column->Type,
                    'null' => $column->Null === 'YES',
                    'key' => $column->Key,
                    'default' => $column->Default,
                    'extra' => $column->Extra,
                    'is_primary' => $column->Key === 'PRI',
                    'is_auto_increment' => str_contains($column->Extra, 'auto_increment'),
                    'php_type' => $this->mapSqlTypeToPhp($column->Type),
                ];
            }
        } catch (Exception $e) {
            // Fallback to basic inspection
            $columns = $this->getBasicColumnInfo();
        }
        
        return $columns;
    }

    /**
     * Determine searchable fields based on fillable, builtin, and column types
     */
    protected function getSearchableFields(): array
    {
        $columns = $this->getTableColumns();
        $fillable = $this->getProperty('fillable', []);
        $builtin = $this->getProperty('builtin', []);
        $searchable = [];
        
        $searchableTypes = ['varchar', 'text', 'longtext', 'mediumtext', 'tinytext'];
        
        foreach ($columns as $name => $info) {
            $isTextType = false;
            foreach ($searchableTypes as $type) {
                if (str_contains(strtolower($info['type']), $type)) {
                    $isTextType = true;
                    break;
                }
            }
            
            if ($isTextType) {
                $searchable[$name] = [
                    'column' => $name,
                    'type' => 'text',
                    'searchable' => true,
                    'weight' => $this->getSearchWeight($name),
                ];
            }
        }
        
        return $searchable;
    }

    /**
     * Determine sortable fields
     */
    protected function getSortableFields(): array
    {
        $columns = $this->getTableColumns();
        $sortable = [];
        
        $sortableTypes = ['int', 'bigint', 'decimal', 'float', 'double', 'datetime', 'timestamp', 'date', 'varchar'];
        
        foreach ($columns as $name => $info) {
            $isSortableType = false;
            foreach ($sortableTypes as $type) {
                if (str_contains(strtolower($info['type']), $type)) {
                    $isSortableType = true;
                    break;
                }
            }
            
            if ($isSortableType) {
                $sortable[$name] = [
                    'column' => $name,
                    'type' => $info['php_type'],
                    'sortable' => true,
                    'default_direction' => $this->getDefaultSortDirection($name, $info['type']),
                ];
            }
        }
        
        return $sortable;
    }

    /**
     * Determine filterable fields and their filter types
     */
    protected function getFilterableFields(): array
    {
        $columns = $this->getTableColumns();
        $relationships = $this->getRelationships();
        $filterable = [];
        
        foreach ($columns as $name => $info) {
            $filterType = $this->determineFilterType($info['type'], $name);
            if ($filterType) {
                $filterable[$name] = [
                    'column' => $name,
                    'type' => $filterType,
                    'options' => $this->getFilterOptions($name, $filterType),
                ];
            }
        }
        
        // Add relationship filters
        foreach ($relationships as $name => $info) {
            if ($info['type'] === 'belongsTo' || $info['type'] === 'hasOne') {
                $filterable[$name] = [
                    'column' => $name,
                    'type' => 'select',
                    'options' => 'relationship',
                    'relationship' => $info,
                ];
            }
        }
        
        return $filterable;
    }

    /**
     * Get display configuration for index pages
     */
    protected function getDisplayConfig(): array
    {
        $columns = $this->getTableColumns();
        $relationships = $this->getRelationships();
        $displayColumns = [];
        
        // Primary display column (usually name, title, or first text column)
        $primaryColumn = $this->findPrimaryDisplayColumn($columns);
        
        // Build display columns with smart defaults
        foreach ($columns as $name => $info) {
            if ($this->shouldDisplayColumn($name, $info)) {
                $displayColumns[$name] = [
                    'label' => $this->generateColumnLabel($name),
                    'type' => $info['php_type'],
                    'sortable' => in_array($name, array_keys($this->getSortableFields())),
                    'searchable' => in_array($name, array_keys($this->getSearchableFields())),
                    'primary' => $name === $primaryColumn,
                    'format' => $this->suggestColumnFormat($name, $info),
                ];
            }
        }
        
        // Add relationship display columns
        foreach ($relationships as $name => $info) {
            if ($info['display_in_index']) {
                $displayColumns[$name] = [
                    'label' => $this->generateColumnLabel($name),
                    'type' => 'relationship',
                    'relationship' => $info,
                    'sortable' => false,
                    'searchable' => false,
                ];
            }
        }
        
        return [
            'columns' => $displayColumns,
            'primary_column' => $primaryColumn,
            'actions' => $this->getDefaultActions(),
            'per_page_options' => [10, 25, 50, 100],
            'default_per_page' => 25,
        ];
    }

    /**
     * Get meta information for additional context
     */
    protected function getMetaInfo(): array
    {
        return [
            'has_wp_integration' => $this->hasWordPressIntegration(),
            'custom_post_type' => $this->getCustomPostType(),
            'taxonomy_support' => $this->getTaxonomySupport(),
            'meta_fields' => $this->getMetaFieldsConfig(),
            'permissions' => $this->getPermissionConfig(),
        ];
    }

    /**
     * Helper methods
     */

    protected function getProperty(string $property, $default = null)
    {
        try {
            $reflection = $this->reflectionClass->getProperty($property);
            $reflection->setAccessible(true);
            return $reflection->getValue($this->model) ?? $default;
        } catch (ReflectionException $e) {
            return $default;
        }
    }

    protected function isRelationshipResult($result): bool
    {
        // Check if result is a model query or model instance that indicates a relationship
        return $result instanceof \TypeRocket\Models\Model || 
               $result instanceof \TypeRocket\Database\Results ||
               (is_object($result) && method_exists($result, 'getRelatedBy'));
    }

    protected function analyzeRelationship($result, string $methodName): array
    {
        $info = [
            'method' => $methodName,
            'type' => 'unknown',
            'model' => null,
            'foreign_key' => null,
            'local_key' => null,
            'display_in_index' => $this->shouldDisplayRelationshipInIndex($methodName),
        ];
        
        if (method_exists($result, 'getRelatedBy')) {
            $relatedBy = $result->getRelatedBy();
            if (isset($relatedBy['type'])) {
                $info['type'] = $relatedBy['type'];
                $info['model'] = get_class($result);
                
                if (isset($relatedBy['query'])) {
                    $query = $relatedBy['query'];
                    $info['foreign_key'] = $query['id_foreign'] ?? null;
                    $info['local_key'] = $query['id_local'] ?? null;
                }
            }
        }
        
        return $info;
    }

    protected function inferRelationshipFromMethod(ReflectionMethod $method): ?array
    {
        $methodName = $method->getName();
        
        // Common relationship patterns
        if (str_ends_with($methodName, 's') && !in_array($methodName, ['class', 'exists'])) {
            return [
                'method' => $methodName,
                'type' => 'hasMany',
                'inferred' => true,
                'display_in_index' => false,
            ];
        }
        
        return null;
    }

    protected function mapSqlTypeToPhp(string $sqlType): string
    {
        $type = strtolower(explode('(', $sqlType)[0]);
        
        return match($type) {
            'int', 'bigint', 'smallint', 'tinyint', 'mediumint' => 'integer',
            'decimal', 'float', 'double' => 'float',
            'varchar', 'char', 'text', 'longtext', 'mediumtext', 'tinytext' => 'string',
            'datetime', 'timestamp', 'date', 'time' => 'datetime',
            'json' => 'array',
            'boolean', 'bool' => 'boolean',
            default => 'string'
        };
    }

    protected function getSearchWeight(string $columnName): int
    {
        // Higher weight for more important fields
        $highWeight = ['name', 'title', 'subject', 'headline'];
        $mediumWeight = ['description', 'content', 'body', 'text'];
        
        foreach ($highWeight as $field) {
            if (str_contains(strtolower($columnName), $field)) {
                return 10;
            }
        }
        
        foreach ($mediumWeight as $field) {
            if (str_contains(strtolower($columnName), $field)) {
                return 5;
            }
        }
        
        return 1;
    }

    protected function getDefaultSortDirection(string $columnName, string $type): string
    {
        // Dates and IDs usually sort DESC (newest first)
        if (str_contains($columnName, 'created') || 
            str_contains($columnName, 'updated') || 
            str_contains($columnName, 'date') ||
            $columnName === 'id') {
            return 'DESC';
        }
        
        return 'ASC';
    }

    protected function determineFilterType(string $sqlType, string $columnName): ?string
    {
        $type = strtolower(explode('(', $sqlType)[0]);
        
        // Boolean fields
        if (str_contains($columnName, 'is_') || 
            str_contains($columnName, 'has_') ||
            in_array($type, ['boolean', 'bool', 'tinyint(1)'])) {
            return 'boolean';
        }
        
        // Date fields
        if (in_array($type, ['date', 'datetime', 'timestamp'])) {
            return 'date_range';
        }
        
        // Numeric fields
        if (in_array($type, ['int', 'bigint', 'decimal', 'float', 'double'])) {
            return 'number_range';
        }
        
        // Status or type fields
        if (str_contains($columnName, 'status') || 
            str_contains($columnName, 'type') ||
            str_contains($columnName, 'category')) {
            return 'select';
        }
        
        return null;
    }

    protected function getFilterOptions(string $columnName, string $filterType): array
    {
        if ($filterType === 'boolean') {
            return [
                '1' => 'Yes',
                '0' => 'No'
            ];
        }
        
        if ($filterType === 'select') {
            // Would need to query database for actual options
            return 'dynamic'; // Indicates options should be loaded dynamically
        }
        
        return [];
    }

    protected function findPrimaryDisplayColumn(array $columns): string
    {
        $priorities = ['name', 'title', 'subject', 'headline', 'label'];
        
        foreach ($priorities as $priority) {
            if (isset($columns[$priority])) {
                return $priority;
            }
        }
        
        // Fallback to first text column
        foreach ($columns as $name => $info) {
            if ($info['php_type'] === 'string' && $name !== 'id') {
                return $name;
            }
        }
        
        return 'id';
    }

    protected function shouldDisplayColumn(string $name, array $info): bool
    {
        // Skip certain columns
        $skipColumns = ['password', 'token', 'secret', 'hash'];
        
        foreach ($skipColumns as $skip) {
            if (str_contains(strtolower($name), $skip)) {
                return false;
            }
        }
        
        // Skip very long text fields in index
        if (str_contains(strtolower($info['type']), 'longtext')) {
            return false;
        }
        
        return true;
    }

    protected function shouldDisplayRelationshipInIndex(string $methodName): bool
    {
        // Only show belongsTo relationships by default
        $showInIndex = ['user', 'author', 'category', 'type', 'status', 'parent'];
        
        foreach ($showInIndex as $show) {
            if (str_contains(strtolower($methodName), $show)) {
                return true;
            }
        }
        
        return false;
    }

    protected function generateColumnLabel(string $columnName): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $columnName));
    }

    protected function suggestColumnFormat(string $name, array $info): ?string
    {
        // Suggest formatting based on column name and type
        if (str_contains($name, 'email')) return 'email';
        if (str_contains($name, 'url') || str_contains($name, 'link')) return 'url';
        if (str_contains($name, 'phone')) return 'phone';
        if (str_contains($name, 'price') || str_contains($name, 'cost')) return 'currency';
        if (str_contains($name, 'date')) return 'date';
        if (str_contains($name, 'image') || str_contains($name, 'photo')) return 'image';
        if ($info['php_type'] === 'boolean') return 'boolean';
        
        return null;
    }

    protected function getDefaultActions(): array
    {
        return ['view', 'edit', 'delete'];
    }

    protected function hasTimestamps(): bool
    {
        $columns = $this->getTableColumns();
        return isset($columns['created_at']) && isset($columns['updated_at']);
    }

    protected function hasSoftDeletes(): bool
    {
        $columns = $this->getTableColumns();
        return isset($columns['deleted_at']);
    }

    protected function hasWordPressIntegration(): bool
    {
        return !empty($this->getProperty('builtin'));
    }

    protected function getCustomPostType(): ?string
    {
        $resource = $this->getProperty('resource');
        if ($resource && $this->hasWordPressIntegration()) {
            return $resource;
        }
        return null;
    }

    protected function getTaxonomySupport(): array
    {
        // This would need to be determined based on model configuration
        return [];
    }

    protected function getMetaFieldsConfig(): array
    {
        return [
            'metaless' => $this->getProperty('metaless', []),
            'builtin' => $this->getProperty('builtin', []),
        ];
    }

    protected function getPermissionConfig(): array
    {
        return [
            'can_create' => method_exists($this->model, 'can') ? true : null,
            'can_edit' => method_exists($this->model, 'can') ? true : null,
            'can_delete' => method_exists($this->model, 'can') ? true : null,
            'can_view' => method_exists($this->model, 'can') ? true : null,
        ];
    }

    protected function getBasicColumnInfo(): array
    {
        // Basic fallback when database inspection fails
        return [
            'id' => [
                'name' => 'id',
                'type' => 'int(11)',
                'php_type' => 'integer',
                'is_primary' => true,
                'is_auto_increment' => true,
            ]
        ];
    }
}

/**
 * Helper functions for easy access
 */

/**
 * Get comprehensive model information
 */
function tr_model_info($model): array
{
    $inspector = new TR_ModelInspector($model);
    return $inspector->getModelInfo();
}

/**
 * Create smart table configuration from model
 */
function tr_smart_table($model, array $overrides = []): array
{
    $modelInfo = tr_model_info($model);
    
    $config = [
        'columns' => [],
        'searchable' => [],
        'sortable' => [],
        'filterable' => [],
        'actions' => $modelInfo['display']['actions'],
        'per_page' => $modelInfo['display']['default_per_page'],
    ];
    
    // Build columns for table
    foreach ($modelInfo['display']['columns'] as $name => $column) {
        $config['columns'][$name] = [
            'label' => $column['label'],
            'sort' => $column['sortable'],
            'actions' => $column['primary'] ? $config['actions'] : false,
        ];
        
        if ($column['searchable']) {
            $config['searchable'][] = $name;
        }
    }
    
    // Merge with any overrides
    return array_merge_recursive($config, $overrides);
}

/**
 * Generate smart filters for model
 */
function tr_smart_filters($model): array
{
    $modelInfo = tr_model_info($model);
    $filters = [];
    
    foreach ($modelInfo['filterable'] as $name => $config) {
        $filters[$name] = [
            'type' => $config['type'],
            'label' => ucwords(str_replace('_', ' ', $name)),
            'options' => $config['options'],
        ];
    }
    
    return $filters;
}

/**
 * Get model relationships for forms and displays
 */
function tr_model_relationships($model): array
{
    $modelInfo = tr_model_info($model);
    return $modelInfo['relationships'];
}

/**
 * Get fillable fields with metadata
 */
function tr_model_fillable($model): array
{
    $modelInfo = tr_model_info($model);
    return $modelInfo['fillable'];
}