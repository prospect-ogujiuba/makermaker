<?php
/**
 * TypeRocket Table Helper Functions
 * 
 * Drop-in PHP helpers for eliminating repetitive code in TypeRocket admin index pages.
 * Provides reflection-based defaults with override capability.
 */

/**
 * Primary entrypoint - configures and returns a TypeRocket table instance
 * 
 * @param string $modelClass Fully qualified model class name
 * @param array $overrides Override configuration
 * @return \TypeRocket\Pro\Elements\Table
 * @throws Exception
 */
function tr_index_setup(string $modelClass, array $overrides = []) 
{
    if (!class_exists($modelClass)) {
        throw new Exception("Model class {$modelClass} does not exist");
    }

    // Create table instance
    $table = tr_table($modelClass);
    
    // Get inferred configuration
    $columns = tr_infer_columns($modelClass, $overrides['columns'] ?? []);
    $searchColumns = tr_infer_search_columns($modelClass, $overrides['search'] ?? []);
    $relationships = tr_infer_relationships($modelClass, $overrides['relationships'] ?? []);
    $actions = tr_infer_actions($modelClass, $overrides['actions'] ?? []);
    $filters = isset($overrides['filters']) ? $overrides['filters'] : tr_infer_filters($modelClass);
    
    // Merge relationship columns into main columns
    $columns = array_merge($columns, $relationships);
    
    // Configure table
    $table->setColumns($columns, $overrides['primary'] ?? null);
    
    if (!empty($searchColumns)) {
        $table->setSearchColumns($searchColumns);
    }
    
    // Set ordering if specified
    if (isset($overrides['order'])) {
        foreach ($overrides['order'] as $column => $direction) {
            $table->setOrder($column, $direction);
            break; // TypeRocket only supports one order at a time
        }
    }
    
    // Set up bulk actions
    if (isset($actions['bulk']) && !empty($actions['bulk'])) {
        $form = tr_form();
        $table->setBulkActions($form, $actions['bulk']);
    }
    
    // Add filters with renderAdvancedSearchActions integration
    if (!empty($filters)) {
        $resourceName = $overrides['resource_name'] ?? tr_infer_resource_name($modelClass);
        
        $table->addSearchFormFilter(function() use ($filters, $resourceName, $modelClass) {
            renderAdvancedSearchActions($resourceName);
            tr_render_advanced_filters($modelClass, $filters);
        });
        
        $table->addSearchModelFilter(function($args, $model, $table) use ($filters) {
            tr_apply_advanced_filters($filters, $model);
        });
    }
    
    return $table;
}

/**
 * Infer table columns from model reflection
 * 
 * @param string $modelClass
 * @param array $overrides
 * @return array
 */
function tr_infer_columns(string $modelClass, array $overrides = []): array 
{
    $model = new $modelClass;
    $columns = [];
    
    // Start with fillable fields or all model properties
    $fillable = $model->getFillableFields();
    $casts = property_exists($model, 'cast') ? ($model->cast ?? []) : [];
    $formats = property_exists($model, 'format') ? ($model->format ?? []) : [];
    
    // Get static index configuration if it exists
    $staticIndex = property_exists($modelClass, 'index') ? ($modelClass::$index ?? []) : [];
    
    // Use fillable fields as base, or common fields if fillable is empty
    $baseFields = !empty($fillable) ? $fillable : ['id'];
    
    // Only add basic table fields - no relationship fields by default
    foreach ($baseFields as $field) {
        // Skip relationship notation fields in base inference
        if (str_contains($field, '.')) {
            continue;
        }
        
        if (isset($staticIndex[$field]) && $staticIndex[$field] === false) {
            continue; // Skip explicitly hidden fields
        }
        
        $column = [
            'label' => tr_labelizer($field),
            'sort' => true, // All basic fields can be sorted
        ];
        
        // Add formatter based on cast or field name patterns
        $formatter = tr_infer_formatter($field, $casts, $formats);
        if ($formatter) {
            $column['callback'] = $formatter;
        }
        
        // Add actions to primary column (usually first fillable field)
        if ($field === reset($baseFields)) {
            $column['actions'] = ['edit', 'view', 'delete'];
        }
        
        $columns[$field] = $column;
    }
    
    // Merge with static configuration and overrides
    if (!empty($staticIndex)) {
        $columns = array_merge($columns, $staticIndex);
    }
    
    return array_merge($columns, $overrides);
}

