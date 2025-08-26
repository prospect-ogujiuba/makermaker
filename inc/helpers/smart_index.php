<?php

/**
 * Smart Index.php Recreation
 * 
 * This demonstrates how to use the TR_ModelInspector to automatically
 * recreate the complex index.php functionality with minimal configuration.
 */



// Initialize the model
$model = new \MakerMaker\Models\ServiceComplexity();

// Get comprehensive model information
$modelInfo = tr_model_info($model);

// Create the table with smart configuration
$table = tr_table($model);

// Set up bulk actions (same as original)
$table->setBulkActions(tr_form()->useConfirm(), [
    'Activate Addons' => 'activate_addons',
    'Deactivate Addons' => 'deactivate_addons',
    'Delete Selected' => 'delete_addons'
]);

// Auto-generate search columns from model inspection
$smartSearchColumns = [];
foreach ($modelInfo['searchable'] as $field => $config) {
    $smartSearchColumns[$field] = $config['column'];
}

// Add relationship fields to search if they exist
foreach ($modelInfo['relationships'] as $relationName => $relationConfig) {
    if ($relationConfig['type'] === 'belongsTo' && $relationConfig['display_in_index']) {
        $smartSearchColumns[$relationName] = ucwords(str_replace('_', ' ', $relationName));
    }
}

$table->setSearchColumns($smartSearchColumns);

// Auto-generate smart filters based on model inspection
$table->addSearchFormFilter(function () use ($modelInfo) {
    renderAdvancedSearchActions(strtolower($modelInfo['basic']['class_name']) . 's'); ?>

    <div class="tr-search-filters">
        <?php 
        // Generate filters automatically based on discovered filterable fields
        foreach ($modelInfo['filterable'] as $fieldName => $filterConfig): 
            switch ($filterConfig['type']):
                case 'select':
                    if ($filterConfig['options'] === 'relationship' && isset($filterConfig['relationship'])):
                        // Handle relationship filters (like Service filter in original)
                        $relationshipModel = $filterConfig['relationship']['model'];
                        if (class_exists($relationshipModel)):
        ?>
        <div class="tr-filter-group">
            <label><?= $filterConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName)); ?>:</label>
            <select name="<?= $fieldName; ?>" class="tr-filter">
                <option value="">All <?= $filterConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName)); ?>s</option>
                <?php
                $relatedItems = (new $relationshipModel)->get();
                foreach ($relatedItems as $item):
                    $selected = ($_GET[$fieldName] ?? '') == $item->getID() ? 'selected' : '';
                    $displayField = $this->findDisplayField($item);
                ?>
                    <option value="<?= $item->getID(); ?>" <?= $selected; ?>>
                        <?= esc_html($item->$displayField); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php 
                        endif;
                    else:
                        // Handle enum/status select fields
        ?>
        <div class="tr-filter-group">
            <label><?= $filterConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName)); ?>:</label>
            <select name="<?= $fieldName; ?>" class="tr-filter">
                <option value="">All <?= $filterConfig['label'] ?? 'Options'; ?></option>
                <?php if (is_array($filterConfig['options'])): ?>
                    <?php foreach ($filterConfig['options'] as $value => $label): ?>
                        <option value="<?= $value; ?>" <?= ($_GET[$fieldName] ?? '') === $value ? 'selected' : ''; ?>>
                            <?= esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Dynamic options would be loaded here -->
                    <?php $this->renderDynamicSelectOptions($fieldName, $filterConfig); ?>
                <?php endif; ?>
            </select>
        </div>
        <?php 
                    endif;
                    break;
                    
                case 'boolean':
        ?>
        <div class="tr-filter-group">
            <label><?= $filterConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName)); ?>:</label>
            <select name="<?= $fieldName; ?>" class="tr-filter">
                <option value="">All</option>
                <option value="1" <?= ($_GET[$fieldName] ?? '') === '1' ? 'selected' : ''; ?>>Yes</option>
                <option value="0" <?= ($_GET[$fieldName] ?? '') === '0' ? 'selected' : ''; ?>>No</option>
            </select>
        </div>
        <?php 
                    break;
                    
                case 'date_range':
        ?>
        <div class="tr-filter-group">
            <label><?= $filterConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName)); ?> From:</label>
            <input type="date" name="<?= $fieldName; ?>_from" class="tr-filter"
                value="<?php echo $_GET[$fieldName . '_from'] ?? ''; ?>">
        </div>

        <div class="tr-filter-group">
            <label><?= $filterConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName)); ?> To:</label>
            <input type="date" name="<?= $fieldName; ?>_to" class="tr-filter"
                value="<?php echo $_GET[$fieldName . '_to'] ?? ''; ?>">
        </div>
        <?php 
                    break;
                    
                case 'number_range':
        ?>
        <div class="tr-filter-group">
            <label><?= $filterConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName)); ?> Min:</label>
            <input type="number" name="<?= $fieldName; ?>_min" class="tr-filter"
                value="<?php echo $_GET[$fieldName . '_min'] ?? ''; ?>"
                placeholder="Min <?= $filterConfig['label'] ?? $fieldName; ?>">
        </div>

        <div class="tr-filter-group">
            <label><?= $filterConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName)); ?> Max:</label>
            <input type="number" name="<?= $fieldName; ?>_max" class="tr-filter"
                value="<?php echo $_GET[$fieldName . '_max'] ?? ''; ?>"
                placeholder="Max <?= $filterConfig['label'] ?? $fieldName; ?>">
        </div>
        <?php 
                    break;
                    
                default:
                    // Text search fields
        ?>
        <div class="tr-filter-group">
            <label><?= $filterConfig['label'] ?? ucwords(str_replace('_', ' ', $fieldName)); ?>:</label>
            <input type="text" name="<?= $fieldName; ?>" class="tr-filter"
                value="<?php echo $_GET[$fieldName] ?? ''; ?>"
                placeholder="Search <?= $filterConfig['label'] ?? $fieldName; ?>">
        </div>
        <?php 
                    break;
            endswitch;
        endforeach; 
        ?>
    </div>
    <?php
});

