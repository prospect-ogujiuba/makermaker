<?php

/**
 * Helpers.
 *
 * @package makermaker
 */

function outputSelectOptions($options, $currentValue, $valueKey = null, $labelKey = null)
{
    foreach ($options as $key => $option) {
        if (is_array($option)) {
            $value = $option[$valueKey];
            $label = $option[$labelKey];
        } else {
            $value = is_numeric($key) ? $option : $key;
            $label = $option;
        }
        $selected = ($currentValue === $value) ? 'selected' : '';
        echo "<option value=\"" . htmlspecialchars($value) . "\" {$selected}>" .
            htmlspecialchars($label) . "</option>";
    }
}

function renderAdvancedSearchActions($resource)
{ ?>
    <div class="tr-search-actions">
        <div>
            <a href="<?php echo strtok($_SERVER["REQUEST_URI"], ' ? '); ?>?page=<?= $resource ?>_index" class="button">Reset Filters</a>
            <button type="submit" class="button">Search</button>
        </div>
    </div>

    <input type="checkbox" id="search-toggle" class="search-toggle-input">
    <label for="search-toggle" class="button">Toggle Advanced Search</label>
<?php
}

// Helper function to create service resources
function createServiceResource($resourceKey, $controller, $title, $hasAddButton = true)
{
    $resourcePage = tr_resource_pages(
        $resourceKey . '@\MakerMaker\Controllers\\' . $controller,
        $title
    );

    if ($hasAddButton) {
        $adminPageSlug = strtolower($resourceKey) . '_add';
        $resourcePage->addNewButton(admin_url('admin.php?page=' . $adminPageSlug));
    }

    return $resourcePage;
}


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
    
    // Add filters if configured
    if (isset($overrides['filters'])) {
        $table->addSearchFormFilter(function() use ($overrides) {
            tr_render_table_filters($overrides['filters']);
        });
        
        $table->addSearchModelFilter(function($args, $model, $table) use ($overrides) {
            tr_apply_table_filters($overrides['filters'], $model);
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
    $casts = property_exists($model, 'cast') ? $model->cast : [];
    $formats = property_exists($model, 'format') ? $model->format : [];
    
    // Get static index configuration if it exists
    $staticIndex = property_exists($modelClass, 'index') ? $modelClass::$index : [];
    
    // Use fillable fields as base, or common fields if fillable is empty
    $baseFields = !empty($fillable) ? $fillable : ['id', 'name', 'title', 'created_at'];
    
    foreach ($baseFields as $field) {
        if (isset($staticIndex[$field]) && $staticIndex[$field] === false) {
            continue; // Skip explicitly hidden fields
        }
        
        $column = [
            'label' => tr_labelizer($field),
            'sort' => !str_contains($field, '.'), // No sorting on relationship fields
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
    if (!empty($overrides)) {
        return $overrides;
    }
    
    $model = new $modelClass;
    $relationships = [];
    $methods = get_class_methods($model);
    
    foreach ($methods as $method) {
        // Skip magic methods and getters
        if (str_starts_with($method, '__') || str_starts_with($method, 'get')) {
            continue;
        }
        
        // Check for relationship methods by trying to call them
        try {
            $reflection = new ReflectionMethod($model, $method);
            if ($reflection->isPublic() && $reflection->getNumberOfRequiredParameters() === 0) {
                // Try to detect relationships by method naming patterns
                if (preg_match('/^[a-z][a-zA-Z]*$/', $method)) {
                    // belongsTo relationship - show related field
                    $relationships[$method . '.name'] = [
                        'label' => tr_labelizer($method),
                        'sort' => false,
                    ];
                    
                    // hasMany relationship - show count
                    $relationships[$method . '_count'] = [
                        'label' => tr_labelizer($method) . ' Count',
                        'sort' => false,
                        'callback' => function($value, $item) use ($method) {
                            $relation = $item->$method();
                            return $relation ? $relation->count() : 0;
                        }
                    ];
                }
            }
        } catch (Exception $e) {
            // Skip methods that can't be reflected or called
            continue;
        }
    }
    
    return $relationships;
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
 * Render filter form elements (used in addSearchFormFilter)
 * 
 * @param array $filters
 */
function tr_render_table_filters(array $filters): void 
{
    echo '<div class="tr-table-filters" style="margin: 10px 0; display: flex; gap: 15px; flex-wrap: wrap;">';
    
    foreach ($filters as $field => $config) {
        $type = $config['type'] ?? 'text';
        $label = $config['label'] ?? tr_labelizer($field);
        $value = $_GET[$field] ?? '';
        
        echo '<div class="tr-filter-group">';
        echo "<label>{$label}:</label>";
        
        switch ($type) {
            case 'select':
                echo "<select name=\"{$field}\" class=\"tr-filter\">";
                echo '<option value="">All</option>';
                
                $options = $config['options'] ?? [];
                if (is_string($options) && str_starts_with($options, 'fromRelation:')) {
                    // Handle relation-based options
                    $relation = substr($options, 13);
                    // Implementation would need actual model context
                    echo '<option value="">-- Relation options --</option>';
                } else {
                    foreach ($options as $optValue => $optLabel) {
                        $selected = $value == $optValue ? 'selected' : '';
                        echo "<option value=\"{$optValue}\" {$selected}>{$optLabel}</option>";
                    }
                }
                echo '</select>';
                break;
                
            case 'bool':
                echo "<select name=\"{$field}\" class=\"tr-filter\">";
                echo '<option value="">All</option>';
                echo '<option value="1"' . ($value === '1' ? ' selected' : '') . '>Yes</option>';
                echo '<option value="0"' . ($value === '0' ? ' selected' : '') . '>No</option>';
                echo '</select>';
                break;
                
            case 'date':
                echo "<input type=\"date\" name=\"{$field}\" class=\"tr-filter\" value=\"{$value}\">";
                break;
                
            case 'date-range':
                echo '<div class="tr-date-inputs">';
                echo "<input type=\"date\" name=\"{$field}_from\" class=\"tr-filter\" value=\"" . ($_GET[$field . '_from'] ?? '') . "\" placeholder=\"From\">";
                echo "<input type=\"date\" name=\"{$field}_to\" class=\"tr-filter\" value=\"" . ($_GET[$field . '_to'] ?? '') . "\" placeholder=\"To\">";
                echo '</div>';
                break;
                
            case 'number':
                echo "<input type=\"number\" name=\"{$field}\" class=\"tr-filter\" value=\"{$value}\" placeholder=\"{$label}\">";
                break;
                
            default: // text
                echo "<input type=\"text\" name=\"{$field}\" class=\"tr-filter\" value=\"{$value}\" placeholder=\"Search {$label}\">";
                break;
        }
        
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Apply filters to model query (used in addSearchModelFilter)
 * 
 * @param array $filters
 * @param \TypeRocket\Models\Model $model
 */
function tr_apply_table_filters(array $filters, $model): void 
{
    foreach ($filters as $field => $config) {
        $type = $config['type'] ?? 'text';
        $value = $_GET[$field] ?? '';
        
        if (empty($value)) continue;
        
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
                $model->where($field, $operator, $value);
                break;
                
            default: // text
                $model->where($field, 'LIKE', "%{$value}%");
                break;
        }
    }
}