/**
 * Infer search columns from model
 * 
 * @param string $modelClass
 * @param array $overrides
 * @return array
 */
function tr_infer_search_columns(string $modelClass, array $overrides = []): array 
{
    if (!empty($overrides)) {
        return $overrides;
    }
    
    $model = new $modelClass;
    $fillable = $model->getFillableFields();
    $searchable = [];
    
    // Common searchable field patterns
    $searchablePatterns = ['name', 'title', 'email', 'firstname', 'lastname', 'description', 'content'];
    
    foreach ($fillable as $field) {
        foreach ($searchablePatterns as $pattern) {
            if (str_contains(strtolower($field), $pattern)) {
                $searchable[] = $field;
                break;
            }
        }
    }
    
    return !empty($searchable) ? $searchable : ['id'];
}

/**
 * Infer relationship columns from model methods
 * 
 * @param string $modelClass
 * @param array $overrides
 * @return array
 */
function tr_infer_relationships(string $modelClass, array $overrides = []): array 
{
    // For now, return empty array to avoid relationship inference issues
    // Users should explicitly define relationship columns in overrides
    return $overrides;
}

/**
 * Infer table actions (bulk and row actions)
 * 
 * @param string $modelClass
 * @param array $overrides
 * @return array
 */
function tr_infer_actions(string $modelClass, array $overrides = []): array 
{
    $defaults = [
        'bulk' => [
            'Delete Selected' => 'delete',
        ],
        'row' => [
            'Edit' => 'edit',
            'View' => 'view',
            'Delete' => 'delete',
        ]
    ];
    
    return array_merge_recursive($defaults, $overrides);
}

/**
 * Convert field names to human-readable labels
 * 
 * @param string $field
 * @return string
 */
function tr_labelizer(string $field): string 
{
    // Handle relationship notation
    if (str_contains($field, '.')) {
        $parts = explode('.', $field);
        $field = end($parts);
    }
    
    // Remove _count suffix for relationship counts
    $field = preg_replace('/_count$/', '', $field);
    
    // Convert snake_case and camelCase to Title Case
    $field = str_replace('_', ' ', $field);
    $field = preg_replace('/([a-z])([A-Z])/', '$1 $2', $field);
    
    return ucwords($field);
}

/**
 * Infer appropriate formatter for a field
 * 
 * @param string $field
 * @param array $casts
 * @param array $formats
 * @return callable|null
 */
function tr_infer_formatter(string $field, array $casts = [], array $formats = []): ?callable 
{
    // Check for explicit format configuration
    if (isset($formats[$field])) {
        return $formats[$field];
    }
    
    // Infer from cast type
    if (isset($casts[$field])) {
        switch ($casts[$field]) {
            case 'bool':
            case 'boolean':
                return function($value) { return tr_bool_badge($value); };
            case 'datetime':
            case 'date':
                return function($value) { return tr_date_formatter($value); };
        }
    }
    
    // Infer from field name patterns
    $lowerField = strtolower($field);
    
    if (str_contains($lowerField, 'email')) {
        return function($value) {
            return $value ? "<a href=\"mailto:{$value}\">{$value}</a>" : 'N/A';
        };
    }
    
    if (str_contains($lowerField, 'price') || str_contains($lowerField, 'amount') || str_contains($lowerField, 'cost')) {
        return function($value) { return tr_money_formatter($value); };
    }
    
    if (preg_match('/(is_|has_|can_|active|enabled|published)/', $lowerField)) {
        return function($value) { return tr_bool_badge($value); };
    }
    
    if (str_contains($lowerField, 'created_at') || str_contains($lowerField, 'updated_at') || str_ends_with($lowerField, '_date')) {
        return function($value) { return tr_date_formatter($value); };
    }
    
    return null;
}

/**
 * Format boolean values as badges
 * 
 * @param mixed $value
 * @return string
 */
function tr_bool_badge($value): string 
{
    $isTrue = in_array($value, [1, '1', true, 'true', 'yes', 'on'], true);
    $class = $isTrue ? 'tr-badge-success' : 'tr-badge-default';
    $text = $isTrue ? 'Yes' : 'No';
    
    return "<span class=\"tr-badge {$class}\">{$text}</span>";
}

