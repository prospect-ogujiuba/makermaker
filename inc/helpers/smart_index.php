<?php

/**
 * Smart Index System - Refactored
 * 
 * Provides configurable index pages with minimal setup while maintaining flexibility.
 * Leverages model_inspector.php for metadata and supports extensive customization.
 */

/**
 * Main entry point - Smart Index with configuration support
 * 
 * @param string $modelClass Fully qualified model class name
 * @param array $config Configuration overrides
 * @return void
 */
function tr_smart_index(string $modelClass, array $config = []): void {
    $model = new $modelClass();
    $modelInfo = tr_model_info($model);
    $table = tr_table($model);
    
    // Apply smart configuration
    tr_configure_smart_index($table, $modelClass, $modelInfo, $config);
    
    echo $table;
}

/**
 * Configure the table with smart defaults and user overrides
 * 
 * @param object $table TypeRocket table instance
 * @param string $modelClass Model class name
 * @param array $modelInfo Model metadata from inspector
 * @param array $config User configuration
 * @return void
 */
function tr_configure_smart_index($table, string $modelClass, array $modelInfo, array $config): void {
    // 1. Configure columns
    tr_smart_configure_columns($table, $modelInfo, $config['columns'] ?? []);
    
    // 2. Configure search
    // tr_smart_configure_search($table, $modelInfo, $config['search_columns'] ?? []);
    
    // 3. Configure filters
    tr_smart_configure_filters($table, $modelClass, $modelInfo, $config['filters'] ?? []);
    
    // 4. Configure bulk actions
    tr_smart_configure_bulk_actions($table, $config['bulk_actions'] ?? []);
    
    // 5. Configure sorting
    tr_smart_configure_sorting($table, $config['order'] ?? []);
    
    // 6. Apply any direct table method calls
    tr_smart_apply_table_methods($table, $config);
}

/**
 * COLUMN CONFIGURATION HELPER
 */
function tr_smart_configure_columns($table, array $modelInfo, array $columnOverrides): void {
    $columns = [];
    $primaryColumn = null;
    
    // Start with model info columns
    foreach ($modelInfo['display']['columns'] as $name => $config) {
        $column = [
            'label' => $config['label'],
            'sort' => $config['sortable'],
        ];
        
        // Add formatter if available
        if (isset($config['formatter'])) {
            $column['callback'] = $config['formatter'];
        }
        
        // Add actions to primary column
        if ($config['primary']) {
            $column['actions'] = ['edit', 'view', 'delete'];
            $primaryColumn = $name;
        }
        
        $columns[$name] = $column;
    }
    
    // Apply column overrides
    if (!empty($columnOverrides)) {
        foreach ($columnOverrides as $name => $columnConfig) {
            if ($columnConfig === false) {
                // Remove column
                unset($columns[$name]);
                continue;
            }
            
            if (is_string($columnConfig)) {
                // Simple label override
                if (isset($columns[$name])) {
                    $columns[$name]['label'] = $columnConfig;
                } else {
                    $columns[$name] = ['label' => $columnConfig, 'sort' => true];
                }
            } elseif (is_array($columnConfig)) {
                // Full column configuration
                if (isset($columns[$name])) {
                    $columns[$name] = array_merge($columns[$name], $columnConfig);
                } else {
                    $columns[$name] = $columnConfig;
                }
                
                // Handle formatter
                if (isset($columnConfig['formatter'])) {
                    $columns[$name]['callback'] = $columnConfig['formatter'];
                }
            }
        }
    }
    
    $table->setColumns($columns, $primaryColumn);
}

/**
 * SEARCH CONFIGURATION HELPER
 */
function tr_smart_configure_search($table, array $modelInfo, array $searchOverrides): void {
    $searchColumns = [];
    
    if (!empty($searchOverrides)) {
        // Use provided search columns
        $searchColumns = $searchOverrides;
    } else {
        // Auto-detect from model info
        foreach ($modelInfo['searchable'] as $field => $config) {
            $searchColumns[$field] = $config['label'] ?? tr_humanize_field_name($field);
        }
    }
    
    if (!empty($searchColumns)) {
        $table->setSearchColumns($searchColumns);
    }
}

/**
 * BULK ACTIONS CONFIGURATION HELPER
 */
function tr_smart_configure_bulk_actions($table, array $bulkOverrides): void {
    if (!empty($bulkOverrides)) {
        $form = tr_form()->useConfirm();
        $table->setBulkActions($form, $bulkOverrides);
    }
}

/**
 * SORTING CONFIGURATION HELPER
 */
function tr_smart_configure_sorting($table, array $orderConfig): void {
    if (!empty($orderConfig)) {
        foreach ($orderConfig as $column => $direction) {
            $table->setOrder($column, $direction);
            break; // TypeRocket only supports one order at a time
        }
    }
}

/**
 * DIRECT TABLE METHOD CALLS HELPER
 */
function tr_smart_apply_table_methods($table, array $config): void {
    $reservedKeys = ['columns', 'search_columns', 'filters', 'bulk_actions', 'order'];
    
    foreach ($config as $method => $params) {
        if (in_array($method, $reservedKeys)) {
            continue;
        }
        
        if (method_exists($table, $method)) {
            if (is_array($params)) {
                call_user_func_array([$table, $method], $params);
            } else {
                $table->$method($params);
            }
        }
    }
}

/**
 * ENHANCED FILTER CONFIGURATION HELPER
 * Supports all common HTML form elements for robust filtering
 */
function tr_smart_configure_filters($table, string $modelClass, array $modelInfo, array $filterOverrides): void {
    $filters = [];
    
    if (!empty($filterOverrides)) {
        // Use provided filters with smart normalization
        $filters = tr_normalize_filter_config($filterOverrides);
    } else {
        // Auto-detect from model info with enhanced types
        foreach ($modelInfo['filterable'] as $field => $config) {
            $filters[$field] = tr_normalize_single_filter_config($field, $config);
        }
    }
    
    if (!empty($filters)) {
        tr_apply_smart_filters($table, $modelClass, $filters);
    }
}

/**
 * Apply smart filters to table
 */
