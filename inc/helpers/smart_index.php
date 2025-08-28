<?php

/**
 * Smart Index.php Recreation
 * 
 * This demonstrates how to use the TR_ModelInspector to automatically
 * recreate the complex index.php functionality with minimal configuration.
 */





/**
 * Helper functions for the smart index
 */

// Find the best display field for a model/object
function findDisplayField($object): string {
    $preferredFields = ['name', 'title', 'subject', 'headline', 'label', 'display_name'];
    
    foreach ($preferredFields as $field) {
        if (property_exists($object, $field) && !empty($object->$field)) {
            return $field;
        }
    }
    
    // Fallback to ID
    return 'id';
}

// Generate smart callbacks based on column configuration
function generateSmartCallback(string $columnName, array $columnConfig, array $modelInfo): callable {
    $format = $columnConfig['format'];
    
    return match($format) {
        'boolean' => function ($value, $item) {
            $isActive = (bool) $value;
            $color = $isActive ? '#008000' : '#FF2C2C';
            $text = $isActive ? 'Yes' : 'No';
            return '<span style="color:' . $color . '; font-size: 16px; margin-right: 5px;">●</span>' . $text;
        },
        
        'currency' => function ($value, $item) {
            return '$' . number_format((float) $value, 2);
        },
        
        'date' => function ($value, $item) {
            if (empty($value)) return 'N/A';
            return date('Y-m-d', strtotime($value));
        },
        
        'email' => function ($value, $item) {
            return '<a href="mailto:' . esc_attr($value) . '">' . esc_html($value) . '</a>';
        },
        
        'url' => function ($value, $item) {
            return '<a href="' . esc_url($value) . '" target="_blank">' . esc_html($value) . '</a>';
        },
        
        'image' => function ($value, $item) {
            if (empty($value)) return 'N/A';
            return '<img src="' . esc_url($value) . '" style="max-width: 50px; max-height: 50px;" />';
        },
        
        default => function ($value, $item) use ($columnName, $modelInfo) {
            // Smart status indicator for primary columns
            if ($columnName === $modelInfo['display']['primary_column']) {
                $statusField = null;
                
                // Look for status indicators
                foreach (['is_active', 'status', 'is_featured', 'is_published'] as $field) {
                    if (isset($item->$field)) {
                        $statusField = $field;
                        break;
                    }
                }
                
                if ($statusField) {
                    $statusValue = $item->$statusField;
                    $statusColor = match($statusValue) {
                        '1', 1, true, 'active', 'published' => '#008000',
                        '0', 0, false, 'inactive', 'draft' => '#FF2C2C',
                        default => '#FFA500'
                    };
                    
                    $statusDot = '<span style="color:' . $statusColor . '; font-size: 16px; margin-right: 5px;">●</span>';
                    
                    // Add featured star if applicable
                    $featured = '';
                    if (isset($item->is_featured) && $item->is_featured) {
                        $featured = ' <span style="color:#FFB800; font-size: 14px;">★</span>';
                    }
                    
                    return $statusDot . $value . $featured;
                }
            }
            
            return $value;
        }
    };
}

// Render dynamic select options for enum fields
function renderDynamicSelectOptions(string $fieldName, array $filterConfig): void {
    // This would query the database to get unique values for the field
    global $wpdb;
    
    $modelInfo = $GLOBALS['modelInfo'] ?? null;
    if (!$modelInfo) return;
    
    $tableName = $modelInfo['basic']['table_name'];
    
    try {
        $results = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT %i FROM %i WHERE %i IS NOT NULL AND %i != '' ORDER BY %i",
            $fieldName, $tableName, $fieldName, $fieldName, $fieldName
        ));
        
        foreach ($results as $value) {
            $selected = ($_GET[$fieldName] ?? '') === $value ? 'selected' : '';
            echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html(ucwords(str_replace('_', ' ', $value))) . '</option>';
        }
    } catch (Exception $e) {
        // Handle error silently or log it
        error_log('Failed to load dynamic options for ' . $fieldName . ': ' . $e->getMessage());
    }
}

/**
 * Even Smarter Version: One-liner index
 * 
 * For the ultimate in simplicity, you could create a single function that does everything:
 */

function tr_smart_index(string $modelClass, array $overrides = []): void {
    $model = new $modelClass();
    $modelInfo = tr_model_info($model);
    
    $table = tr_table($model);
    
    // Apply smart configuration
    tr_apply_smart_configuration($table, $modelInfo, $overrides);
    
    echo $table;
}

function tr_apply_smart_configuration($table, array $modelInfo, array $overrides = []): void {
    // Set up bulk actions if not overridden
    if (!isset($overrides['bulk_actions']) && isset($overrides['enable_bulk_actions'])) {
        $table->setBulkActions(tr_form()->useConfirm(), [
            'Delete Selected' => 'delete_selected',
        ]);
    }
    
    // Set search columns
    // $searchColumns = [];
    // foreach ($modelInfo['searchable'] as $field => $config) {
    //     $searchColumns[$field] = $config['column'];
    // }
    // $table->setSearchColumns($searchColumns);
    
    // Set smart columns
    $columns = [];
    foreach ($modelInfo['display']['columns'] as $name => $config) {
        $columns[$name] = [
            'label' => $config['label'],
            'sort' => $config['sortable'],
            'actions' => $config['primary'] ? ['edit', 'view', 'delete'] : false,
        ];
    }
    $table->setColumns($columns, $modelInfo['display']['primary_column']);
    
    // // Add smart filters and model filters
    // tr_add_smart_filters($table, $modelInfo);
    // tr_add_smart_model_filters($table, $modelInfo);
    
    // Apply any overrides
    foreach ($overrides as $method => $params) {
        if (method_exists($table, $method)) {
            call_user_func_array([$table, $method], (array) $params);
        }
    }
}

/**
 * Usage Examples:
 * 
 * // Ultra-simple version
 * tr_smart_index(\MakerMaker\Models\ServiceAddon::class);
 * 
 * // With customizations
 * tr_smart_index(\MakerMaker\Models\ServiceAddon::class, [
 *     'enable_bulk_actions' => true,
 *     'setLimit' => 50,
 *     'setOrder' => ['created_at', 'DESC']
 * ]);
 */