/**
 * Format money values
 * 
 * @param mixed $value
 * @return string
 */
function tr_money_formatter($value): string 
{
    if (!is_numeric($value)) return 'N/A';
    return '$' . number_format((float)$value, 2);
}

/**
 * Format date values
 * 
 * @param mixed $value
 * @return string
 */
function tr_date_formatter($value): string 
{
    if (!$value || $value === '0000-00-00 00:00:00') return 'N/A';
    
    try {
        $date = new DateTime($value);
        return $date->format('M j, Y g:i A');
    } catch (Exception $e) {
        return 'Invalid Date';
    }
}

/**
 * Add these functions to your index_table.php file after the existing functions
 */

/**
 * Infer filters from model fields, relationships, and patterns
 * 
 * @param string $modelClass
 * @param array $overrides
 * @return array
 */
function tr_infer_filters(string $modelClass, array $overrides = []): array 
{
    if (!empty($overrides)) {
        return $overrides;
    }
    
    $model = new $modelClass;
    $filters = [];
    $fillable = $model->getFillableFields();
    $casts = property_exists($model, 'cast') ? ($model->cast ?? []) : [];
    
    foreach ($fillable as $field) {
        // Skip certain fields that shouldn't be filtered
        if (in_array($field, ['password', 'created_at', 'updated_at']) || str_contains($field, '.')) {
            continue;
        }
        
        $filterType = tr_infer_filter_type($field, $casts);
        if ($filterType) {
            $filter = ['type' => $filterType];
            
            // Add predefined options for common fields
            if ($filterType === 'select') {
                $options = tr_get_field_options($field);
                if ($options) {
                    $filter['options'] = $options;
                }
            }
            
            $filters[$field] = $filter;
        }
    }
    
    return $filters;
}

/**
 * Infer the appropriate filter type for a field
 * 
 * @param string $field
 * @param array $casts
 * @return string|null
 */
function tr_infer_filter_type(string $field, array $casts = []): ?string 
{
    $lowerField = strtolower($field);
    
    // Check cast types
    if (isset($casts[$field])) {
        switch ($casts[$field]) {
            case 'bool':
            case 'boolean':
                return 'bool';
            case 'date':
            case 'datetime':
                return 'date-range';
            case 'int':
            case 'integer':
            case 'float':
            case 'decimal':
                return 'number';
        }
    }
    
    // Common select field patterns
    if (str_ends_with($field, '_id') || 
        in_array($field, ['status', 'type', 'category', 'gender', 'province']) ||
        str_contains($lowerField, 'status') || 
        str_contains($lowerField, 'type') ||
        str_contains($lowerField, 'immigration')) {
        return 'select';
    }
    
    // Boolean patterns
    if (preg_match('/^(is_|has_|can_|active|enabled|published)/', $lowerField)) {
        return 'bool';
    }
    
    // Date patterns
    if (str_ends_with($lowerField, '_date') || str_ends_with($lowerField, '_at')) {
        return 'date-range';
    }
    
    // Number patterns
    if (str_contains($lowerField, 'age') || 
        str_contains($lowerField, 'count') || 
        str_contains($lowerField, 'price') ||
        str_contains($lowerField, 'amount')) {
        return 'number';
    }
    
    // Default to text for searchable fields
    if (str_contains($lowerField, 'name') || 
        str_contains($lowerField, 'email') || 
        str_contains($lowerField, 'phone') ||
        str_contains($lowerField, 'address') ||
        str_contains($lowerField, 'city') ||
        str_contains($lowerField, 'reference')) {
        return 'text';
    }
    
    return null;
}

/**
 * Get predefined options for common select fields
 * 
 * @param string $field
 * @return array|null
 */