function tr_apply_smart_filters($table, string $modelClass, array $filters): void {
    $resourceName = tr_infer_resource_name($modelClass);
    
    // Add filter form rendering
    $table->addSearchFormFilter(function() use ($filters, $resourceName, $modelClass) {
        renderAdvancedSearchActions($resourceName);
        tr_render_smart_filters($modelClass, $filters);
    });
    
    // Add filter query application
    $table->addSearchModelFilter(function($args, $model, $table) use ($filters) {
        tr_apply_smart_filter_queries($filters, $model);
    });
}

/**
 * Normalize filter configuration to ensure consistency
 */
function tr_normalize_filter_config(array $filters): array {
    $normalized = [];
    
    foreach ($filters as $field => $config) {
        $normalized[$field] = tr_normalize_single_filter_config($field, $config);
    }
    
    return $normalized;
}

/**
 * Normalize individual filter configuration
 */
function tr_normalize_single_filter_config(string $field, $config): array {
    // Handle simple string configuration (just type)
    if (is_string($config)) {
        $config = ['type' => $config];
    }
    
    // Ensure config is an array
    if (!is_array($config)) {
        $config = ['type' => 'text'];
    }
    
    // Set defaults
    $normalized = [
        'type' => $config['type'] ?? 'text',
        'label' => $config['label'] ?? tr_humanize_field_name($field),
        'placeholder' => $config['placeholder'] ?? null,
        'options' => $config['options'] ?? null,
        'multiple' => $config['multiple'] ?? false,
        'min' => $config['min'] ?? null,
        'max' => $config['max'] ?? null,
        'step' => $config['step'] ?? null,
        'rows' => $config['rows'] ?? 3,
        'cols' => $config['cols'] ?? null,
        'class' => $config['class'] ?? '',
        'attributes' => $config['attributes'] ?? [],
        'where_clause' => $config['where_clause'] ?? null,
        'operator' => $config['operator'] ?? null,
        'tags' => $config['tags'] ?? false,
        'separator' => $config['separator'] ?? null,
        'date_format' => $config['date_format'] ?? null,
    ];
    
    // Auto-generate placeholder if not provided
    if (!$normalized['placeholder']) {
        $normalized['placeholder'] = tr_generate_filter_placeholder($field, $normalized['type']);
    }
    
    return $normalized;
}

/**
 * Generate smart placeholder text for filters
 */
function tr_generate_filter_placeholder(string $field, string $type): string {
    $humanized = tr_humanize_field_name($field);
    
    switch ($type) {
        case 'search':
        case 'text':
            return "Search {$humanized}";
        case 'email':
            return "Enter email address";
        case 'url':
            return "Enter URL";
        case 'tel':
            return "Enter phone number";
        case 'number':
        case 'range':
            return "Enter {$humanized}";
        case 'date':
            return "Select date";
        case 'datetime-local':
            return "Select date and time";
        case 'time':
            return "Select time";
        case 'month':
            return "Select month";
        case 'week':
            return "Select week";
        case 'color':
            return "Choose color";
        case 'password':
            return "Enter password";
        case 'textarea':
            return "Enter {$humanized}";
        case 'tags':
            return "Type and press Enter to add tags";
        default:
            return "Select {$humanized}";
    }
}

/**
 * ENHANCED FILTER RENDERING
 */

/**
 * Render smart filter form fields with full HTML element support
 */
function tr_render_smart_filters(string $modelClass, array $filters): void {
    echo '<div class="tr-search-filters">';
    
    foreach ($filters as $field => $config) {
        tr_render_smart_filter_field($field, $config, $modelClass);
    }
    
    echo '</div>';
}

/**
 * Render individual filter field with comprehensive element support
 */
function tr_render_smart_filter_field(string $field, array $config, string $modelClass): void {
    $type = $config['type'];
    $label = $config['label'];
    $value = tr_get_filter_value($field, $type);
    
    echo '<div class="tr-filter-group">';
    echo '<label>' . esc_html($label) . ':</label>';
    
    switch ($type) {
        // Text-based inputs
        case 'text':
        case 'search':
        case 'email':
        case 'url':
        case 'tel':
        case 'password':
            tr_render_text_input($field, $value, $config, $type);
            break;
            
        // Numeric inputs
        case 'number':
            tr_render_number_input($field, $value, $config);
            break;
            
        case 'range':
            tr_render_range_input($field, $value, $config);
            break;
            
        // Date/time inputs
        case 'date':
        case 'datetime-local':
        case 'time':
        case 'month':
        case 'week':
            tr_render_datetime_input($field, $value, $config, $type);
            break;
            
        case 'date-range':
            tr_render_date_range_filter($field, $config);
            break;
            
        // Selection inputs
        case 'select':
            tr_render_select_filter($field, $value, $config, $modelClass);
            break;
            
        case 'multiselect':
            $config['multiple'] = true;
            tr_render_select_filter($field, $value, $config, $modelClass);
            break;
            
        // Boolean inputs
        case 'checkbox':
            tr_render_checkbox_input($field, $value, $config);
            break;
            
        case 'bool':
        case 'boolean':
            tr_render_bool_filter($field, $value);
            break;
            
        case 'radio':
            tr_render_radio_group($field, $value, $config);
            break;
            
        case 'checkbox-group':
            tr_render_checkbox_group($field, $value, $config);
            break;
            
        // Advanced inputs
        case 'textarea':
            tr_render_textarea_input($field, $value, $config);
            break;
            
        case 'tags':
            tr_render_tags_input($field, $value, $config);
            break;
            
        case 'color':
            tr_render_color_input($field, $value, $config);
            break;
            
        case 'file':
            tr_render_file_input($field, $value, $config);
            break;
            
        case 'hidden':
            tr_render_hidden_input($field, $value, $config);
            break;
            
        // Complex inputs
        case 'numeric-range':
            tr_render_numeric_range_filter($field, $value, $config);
            break;
            
        case 'slider':
            tr_render_slider_input($field, $value, $config);
            break;
            
        default:
            tr_render_text_input($field, $value, $config, 'text');
            break;
    }
    
    echo '</div>';
}

/**
 * Get filter value from request, handling different input types
 */