// Auto-generate model filters based on discovered filterable fields
$table->addSearchModelFilter(function ($args, $model, $table) use ($modelInfo) {
    foreach ($modelInfo['filterable'] as $fieldName => $filterConfig) {
        switch ($filterConfig['type']) {
            case 'select':
                if (!empty($_GET[$fieldName])) {
                    $model->where($fieldName, '=', $_GET[$fieldName]);
                }
                break;
                
            case 'boolean':
                if (isset($_GET[$fieldName]) && $_GET[$fieldName] !== '') {
                    $model->where($fieldName, '=', $_GET[$fieldName]);
                }
                break;
                
            case 'date_range':
                if (!empty($_GET[$fieldName . '_from'])) {
                    $model->where($fieldName, '>=', $_GET[$fieldName . '_from'] . ' 00:00:00');
                }
                if (!empty($_GET[$fieldName . '_to'])) {
                    $model->where($fieldName, '<=', $_GET[$fieldName . '_to'] . ' 23:59:59');
                }
                break;
                
            case 'number_range':
                if (!empty($_GET[$fieldName . '_min'])) {
                    $model->where($fieldName, '>=', $_GET[$fieldName . '_min']);
                }
                if (!empty($_GET[$fieldName . '_max'])) {
                    $model->where($fieldName, '<=', $_GET[$fieldName . '_max']);
                }
                break;
                
            default:
                // Text search
                if (!empty($_GET[$fieldName])) {
                    $model->where($fieldName, 'LIKE', '%' . $_GET[$fieldName] . '%');
                }
                break;
        }
    }
});

// Auto-generate smart table columns
$smartColumns = [];
foreach ($modelInfo['display']['columns'] as $columnName => $columnConfig) {
    $smartColumns[$columnName] = [
        'label' => $columnConfig['label'],
        'sort' => $columnConfig['sortable'],
        'actions' => $columnConfig['primary'] ? $modelInfo['display']['actions'] : false,
    ];
    
    // Add smart callbacks based on column type and format
    if (isset($columnConfig['format'])) {
        $smartColumns[$columnName]['callback'] = $this->generateSmartCallback($columnName, $columnConfig, $modelInfo);
    }
}

// Add relationship display columns
foreach ($modelInfo['relationships'] as $relationName => $relationConfig) {
    if ($relationConfig['display_in_index']) {
        $smartColumns[$relationName] = [
            'label' => ucwords(str_replace('_', ' ', $relationName)),
            'sort' => false,
            'callback' => function ($value, $item) use ($relationName) {
                $relation = $item->$relationName;
                if ($relation) {
                    $displayField = $this->findDisplayField($relation);
                    return $relation->$displayField ?? 'N/A';
                }
                return 'N/A';
            },
        ];
    }
}

$table->setColumns($smartColumns, $modelInfo['display']['primary_column']);

// Add smart sorting based on model analysis
if (isset($modelInfo['display']['primary_column'])) {
    $primaryColumn = $modelInfo['display']['primary_column'];
    $sortDirection = 'ASC';
    
    // Use smart default sorting for dates and IDs
    if (in_array($primaryColumn, ['created_at', 'updated_at', 'id'])) {
        $sortDirection = 'DESC';
    }
    
    $table->setOrder($primaryColumn, $sortDirection);
}

// Display the table
echo $table;

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
    $searchColumns = [];
    foreach ($modelInfo['searchable'] as $field => $config) {
        $searchColumns[$field] = $config['column'];
    }
    $table->setSearchColumns($searchColumns);
    
    // Set smart columns
    $columns = [];
    foreach ($modelInfo['display']['columns'] as $name => $config) {
        $columns[$name] = [
            'label' => $config['label'],
            'sort' => $config['sortable'],
            'actions' => $config['primary'] ? ['edit', 'delete'] : false,
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