function tr_get_field_options(string $field): ?array 
{
    $lowerField = strtolower($field);
    
    // Common field options based on patterns from the index.php
    switch (true) {
        case $field === 'gender':
            return ['M' => 'Male', 'F' => 'Female', 'Other' => 'Other', 'Prefer Not To Say' => 'Prefer Not To Say'];
            
        case $field === 'province':
            return ['ON' => 'Ontario', 'BC' => 'British Columbia', 'AB' => 'Alberta', 
                   'SK' => 'Saskatchewan', 'MB' => 'Manitoba', 'QC' => 'Quebec', 
                   'NB' => 'New Brunswick', 'NS' => 'Nova Scotia', 'PE' => 'Prince Edward Island', 
                   'NL' => 'Newfoundland and Labrador', 'YT' => 'Yukon', 'NT' => 'Northwest Territories', 'NU' => 'Nunavut'];
                   
        case str_contains($lowerField, 'dl_type') || str_contains($lowerField, 'license_type'):
            return ['G' => 'G', 'G1' => 'G1', 'G2' => 'G2', 'Other' => 'Other'];
            
        case str_contains($lowerField, 'dl_status') || str_contains($lowerField, 'license_status'):
            return ['Unlicensed' => 'Unlicensed', 'Active' => 'Active', 'Suspended' => 'Suspended', 'Other' => 'Other'];
            
        case str_contains($lowerField, 'immigration'):
            return ['Student' => 'Student', 'Work Permit' => 'Work Permit', 'Study Permit' => 'Study Permit',
                   'Temporary Resident' => 'Temporary Resident', 'Permanent Resident' => 'Permanent Resident',
                   'Visitor' => 'Visitor', 'Refugee' => 'Refugee', 'Other' => 'Other'];
                   
        case str_contains($lowerField, 'work') && !str_contains($lowerField, 'permit'):
            return ['N' => 'None', 'F' => 'Full-time', 'PT' => 'Part-time', 'Other' => 'Other'];
            
        default:
            return null;
    }
}

/**
 * Infer resource name from model class for renderAdvancedSearchActions
 * 
 * @param string $modelClass
 * @return string
 */
function tr_infer_resource_name(string $modelClass): string 
{
    // Extract class name from fully qualified class name
    $parts = explode('\\', $modelClass);
    $className = end($parts);
    return strtolower($className);
}

/**
 * Render advanced filter form using the structure from index.php
 * 
 * @param string $modelClass
 * @param array $filters
 */
function tr_render_advanced_filters(string $modelClass, array $filters): void 
{
    echo '<div class="tr-search-filters">';
    
    foreach ($filters as $field => $config) {
        tr_render_filter_field($field, $config, $modelClass);
    }
    
    echo '</div>';
}

/**
 * Render a single filter field with proper error handling
 * 
 * @param string $field
 * @param array $config
 * @param string $modelClass
 */
function tr_render_filter_field(string $field, array $config, string $modelClass): void 
{
    $type = $config['type'] ?? 'text';
    $label = $config['label'] ?? tr_labelizer($field);
    $value = $_GET[$field] ?? '';
    
    echo '<div class="tr-filter-group">';
    echo "<label>{$label}:</label>";
    
    try {
        switch ($type) {
            case 'select':
                tr_render_select_filter($field, $config, $value, $modelClass);
                break;
                
            case 'bool':
                tr_render_bool_filter($field, $value);
                break;
                
            case 'date':
                echo "<input type=\"date\" name=\"{$field}\" class=\"tr-filter\" value=\"{$value}\">";
                break;
                
            case 'date-range':
                tr_render_date_range_filter($field);
                break;
                
            case 'number':
                echo "<input type=\"number\" name=\"{$field}\" class=\"tr-filter\" value=\"{$value}\" placeholder=\"{$label}\">";
                break;
                
            default: // text
                echo "<input type=\"text\" name=\"{$field}\" class=\"tr-filter\" value=\"{$value}\" placeholder=\"Search {$label}\">";
                break;
        }
    } catch (Exception $e) {
        // Fallback to text input if filter rendering fails
        echo "<input type=\"text\" name=\"{$field}\" class=\"tr-filter\" value=\"{$value}\" placeholder=\"Search {$label}\">";
    }
    
    echo '</div>';
}

/**
 * Render select filter with relationship support and error handling
 * 
 * @param string $field
 * @param array $config
 * @param string $value
 * @param string $modelClass
 */