function tr_get_filter_value(string $field, string $type) {
    switch ($type) {
        case 'multiselect':
        case 'checkbox-group':
        case 'tags':
            return $_GET[$field] ?? [];
            
        case 'checkbox':
            return isset($_GET[$field]) ? $_GET[$field] : '';
            
        case 'date-range':
        case 'numeric-range':
            return [
                'from' => $_GET[$field . '_from'] ?? '',
                'to' => $_GET[$field . '_to'] ?? ''
            ];
            
        default:
            return $_GET[$field] ?? '';
    }
}

/**
 * INDIVIDUAL FILTER ELEMENT RENDERERS
 */

/**
 * Render text-based inputs
 */
function tr_render_text_input(string $field, string $value, array $config, string $inputType = 'text'): void {
    $attrs = tr_build_input_attributes($config);
    $placeholder = $config['placeholder'] ? 'placeholder="' . esc_attr($config['placeholder']) . '"' : '';
    
    echo '<input type="' . esc_attr($inputType) . '" name="' . esc_attr($field) . '" ' .
         'class="tr-filter ' . esc_attr($config['class']) . '" ' .
         'value="' . esc_attr($value) . '" ' . $placeholder . ' ' . $attrs . '>';
}

/**
 * Render number input with operators
 */
function tr_render_number_input(string $field, string $value, array $config): void {
    if ($config['operator'] !== false) {
        $operator = $_GET[$field . '_operator'] ?? '=';
        $operators = [
            '=' => 'Equal to',
            '<' => 'Less than',
            '>' => 'Greater than',
            '<=' => 'Less or equal',
            '>=' => 'Greater or equal',
            '!=' => 'Not equal to'
        ];
        
        echo '<div class="tr-range-inputs">';
        echo '<select name="' . esc_attr($field . '_operator') . '" class="tr-filter">';
        foreach ($operators as $op => $label) {
            $selected = $operator === $op ? 'selected' : '';
            echo '<option value="' . esc_attr($op) . '" ' . $selected . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }
    
    $attrs = tr_build_input_attributes($config, ['min', 'max', 'step']);
    $placeholder = $config['placeholder'] ? 'placeholder="' . esc_attr($config['placeholder']) . '"' : '';
    
    echo '<input type="number" name="' . esc_attr($field) . '" ' .
         'class="tr-filter ' . esc_attr($config['class']) . '" ' .
         'value="' . esc_attr($value) . '" ' . $placeholder . ' ' . $attrs . '>';
    
    if ($config['operator'] !== false) {
        echo '</div>';
    }
}

/**
 * Render range input (slider)
 */
function tr_render_range_input(string $field, string $value, array $config): void {
    $attrs = tr_build_input_attributes($config, ['min', 'max', 'step']);
    
    echo '<div class="tr-range-wrapper">';
    echo '<input type="range" name="' . esc_attr($field) . '" ' .
         'class="tr-filter tr-range-slider ' . esc_attr($config['class']) . '" ' .
         'value="' . esc_attr($value) . '" ' . $attrs . '>';
    echo '<output for="' . esc_attr($field) . '" class="tr-range-value">' . esc_html($value ?: $config['min'] ?: '0') . '</output>';
    echo '</div>';
}

/**
 * Render date/time inputs
 */
function tr_render_datetime_input(string $field, string $value, array $config, string $inputType): void {
    $attrs = tr_build_input_attributes($config);
    $placeholder = $config['placeholder'] ? 'placeholder="' . esc_attr($config['placeholder']) . '"' : '';
    
    echo '<input type="' . esc_attr($inputType) . '" name="' . esc_attr($field) . '" ' .
         'class="tr-filter ' . esc_attr($config['class']) . '" ' .
         'value="' . esc_attr($value) . '" ' . $placeholder . ' ' . $attrs . '>';
}

/**
 * Render enhanced date range filter
 */
function tr_render_date_range_filter(string $field, array $config): void {
    $fromValue = $_GET[$field . '_from'] ?? '';
    $toValue = $_GET[$field . '_to'] ?? '';
    
    echo '<div class="tr-date-inputs">';
    echo '<input type="date" name="' . esc_attr($field . '_from') . '" class="tr-filter" ' .
         'value="' . esc_attr($fromValue) . '" placeholder="From" title="From Date">';
    echo '<input type="date" name="' . esc_attr($field . '_to') . '" class="tr-filter" ' .
         'value="' . esc_attr($toValue) . '" placeholder="To" title="To Date">';
    echo '</div>';
}

/**
 * Render enhanced select filter
 */
function tr_render_select_filter(string $field, $value, array $config, string $modelClass): void {
    $multiple = $config['multiple'] ? 'multiple' : '';
    $name = $config['multiple'] ? $field . '[]' : $field;
    $attrs = tr_build_input_attributes($config);
    
    echo '<select name="' . esc_attr($name) . '" class="tr-filter ' . esc_attr($config['class']) . '" ' . $multiple . ' ' . $attrs . '>';
    
    if (!$config['multiple']) {
        echo '<option value="">All</option>';
    }
    
    $options = $config['options'] ?? [];
    
    if (empty($options) || $options === 'dynamic') {
        tr_render_dynamic_select_options($field, $modelClass, $value);
    } elseif (is_callable($options)) {
        tr_render_callback_select_options($options, $value);
    } 
    // else {
    //     tr_render_static_select_options($options, $value, $config['multiple']);
    // }
    
    echo '</select>';
}

/**
 * Render checkbox input
 */
function tr_render_checkbox_input(string $field, string $value, array $config): void {
    $checked = !empty($value) ? 'checked' : '';
    $attrs = tr_build_input_attributes($config);
    
    echo '<label class="tr-checkbox-label">';
    echo '<input type="checkbox" name="' . esc_attr($field) . '" value="1" ' .
         'class="tr-filter ' . esc_attr($config['class']) . '" ' . $checked . ' ' . $attrs . '>';
    echo '<span class="tr-checkbox-text">' . esc_html($config['label']) . '</span>';
    echo '</label>';
}

/**
 * Render radio group
 */
function tr_render_radio_group(string $field, string $value, array $config): void {
    $options = $config['options'] ?? [];
    
    echo '<div class="tr-radio-group">';
    
    // Add "All" option for radio groups
    echo '<label class="tr-radio-label">';
    echo '<input type="radio" name="' . esc_attr($field) . '" value="" ' .
         'class="tr-filter" ' . (empty($value) ? 'checked' : '') . '>';
    echo '<span class="tr-radio-text">All</span>';
    echo '</label>';
    
    foreach ($options as $optValue => $optLabel) {
        $checked = $value == $optValue ? 'checked' : '';
        echo '<label class="tr-radio-label">';
        echo '<input type="radio" name="' . esc_attr($field) . '" value="' . esc_attr($optValue) . '" ' .
             'class="tr-filter" ' . $checked . '>';
        echo '<span class="tr-radio-text">' . esc_html($optLabel) . '</span>';
        echo '</label>';
    }
    
    echo '</div>';
}

/**
 * Render checkbox group
 */
function tr_render_checkbox_group(string $field, array $values, array $config): void {
    $options = $config['options'] ?? [];
    
    echo '<div class="tr-checkbox-group">';
    
    foreach ($options as $optValue => $optLabel) {
        $checked = in_array($optValue, $values) ? 'checked' : '';
        echo '<label class="tr-checkbox-label">';
        echo '<input type="checkbox" name="' . esc_attr($field) . '[]" value="' . esc_attr($optValue) . '" ' .
             'class="tr-filter" ' . $checked . '>';
        echo '<span class="tr-checkbox-text">' . esc_html($optLabel) . '</span>';
        echo '</label>';
    }
    
    echo '</div>';
}

/**
 * Render textarea input
 */
function tr_render_textarea_input(string $field, string $value, array $config): void {
    $attrs = tr_build_input_attributes($config, ['rows', 'cols']);
    $placeholder = $config['placeholder'] ? 'placeholder="' . esc_attr($config['placeholder']) . '"' : '';
    $rows = $config['rows'] ?: 3;
    
    echo '<textarea name="' . esc_attr($field) . '" ' .
         'class="tr-filter ' . esc_attr($config['class']) . '" ' .
         'rows="' . esc_attr($rows) . '" ' . $placeholder . ' ' . $attrs . '>' .
         esc_textarea($value) . '</textarea>';
}

/**
 * Render tags input
 */
function tr_render_tags_input(string $field, $values, array $config): void {
    $separator = $config['separator'] ?? ',';
    $displayValue = is_array($values) ? implode($separator . ' ', $values) : $values;
    $attrs = tr_build_input_attributes($config);
    
    echo '<input type="text" name="' . esc_attr($field) . '" ' .
         'class="tr-filter tr-tags-input ' . esc_attr($config['class']) . '" ' .
         'value="' . esc_attr($displayValue) . '" ' .
         'placeholder="' . esc_attr($config['placeholder']) . '" ' .
         'data-separator="' . esc_attr($separator) . '" ' . $attrs . '>';
}

/**
 * Render color input
 */
function tr_render_color_input(string $field, string $value, array $config): void {
    $attrs = tr_build_input_attributes($config);
    
    echo '<input type="color" name="' . esc_attr($field) . '" ' .
         'class="tr-filter ' . esc_attr($config['class']) . '" ' .
         'value="' . esc_attr($value) . '" ' . $attrs . '>';
}

/**
 * Render file input
 */
function tr_render_file_input(string $field, string $value, array $config): void {
    $attrs = tr_build_input_attributes($config, ['accept']);
    
    echo '<input type="file" name="' . esc_attr($field) . '" ' .
         'class="tr-filter ' . esc_attr($config['class']) . '" ' . $attrs . '>';
         
    if ($value) {
        echo '<small>Current: ' . esc_html(basename($value)) . '</small>';
    }
}

/**
 * Render hidden input
 */
function tr_render_hidden_input(string $field, string $value, array $config): void {
    echo '<input type="hidden" name="' . esc_attr($field) . '" value="' . esc_attr($value) . '">';
}

/**
 * Render numeric range filter
 */
function tr_render_numeric_range_filter(string $field, array $values, array $config): void {
    $fromValue = $values['from'] ?? '';
    $toValue = $values['to'] ?? '';
    $attrs = tr_build_input_attributes($config, ['min', 'max', 'step']);
    
    echo '<div class="tr-range-inputs">';
    echo '<input type="number" name="' . esc_attr($field . '_from') . '" ' .
         'class="tr-filter" value="' . esc_attr($fromValue) . '" ' .
         'placeholder="Min" ' . $attrs . '>';
    echo '<input type="number" name="' . esc_attr($field . '_to') . '" ' .
         'class="tr-filter" value="' . esc_attr($toValue) . '" ' .
         'placeholder="Max" ' . $attrs . '>';
    echo '</div>';
}

/**
 * Render slider input with range
 */
function tr_render_slider_input(string $field, string $value, array $config): void {
    $min = $config['min'] ?? 0;
    $max = $config['max'] ?? 100;
    $step = $config['step'] ?? 1;
    
    echo '<div class="tr-slider-wrapper">';
    echo '<input type="range" name="' . esc_attr($field) . '" ' .
         'class="tr-filter tr-slider ' . esc_attr($config['class']) . '" ' .
         'value="' . esc_attr($value ?: $min) . '" ' .
         'min="' . esc_attr($min) . '" max="' . esc_attr($max) . '" step="' . esc_attr($step) . '">';
    echo '<div class="tr-slider-labels">';
    echo '<span class="tr-slider-min">' . esc_html($min) . '</span>';
    echo '<span class="tr-slider-value">' . esc_html($value ?: $min) . '</span>';
    echo '<span class="tr-slider-max">' . esc_html($max) . '</span>';
    echo '</div>';
    echo '</div>';
}

/**
 * HELPER FUNCTIONS
 */

/**
 * Build HTML attributes string from config
 */
function tr_build_input_attributes(array $config, array $allowedAttrs = []): string {
    $attrs = [];
    
    // Standard HTML attributes
    $standardAttrs = array_merge(['min', 'max', 'step', 'rows', 'cols', 'accept', 'multiple'], $allowedAttrs);
    
    foreach ($standardAttrs as $attr) {
        if (isset($config[$attr]) && $config[$attr] !== null) {
            if ($attr === 'multiple' && $config[$attr]) {
                $attrs[] = 'multiple';
            } else {
                $attrs[] = $attr . '="' . esc_attr($config[$attr]) . '"';
            }
        }
    }
    
    // Custom attributes
    if (!empty($config['attributes'])) {
        foreach ($config['attributes'] as $attr => $value) {
            $attrs[] = esc_attr($attr) . '="' . esc_attr($value) . '"';
        }
    }
    
    return implode(' ', $attrs);
}

/**
 * Render static select options
 */
function tr_render_static_select_options(array $options, $value, bool $multiple = false): void {
    $selectedValues = $multiple && is_array($value) ? $value : [$value];
    
    foreach ($options as $optValue => $optLabel) {
        $selected = in_array($optValue, $selectedValues) ? 'selected' : '';
        echo '<option value="' . esc_attr($optValue) . '" ' . $selected . '>' . 
             esc_html($optLabel) . '</option>';
    }
}

/**
 * Render callback-generated select options
 */
function tr_render_callback_select_options(callable $callback, $value): void {
    try {
        $options = call_user_func($callback);
        if (is_array($options)) {
            tr_render_static_select_options($options, $value);
        }
    } catch (Exception $e) {
        error_log('Filter options callback failed: ' . $e->getMessage());
        echo '<option value="">-- Error loading options --</option>';
    }
}

/**
 * ENHANCED QUERY APPLICATION
 */

/**
 * Apply enhanced filter queries to the model
 */
function tr_apply_smart_filter_queries(array $filters, $model): void {
    foreach ($filters as $field => $config) {
        tr_apply_single_filter_query($field, $config, $model);
    }
}

/**
 * Apply individual filter query with comprehensive type support
 */
function tr_apply_single_filter_query(string $field, array $config, $model): void {
    $type = $config['type'];
    $value = tr_get_filter_value($field, $type);
    
    // Skip empty values unless explicitly allowed
    if (tr_is_empty_filter_value($value, $type)) {
        return;
    }
    
    switch ($type) {
        case 'select':
        case 'radio':
        case 'bool':
        case 'boolean':
        case 'checkbox':
        case 'color':
        case 'hidden':
            tr_apply_exact_match_query($field, $value, $model, $config);
            break;
            
        case 'multiselect':
        case 'checkbox-group':
            tr_apply_multi_value_query($field, $value, $model, $config);
            break;
            
        case 'text':
        case 'search':
        case 'email':
        case 'url':
        case 'tel':
        case 'textarea':
            tr_apply_text_search_query($field, $value, $model, $config);
            break;
            
        case 'number':
            tr_apply_numeric_query($field, $value, $model, $config);
            break;
            
        case 'range':
        case 'slider':
            tr_apply_exact_match_query($field, $value, $model, $config);
            break;
            
        case 'date':
        case 'datetime-local':
        case 'time':
        case 'month':
        case 'week':
            tr_apply_date_query($field, $value, $model, $config);
            break;
            
        case 'date-range':
            tr_apply_date_range_query($field, $value, $model, $config);
            break;
            
        case 'numeric-range':
            tr_apply_numeric_range_query($field, $value, $model, $config);
            break;
            
        case 'tags':
            tr_apply_tags_query($field, $value, $model, $config);
            break;
            
        case 'file':
            tr_apply_file_query($field, $value, $model, $config);
            break;
            
        default:
            tr_apply_text_search_query($field, $value, $model, $config);
            break;
    }
}

/**
 * Check if filter value should be considered empty
 */
function tr_is_empty_filter_value($value, string $type): bool {
    if (is_array($value)) {
        if ($type === 'date-range' || $type === 'numeric-range') {
            return empty($value['from']) && empty($value['to']);
        }
        return empty($value);
    }
    
    return empty($value) && $value !== '0';
}

/**
 * Apply exact match query
 */
function tr_apply_exact_match_query(string $field, $value, $model, array $config): void {
    if ($config['where_clause']) {
        $model->whereRaw($config['where_clause'], [$value]);
    } else {
        $model->where($field, $operator, $value);
    }
}

/**
 * ADVANCED CONFIGURATION EXAMPLES AND USAGE
 */

/**
 * Example usage configurations for different filter types
 * 
 * // Zero-config (auto-infer from model)
 * tr_smart_index(User::class);
 * 
 * // Basic configuration
 * tr_smart_index(User::class, [
 *     'filters' => [
 *         'name' => 'text',
 *         'email' => 'email', 
 *         'status' => 'select',
 *         'is_active' => 'boolean',
 *         'created_at' => 'date-range'
 *     ]
 * ]);
 * 
 * // Advanced configuration
 * tr_smart_index(Product::class, [
 *     'filters' => [
 *         'name' => [
 *             'type' => 'search',
 *             'placeholder' => 'Search products...',
 *             'class' => 'large-search'
 *         ],
 *         'category_ids' => [
 *             'type' => 'multiselect',
 *             'label' => 'Categories',
 *             'options' => function() {
 *                 return Category::all()->pluck('name', 'id')->toArray();
 *             }
 *         ],
 *         'price' => [
 *             'type' => 'numeric-range',
 *             'label' => 'Price Range',
 *             'min' => 0,
 *             'step' => 0.01
 *         ],
 *         'tags' => [
 *             'type' => 'tags',
 *             'separator' => ',',
 *             'placeholder' => 'Enter tags separated by commas'
 *         ],
 *         'color' => [
 *             'type' => 'color',
 *             'label' => 'Primary Color'
 *         ],
 *         'rating' => [
 *             'type' => 'slider',
 *             'min' => 1,
 *             'max' => 5,
 *             'step' => 0.5
 *         ],
 *         'features' => [
 *             'type' => 'checkbox-group',
 *             'options' => [
 *                 'waterproof' => 'Waterproof',
 *                 'wireless' => 'Wireless',
 *                 'portable' => 'Portable'
 *             ]
 *         ],
 *         'description' => [
 *             'type' => 'textarea',
 *             'rows' => 4,
 *             'placeholder' => 'Search in descriptions...'
 *         ],
 *         'custom_query' => [
 *             'type' => 'text',
 *             'where_clause' => 'MATCH(name, description) AGAINST(? IN NATURAL LANGUAGE MODE)',
 *             'placeholder' => 'Full-text search...'
 *         ]
 *     ]
 * ]);
 */

/**
 * Apply date query
 */
function tr_apply_date_query(string $field, string $value, $model, array $config): void {
    if ($config['where_clause']) {
        $model->whereRaw($config['where_clause'], [$value]);
    } else {
        // For date types, we might want to match the entire day
        if ($config['type'] === 'date') {
            $model->whereDate($field, $value);
        } else {
            $model->where($field, $value);
        }
    }
}

/**
 * Apply date range query
 */
function tr_apply_date_range_query(string $field, array $values, $model, array $config): void {
    $fromValue = $values['from'] ?? '';
    $toValue = $values['to'] ?? '';
    
    if (!empty($fromValue)) {
        $model->where($field, '>=', $fromValue);
    }
    
    if (!empty($toValue)) {
        // Add end of day for date fields
        $endValue = $config['type'] === 'date-range' ? $toValue . ' 23:59:59' : $toValue;
        $model->where($field, '<=', $endValue);
    }
}

/**
 * Apply numeric range query
 */
function tr_apply_numeric_range_query(string $field, array $values, $model, array $config): void {
    $fromValue = $values['from'] ?? '';
    $toValue = $values['to'] ?? '';
    
    if (!empty($fromValue) && is_numeric($fromValue)) {
        $model->where($field, '>=', $fromValue);
    }
    
    if (!empty($toValue) && is_numeric($toValue)) {
        $model->where($field, '<=', $toValue);
    }
}

/**
 * Apply tags query (comma-separated values)
 */
function tr_apply_tags_query(string $field, $value, $model, array $config): void {
    $separator = $config['separator'] ?? ',';
    
    if (is_string($value)) {
        $tags = array_map('trim', explode($separator, $value));
    } else {
        $tags = is_array($value) ? $value : [$value];
    }
    
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $model->where($field, 'LIKE', "%{$tag}%");
            }
        }
    }
}

/**
 * Apply file query (check for existence or specific filename)
 */
function tr_apply_file_query(string $field, string $value, $model, array $config): void {
    if ($config['where_clause']) {
        $model->whereRaw($config['where_clause'], [$value]);
    } else {
        // Check if file field is not null/empty
        $model->whereNotNull($field)->where($field, '!=', '');
    }
}

/**
 * UTILITY ENHANCEMENTS
 */

/**
 * Enhanced dynamic select options with better error handling
 */
function tr_render_dynamic_select_options(string $field, string $modelClass, $value): void {
    global $wpdb;
    
    try {
        $model = new $modelClass();
        $tableName = $model->getTable();
        
        // Use proper WordPress wpdb methods with better security
        $query = $wpdb->prepare(
            "SELECT DISTINCT {$field} FROM {$tableName} WHERE {$field} IS NOT NULL AND {$field} != '' ORDER BY {$field} LIMIT 100"
        );
        
        $results = $wpdb->get_col($query);
        
        if (!empty($results)) {
            $selectedValues = is_array($value) ? $value : [$value];
            
            foreach ($results as $result) {
                $selected = in_array($result, $selectedValues) ? 'selected' : '';
                $displayText = tr_format_option_text($result, $field);
                echo '<option value="' . esc_attr($result) . '" ' . $selected . '>' . 
                     esc_html($displayText) . '</option>';
            }
        } else {
            echo '<option value="">-- No options available --</option>';
        }
    } catch (Exception $e) {
        error_log('Failed to load dynamic options for ' . $field . ': ' . $e->getMessage());
        echo '<option value="">-- Error loading options --</option>';
    }
}