function tr_render_select_filter(string $field, array $config, string $value, string $modelClass): void 
{
    echo "<select name=\"{$field}\" class=\"tr-filter\">";
    echo '<option value="">All</option>';
    
    $options = $config['options'] ?? [];
    
    if (is_string($options) && str_starts_with($options, 'fromRelation:')) {
        // Handle relationship-based options with error handling
        try {
            $relation = substr($options, 13);
            $relationOptions = tr_get_relation_options($modelClass, $relation);
            foreach ($relationOptions as $optValue => $optLabel) {
                $selected = $value == $optValue ? 'selected' : '';
                echo "<option value=\"" . htmlspecialchars($optValue) . "\" {$selected}>" . 
                     htmlspecialchars($optLabel) . "</option>";
            }
        } catch (Exception $e) {
            echo '<option value="">-- Error loading options --</option>';
        }
    } else {
        // Static options
        foreach ($options as $optValue => $optLabel) {
            $selected = $value == $optValue ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($optValue) . "\" {$selected}>" . 
                 htmlspecialchars($optLabel) . "</option>";
        }
    }
    
    echo '</select>';
}

/**
 * Render boolean filter
 * 
 * @param string $field
 * @param string $value
 */
function tr_render_bool_filter(string $field, string $value): void 
{
    echo "<select name=\"{$field}\" class=\"tr-filter\">";
    echo '<option value="">All</option>';
    echo '<option value="1"' . ($value === '1' ? ' selected' : '') . '>Yes</option>';
    echo '<option value="0"' . ($value === '0' ? ' selected' : '') . '>No</option>';
    echo '</select>';
}

/**
 * Render date range filter
 * 
 * @param string $field
 */
function tr_render_date_range_filter(string $field): void 
{
    echo '<div class="tr-date-inputs">';
    echo "<input type=\"date\" name=\"{$field}_from\" class=\"tr-filter\" value=\"" . 
         ($_GET[$field . '_from'] ?? '') . "\" placeholder=\"From\">";
    echo "<input type=\"date\" name=\"{$field}_to\" class=\"tr-filter\" value=\"" . 
         ($_GET[$field . '_to'] ?? '') . "\" placeholder=\"To\">";
    echo '</div>';
}

/**
 * Get options from a model relationship with error handling
 * 
 * @param string $modelClass
 * @param string $relation
 * @return array
 */
function tr_get_relation_options(string $modelClass, string $relation): array 
{
    try {
        $model = new $modelClass;
        
        // Try to get the relationship method
        if (method_exists($model, $relation)) {
            $relationQuery = $model->$relation();
            
            if ($relationQuery) {
                $results = $relationQuery->get();
                $options = [];
                
                foreach ($results as $result) {
                    $id = $result->getID();
                    $name = $result->name ?? $result->title ?? $result->code ?? "ID: {$id}";
                    $options[$id] = $name;
                }
                
                return $options;
            }
        }
    } catch (Exception $e) {
        // Return empty options on error
    }
    
    return [];
}

/**
 * Apply advanced filters with enhanced error handling and relationship support
 * 
 * @param array $filters
 * @param \TypeRocket\Models\Model $model
 */
function tr_apply_advanced_filters(array $filters, $model): void 
{
    foreach ($filters as $field => $config) {
        $type = $config['type'] ?? 'text';
        $value = $_GET[$field] ?? '';
        
        if (empty($value) && $value !== '0') continue;
        
        try {
            switch ($type) {
                case 'select':
                case 'bool':
                    $model->where($field, '=', $value);
                    break;
                    
                case 'date':
                    $model->where($field, '>=', $value . ' 00:00:00');
                    break;
                    
                case 'date-range':
                    if (!empty($_GET[$field . '_from'])) {
                        $model->where($field, '>=', $_GET[$field . '_from'] . ' 00:00:00');
                    }
                    if (!empty($_GET[$field . '_to'])) {
                        $model->where($field, '<=', $_GET[$field . '_to'] . ' 23:59:59');
                    }
                    break;
                    
                case 'number':
                    $operator = $_GET[$field . '_operator'] ?? '=';
                    if (in_array($operator, ['=', '<', '>', '<=', '>='])) {
                        $model->where($field, $operator, $value);
                    }
                    break;
                    
                default: // text
                    $model->where($field, 'LIKE', "%{$value}%");
                    break;
            }
        } catch (Exception $e) {
            // Skip filter if there's an error applying it
            continue;
        }
    }
}