/**
 * Format option text for better display
 */
function tr_format_option_text(string $value, string $field): string {
    // Handle boolean-like values
    if (in_array(strtolower($value), ['0', '1', 'true', 'false', 'yes', 'no'])) {
        $boolMap = [
            '0' => 'No', '1' => 'Yes',
            'false' => 'No', 'true' => 'Yes',
            'no' => 'No', 'yes' => 'Yes'
        ];
        return $boolMap[strtolower($value)] ?? $value;
    }
    
    // Handle status-like fields
    if (str_contains($field, 'status')) {
        return ucwords(str_replace(['_', '-'], ' ', $value));
    }
    
    // Default formatting
    if (strlen($value) > 50) {
        return substr($value, 0, 47) . '...';
    }
    
    return $value;
}

/**
 * ADVANCED CONFIGURATION EXAMPLES AND USAGE
 */

/**
 * Example usage configurations for different filter types
 * 
 * // Zero-config (auto-infer from model)
 * tr_smart_index(User::class);
 * 
 * // Basic configuration
 * tr_smart_index(User::class, [
 *     'filters' => [
 *         'name' => 'text',
 *         'email' => 'email', 
 *         'status' => 'select',
 *         'is_active' => 'boolean',
 *         'created_at' => 'date-range'
 *     ]
 * ]);
 * 
 * // Advanced configuration
 * tr_smart_index(Product::class, [
 *     'filters' => [
 *         'name' => [
 *             'type' => 'search',
 *             'placeholder' => 'Search products...',
 *             'class' => 'large-search'
 *         ],
 *         'category_ids' => [
 *             'type' => 'multiselect',
 *             'label' => 'Categories',
 *             'options' => function() {
 *                 return Category::all()->pluck('name', 'id')->toArray();
 *             }
 *         ],
 *         'price' => [
 *             'type' => 'numeric-range',
 *             'label' => 'Price Range',
 *             'min' => 0,
 *             'step' => 0.01
 *         ],
 *         'tags' => [
 *             'type' => 'tags',
 *             'separator' => ',',
 *             'placeholder' => 'Enter tags separated by commas'
 *         ],
 *         'color' => [
 *             'type' => 'color',
 *             'label' => 'Primary Color'
 *         ],
 *         'rating' => [
 *             'type' => 'slider',
 *             'min' => 1,
 *             'max' => 5,
 *             'step' => 0.5
 *         ],
 *         'features' => [
 *             'type' => 'checkbox-group',
 *             'options' => [
 *                 'waterproof' => 'Waterproof',
 *                 'wireless' => 'Wireless',
 *                 'portable' => 'Portable'
 *             ]
 *         ],
 *         'description' => [
 *             'type' => 'textarea',
 *             'rows' => 4,
 *             'placeholder' => 'Search in descriptions...'
 *         ],
 *         'custom_query' => [
 *             'type' => 'text',
 *             'where_clause' => 'MATCH(name, description) AGAINST(? IN NATURAL LANGUAGE MODE)',
 *             'placeholder' => 'Full-text search...'
 *         ]
 *     ]
 * ]);
 */

/**
 * ENHANCED BOOLEAN FILTER HELPER (Updated)
 */
function tr_render_bool_filter(string $field, string $value): void {
    echo '<select name="' . esc_attr($field) . '" class="tr-filter">';
    echo '<option value="">All</option>';
    echo '<option value="1"' . ($value === '1' ? ' selected' : '') . '>Yes</option>';
    echo '<option value="0"' . ($value === '0' ? ' selected' : '') . '>No</option>';
    echo '</select>';
}

/**
 * BACKWARDS COMPATIBILITY HELPERS
 * These ensure existing filter configurations continue to work
 */

/**
 * Legacy number filter renderer (maintains existing API)
 */
function tr_render_number_filter(string $field, string $value, array $config): void {
    tr_render_number_input($field, $value, $config);
}

/**
 * Legacy text filter renderer (maintains existing API)
 */
function tr_render_text_filter(string $field, string $value, array $config): void {
    tr_render_text_input($field, $value, $config, 'text');
}

/**
 * Legacy select filter renderer (maintains existing API)
 */
function tr_render_select_filter_legacy(string $field, string $value, array $config, string $modelClass): void {
    tr_render_select_filter($field, $value, $config, $modelClass);
}

/**
 * FILTER TYPE DETECTION HELPERS
 */

/**
 * Auto-detect filter type from field name and database column info
 */
function tr_auto_detect_filter_type(string $field, array $columnInfo): string {
    $fieldLower = strtolower($field);
    $type = strtolower($columnInfo['type'] ?? 'varchar');
    
    // Boolean fields
    if (str_contains($fieldLower, 'is_') || str_contains($fieldLower, 'has_') || 
        str_contains($fieldLower, 'can_') || $type === 'boolean' || $type === 'tinyint(1)') {
        return 'boolean';
    }
    
    // Date/time fields
    if (str_contains($fieldLower, 'date') || str_contains($fieldLower, 'time') || 
        str_contains($type, 'date') || str_contains($type, 'time')) {
        return str_contains($fieldLower, 'range') ? 'date-range' : 'date';
    }
    
    // Numeric fields
    if (str_contains($type, 'int') || str_contains($type, 'decimal') || 
        str_contains($type, 'float') || str_contains($type, 'double')) {
        return 'number';
    }
    
    // Email fields
    if (str_contains($fieldLower, 'email')) {
        return 'email';
    }
    
    // URL fields
    if (str_contains($fieldLower, 'url') || str_contains($fieldLower, 'link') || 
        str_contains($fieldLower, 'website')) {
        return 'url';
    }
    
    // Phone fields
    if (str_contains($fieldLower, 'phone') || str_contains($fieldLower, 'tel')) {
        return 'tel';
    }
    
    // Color fields
    if (str_contains($fieldLower, 'color') || str_contains($fieldLower, 'colour')) {
        return 'color';
    }
    
    // Large text fields
    if (str_contains($type, 'text') || str_contains($type, 'longtext')) {
        return 'textarea';
    }
    
    // Select fields (common naming patterns)
    if (str_contains($fieldLower, 'status') || str_contains($fieldLower, 'type') || 
        str_contains($fieldLower, 'category') || str_contains($fieldLower, 'role')) {
        return 'select';
    }
    
    // Tag-like fields
    if (str_contains($fieldLower, 'tag') || str_contains($fieldLower, 'keyword')) {
        return 'tags';
    }
    
    // Default to text for unknown types
    return 'text';
}

/**
 * ENHANCED RELATIONSHIP FILTER SUPPORT
 */

/**
 * Render relationship-based select options
 */
function tr_render_relationship_select_options(array $relationshipConfig, $value): void {
    try {
        $relatedModel = $relationshipConfig['related_model'] ?? null;
        $displayField = $relationshipConfig['display_field'] ?? 'name';
        $keyField = $relationshipConfig['key_field'] ?? 'id';
        
        if (!$relatedModel || !class_exists($relatedModel)) {
            echo '<option value="">-- Relationship model not found --</option>';
            return;
        }
        
        $model = new $relatedModel();
        $records = $model->limit(100)->get();
        
        $selectedValues = is_array($value) ? $value : [$value];
        
        foreach ($records as $record) {
            $optValue = $record->$keyField ?? $record->id;
            $optLabel = $record->$displayField ?? $record->name ?? "Record #{$optValue}";
            $selected = in_array($optValue, $selectedValues) ? 'selected' : '';
            
            echo '<option value="' . esc_attr($optValue) . '" ' . $selected . '>' . 
                 esc_html($optLabel) . '</option>';
        }
    } catch (Exception $e) {
        error_log('Failed to load relationship options: ' . $e->getMessage());
        echo '<option value="">-- Error loading relationship data --</option>';
    }
}

/**
 * FILTER VALIDATION AND SANITIZATION
 */

/**
 * Validate and sanitize filter input based on type
 */
function tr_validate_filter_input(string $field, $value, array $config): array {
    $type = $config['type'];
    $errors = [];
    $sanitized = $value;
    
    switch ($type) {
        case 'email':
            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format for {$field}";
            }
            break;
            
        case 'url':
            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                $errors[] = "Invalid URL format for {$field}";
            }
            break;
            
        case 'number':
        case 'range':
        case 'numeric-range':
            if (!empty($value) && !is_numeric($value)) {
                $errors[] = "Invalid numeric value for {$field}";
                $sanitized = '';
            }
            break;
            
        case 'date':
        case 'datetime-local':
            if (!empty($value) && !strtotime($value)) {
                $errors[] = "Invalid date format for {$field}";
                $sanitized = '';
            }
            break;
            
        case 'color':
            if (!empty($value) && !preg_match('/^#[a-fA-F0-9]{6}$/', $value)) {
                $errors[] = "Invalid color format for {$field}";
                $sanitized = '';
            }
            break;
            
        default:
            // Basic sanitization for text fields
            if (is_string($value)) {
                $sanitized = sanitize_text_field($value);
            }
            break;
    }
    
    return [
        'value' => $sanitized,
        'errors' => $errors,
        'valid' => empty($errors)
    ];
}

/**
 * PERFORMANCE OPTIMIZATION HELPERS
 */

/**
 * Cache dynamic filter options to reduce database queries
 */
function tr_get_cached_filter_options(string $cacheKey, callable $generator, int $expiry = 300): array {
    static $cache = [];
    
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }
    
    // In a real implementation, you might use WordPress transients or another caching mechanism
    $options = call_user_func($generator);
    $cache[$cacheKey] = $options;
    
    return $options;
}

/**
 * Optimize query for large datasets
 */
function tr_optimize_filter_query($query, array $filters): void {
    // Add index hints for commonly filtered fields
    $indexedFields = ['status', 'type', 'category_id', 'user_id', 'created_at'];
    
    foreach ($filters as $field => $config) {
        if (in_array($field, $indexedFields)) {
            // Suggest using indexes for better performance
            // This is a placeholder - actual implementation would depend on your ORM
            $query->useIndex($field . '_idx');
        }
    }
}

/**
 * DEBUGGING AND LOGGING HELPERS
 */

/**
 * Log filter configuration for debugging
 */
function tr_debug_filter_config(array $filters): void {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Filter Configuration: ' . print_r($filters, true));
    }
}

/**
 * Log applied filter queries
 */
function tr_debug_filter_queries(array $appliedFilters): void {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Applied Filter Queries: ' . print_r($appliedFilters, true));
    }
}

/**
 * ACCESSIBILITY HELPERS
 */

/**
 * Add ARIA attributes for better accessibility
 */
function tr_add_accessibility_attributes(string $field, array $config): string {
    $attrs = [];
    
    // Add ARIA label if different from visible label
    if (!empty($config['aria_label'])) {
        $attrs[] = 'aria-label="' . esc_attr($config['aria_label']) . '"';
    }
    
    // Add ARIA description
    if (!empty($config['aria_describedby'])) {
        $attrs[] = 'aria-describedby="' . esc_attr($config['aria_describedby']) . '"';
    }
    
    // Add required attribute
    if (!empty($config['required'])) {
        $attrs[] = 'required aria-required="true"';
    }
    
    // Add invalid state for validation errors
    if (!empty($config['has_errors'])) {
        $attrs[] = 'aria-invalid="true"';
    }
    
    return implode(' ', $attrs);
}

/**
 * FINAL INTEGRATION HELPER
 * This function helps identify which parts need to be replaced in the original file
 */

/**
 * Get functions that should replace existing ones in smart_index.php
 */
function tr_get_enhanced_filter_functions(): array {
    return [
        'configuration' => [
            'tr_smart_configure_filters',
            'tr_normalize_filter_config',
            'tr_normalize_single_filter_config',
            'tr_generate_filter_placeholder'
        ],
        'rendering' => [
            'tr_render_smart_filters',
            'tr_render_smart_filter_field',
            'tr_get_filter_value',
            // All tr_render_*_input functions
        ],
        'query_application' => [
            'tr_apply_smart_filter_queries',
            'tr_apply_single_filter_query',
            'tr_is_empty_filter_value',
            // All tr_apply_*_query functions
        ],
        'utilities' => [
            'tr_build_input_attributes',
            'tr_render_static_select_options',
            'tr_render_callback_select_options',
            'tr_render_dynamic_select_options',
            'tr_format_option_text'
        ],
        'backwards_compatibility' => [
            'tr_render_bool_filter',
            'tr_render_number_filter',
            'tr_render_text_filter',
            'tr_render_select_filter_legacy'
        ]
    ];
}



/**
 * Apply multi-value query (IN clause)
 */
function tr_apply_multi_value_query(string $field, array $values, $model, array $config): void {
    if (empty($values)) {
        return;
    }
    
    if ($config['where_clause']) {
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        $query = str_replace('?', $placeholders, $config['where_clause']);
        $model->whereRaw($query, $values);
    } else {
        $model->whereIn($field, $values);
    }
}

/**
 * Apply text search query
 */
function tr_apply_text_search_query(string $field, string $value, $model, array $config): void {
    if ($config['where_clause']) {
        $model->whereRaw($config['where_clause'], ["%{$value}%"]);
    } else {
        $model->where($field, 'LIKE', "%{$value}%");
    }
}

/**
 * Apply numeric query with operator support
 */
function tr_apply_numeric_query(string $field, string $value, $model, array $config): void {
    $operator = $config['operator'] ?? $_GET[$field . '_operator'] ?? '=';
    
    $validOperators = ['=', '<', '>', '<=', '>=', '!='];
    if (!in_array($operator, $validOperators)) {
        $operator = '=';
    }
    
    if ($config['where_clause']) {
        $model->whereRaw($config['where_clause'], [$value]);
    } else {
        $model->where($field, $value);
    }
}



/**
 * UTILITY HELPERS
 */

/**
 * Convert field name to human-readable label
 */
function tr_humanize_field_name(string $field): string {
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
 * Infer resource name from model class
 */
function tr_infer_resource_name(string $modelClass): string {
    $parts = explode('\\', $modelClass);
    $className = end($parts);
    return strtolower($className);
}

/**
 * ENHANCED FORMATTER HELPERS
 */

/**
 * Create boolean badge formatter
 */
function tr_bool_formatter(): callable {
    return function ($value, $item) {
        $isActive = (bool) $value;
        $color = $isActive ? '#008000' : '#FF2C2C';
        $text = $isActive ? 'Yes' : 'No';
        return '<span style="color:' . $color . '; font-size: 16px; margin-right: 5px;">●</span>' . $text;
    };
}

/**
 * Create currency formatter
 */
function tr_currency_formatter(): callable {
    return function ($value, $item) {
        return '$' . number_format((float) $value, 2);
    };
}

/**
 * Create date formatter
 */
function tr_date_formatter(): callable {
    return function ($value, $item) {
        if (empty($value)) return 'N/A';
        return date('Y-m-d', strtotime($value));
    };
}

/**
 * Create email link formatter
 */
function tr_email_formatter(): callable {
    return function ($value, $item) {
        return '<a href="mailto:' . esc_attr($value) . '">' . esc_html($value) . '</a>';
    };
}

/**
 * Create URL link formatter
 */
function tr_url_formatter(): callable {
    return function ($value, $item) {
        return '<a href="' . esc_url($value) . '" target="_blank">' . esc_html($value) . '</a>';
    };
}