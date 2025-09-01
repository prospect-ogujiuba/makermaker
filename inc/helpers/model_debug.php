<?php
/**
 * Comprehensive TypeRocket Model Debug Function
 * 
 * This function provides detailed debugging information for TypeRocket Models,
 * inspecting properties, methods, relationships, queries, and internal state.
 */

function getComprehensiveFieldInfo($model)
{
    if (!$model) {
        return [];
    }

    $reflection = new ReflectionClass($model);
    $fieldInfo = [];

    // Helper function to safely get protected property values
    $getProperty = function($object, $propertyName, $default = []) {
        try {
            $reflection = new ReflectionClass($object);
            if ($reflection->hasProperty($propertyName)) {
                $property = $reflection->getProperty($propertyName);
                $property->setAccessible(true);
                return $property->getValue($object) ?? $default;
            }
            return $default;
        } catch (ReflectionException $e) {
            return $default;
        }
    };

    // Get all field configuration arrays
    $fillable = $getProperty($model, 'fillable', []);
    $guard = $getProperty($model, 'guard', []);
    $builtin = $getProperty($model, 'builtin', []);
    $metaless = $getProperty($model, 'metaless', []);
    $private = $getProperty($model, 'private', []);
    $cast = $getProperty($model, 'cast', []);
    $format = $getProperty($model, 'format', []);
    $static = $getProperty($model, 'static', []);
    $restMetaFields = $getProperty($model, 'restMetaFields', []);
    $fieldOptions = $getProperty($model, 'fieldOptions', []);

    // Get current model properties
    $properties = method_exists($model, 'getProperties') ? $model->getProperties() : [];

    // Collect all unique field names from all sources
    $allFields = array_unique(array_merge(
        $fillable,
        $guard,
        $builtin,
        $metaless,
        $private,
        array_keys($cast),
        array_keys($format),
        array_keys($static),
        array_keys($restMetaFields),
        array_keys($fieldOptions),
        array_keys($properties)
    ));

    // Build comprehensive info for each field
    foreach ($allFields as $fieldName) {
        if (empty($fieldName)) continue;

        $fieldInfo[$fieldName] = [
            // Basic field identification
            'name' => $fieldName,
            
            // Field classification
            'is_fillable' => in_array($fieldName, $fillable),
            'is_guarded' => in_array($fieldName, $guard),
            'is_builtin' => in_array($fieldName, $builtin),
            'is_metaless' => in_array($fieldName, $metaless),
            'is_private' => in_array($fieldName, $private),
            
            // Type information
            'cast_type' => $cast[$fieldName] ?? null,
            'has_formatting' => isset($format[$fieldName]),
            'format_callback' => $format[$fieldName] ?? null,
            
            // Values
            'current_value' => $properties[$fieldName] ?? null,
            'static_value' => $static[$fieldName] ?? null,
            'has_static_value' => isset($static[$fieldName]),
            
            // REST API configuration
            'rest_meta_config' => $restMetaFields[$fieldName] ?? null,
            'rest_accessible' => !in_array($fieldName, $private),
            
            // Field options (TypeRocket specific)
            'field_options' => $fieldOptions[$fieldName] ?? null,
            
            // Computed properties
            'is_mass_assignable' => in_array($fieldName, $fillable) && !in_array($fieldName, $guard),
            'is_database_column' => in_array($fieldName, $builtin),
            'is_meta_field' => !in_array($fieldName, $builtin) && !in_array($fieldName, $metaless),
            'protection_level' => in_array($fieldName, $guard) ? 'guarded' : (in_array($fieldName, $fillable) ? 'fillable' : 'default'),
            
            // Data type inference
            'inferred_type' => inferFieldType($fieldName, $properties[$fieldName] ?? null, $cast[$fieldName] ?? null)
        ];
    }

    return $fieldInfo;
}

/**
 * Infer field type from various sources
 */
function inferFieldType($fieldName, $currentValue, $castType)
{
    // If cast type is explicitly set, use that
    if ($castType) {
        return $castType;
    }

    // Infer from current value
    if ($currentValue !== null) {
        $type = gettype($currentValue);
        if ($type === 'object') {
            return get_class($currentValue);
        }
        return $type;
    }

    // Common field name patterns
    $patterns = [
        '/^(id|_id)$/' => 'integer',
        '/^(created_at|updated_at|deleted_at)$/' => 'datetime',
        '/_at$/' => 'datetime',
        '/^(email|mail)$/' => 'email',
        '/^(url|link)$/' => 'url',
        '/^(password|pass)$/' => 'password',
        '/^(slug|handle)$/' => 'slug',
        '/^(status|state)$/' => 'string',
        '/^(count|total|amount|price|cost)$/' => 'numeric',
        '/^(is_|has_|can_|should_)/' => 'boolean',
        '/_count$/' => 'integer',
        '/_json$/' => 'json',
        '/_array$/' => 'array',
    ];

    foreach ($patterns as $pattern => $type) {
        if (preg_match($pattern, $fieldName)) {
            return $type;
        }
    }

    return 'string'; // Default fallback
}

/**
 * Debug output specifically for field information
 */
function debugModelFieldInfo($model)
{
    if (!defined('WP_DEBUG') || !WP_DEBUG) return;
    
    $fieldInfo = getComprehensiveFieldInfo($model);
    
    if (empty($fieldInfo)) {
        echo '<div style="color: #dc3545; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">No field information available</div>';
        return;
    }
    
    echo '<div style="background: #e8f4fd; border: 1px solid #2196f3; border-radius: 4px; padding: 15px; margin: 10px 0;">';
    echo '<h4 style="margin: 0 0 15px 0; color: #2196f3;">📊 Comprehensive Field Information</h4>';
    
    echo '<div style="overflow-x: auto;">';
    echo '<table style="width: 100%; border-collapse: collapse; font-size: 12px; background: white;">';
    
    // Table header
    echo '<thead><tr style="background: #f8f9fa;">';
    echo '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Field Name</th>';
    echo '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Type</th>';
    echo '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Storage</th>';
    echo '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Protection</th>';
    echo '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Features</th>';
    echo '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Current Value</th>';
    echo '</tr></thead>';
    
    echo '<tbody>';
    foreach ($fieldInfo as $field) {
        echo '<tr>';
        
        // Field Name
        echo '<td style="border: 1px solid #dee2e6; padding: 8px; font-weight: bold;">';
        echo htmlspecialchars($field['name']);
        echo '</td>';
        
        // Type
        echo '<td style="border: 1px solid #dee2e6; padding: 8px;">';
        echo '<span style="background: #e3f2fd; padding: 2px 6px; border-radius: 3px; font-size: 11px;">';
        echo htmlspecialchars($field['inferred_type']);
        echo '</span>';
        if ($field['cast_type']) {
            echo '<br><small style="color: #666;">Cast: ' . htmlspecialchars($field['cast_type']) . '</small>';
        }
        echo '</td>';
        
        // Storage
        echo '<td style="border: 1px solid #dee2e6; padding: 8px;">';
        if ($field['is_database_column']) {
            echo '<span style="color: #6f42c1;">🗄️ Database</span>';
        } elseif ($field['is_meta_field']) {
            echo '<span style="color: #17a2b8;">📝 Meta</span>';
        } else {
            echo '<span style="color: #fd7e14;">⚠️ Meta-less</span>';
        }
        echo '</td>';
        
        // Protection
        echo '<td style="border: 1px solid #dee2e6; padding: 8px;">';
        if ($field['is_mass_assignable']) {
            echo '<span style="color: #28a745;">✅ Fillable</span>';
        } elseif ($field['is_guarded']) {
            echo '<span style="color: #dc3545;">🛡️ Guarded</span>';
        } else {
            echo '<span style="color: #6c757d;">➖ Default</span>';
        }
        if ($field['is_private']) {
            echo '<br><small style="color: #666;">🔒 Private</small>';
        }
        echo '</td>';
        
        // Features
        echo '<td style="border: 1px solid #dee2e6; padding: 8px;">';
        $features = [];
        if ($field['has_formatting']) $features[] = '🎨 Format';
        if ($field['has_static_value']) $features[] = '📌 Static';
        if ($field['field_options']) $features[] = '⚙️ Options';
        if ($field['rest_accessible']) $features[] = '🌐 REST';
        
        echo implode('<br>', $features) ?: '—';
        echo '</td>';
        
        // Current Value
        echo '<td style="border: 1px solid #dee2e6; padding: 8px;">';
        if ($field['current_value'] !== null) {
            $displayValue = $field['current_value'];
            if (is_array($displayValue) || is_object($displayValue)) {
                $displayValue = json_encode($displayValue, JSON_PRETTY_PRINT);
                if (strlen($displayValue) > 100) {
                    $displayValue = substr($displayValue, 0, 100) . '...';
                }
            } else {
                $displayValue = (string)$displayValue;
                if (strlen($displayValue) > 50) {
                    $displayValue = substr($displayValue, 0, 50) . '...';
                }
            }
            echo '<code style="background: #f8f9fa; padding: 2px 4px; border-radius: 2px; font-size: 10px;">';
            echo htmlspecialchars($displayValue);
            echo '</code>';
        } elseif ($field['has_static_value']) {
            echo '<em style="color: #6c757d;">Static: ' . htmlspecialchars((string)$field['static_value']) . '</em>';
        } else {
            echo '<em style="color: #6c757d;">null</em>';
        }
        echo '</td>';
        
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '</div>';
}

/**
 * Get field information as structured array for API/export usage
 */
function getFieldInfoArray($model)
{
    $fieldInfo = getComprehensiveFieldInfo($model);
    
    // Group fields by type for better organization
    $grouped = [
        'database_fields' => [],
        'meta_fields' => [],
        'metaless_fields' => [],
        'fillable_fields' => [],
        'guarded_fields' => [],
        'private_fields' => [],
        'cast_fields' => [],
        'formatted_fields' => [],
        'static_fields' => []
    ];
    
    foreach ($fieldInfo as $fieldName => $info) {
        if ($info['is_database_column']) {
            $grouped['database_fields'][] = $fieldName;
        }
        if ($info['is_meta_field']) {
            $grouped['meta_fields'][] = $fieldName;
        }
        if ($info['is_metaless']) {
            $grouped['metaless_fields'][] = $fieldName;
        }
        if ($info['is_fillable']) {
            $grouped['fillable_fields'][] = $fieldName;
        }
        if ($info['is_guarded']) {
            $grouped['guarded_fields'][] = $fieldName;
        }
        if ($info['is_private']) {
            $grouped['private_fields'][] = $fieldName;
        }
        if ($info['cast_type']) {
            $grouped['cast_fields'][$fieldName] = $info['cast_type'];
        }
        if ($info['has_formatting']) {
            $grouped['formatted_fields'][] = $fieldName;
        }
        if ($info['has_static_value']) {
            $grouped['static_fields'][$fieldName] = $info['static_value'];
        }
    }
    
    return [
        'all_fields' => $fieldInfo,
        'grouped_fields' => $grouped,
        'field_count' => count($fieldInfo)
    ];
}

function debugModel($form, $expanded = false)
{
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $startTime = microtime(true);
        
        echo '<div style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px solid #dee2e6; border-radius: 8px; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">';
        
        echo '<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">';
        echo '<h3 style="margin: 0; color: #495057; font-size: 24px; font-weight: 600;">🔍 TypeRocket Model Debug Console</h3>';
        echo '<div style="font-size: 12px; color: #6c757d;">Debug Level: ' . ($expanded ? 'Full' : 'Standard') . '</div>';
        echo '</div>';

        // Get the model instance from the form
        $model = $form->getModel();
        if (!$model) {
            echo '<div style="color: #dc3545; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">No model found in form instance</div></div>';
            return;
        }

        // Create reflection to access protected properties
        $reflection = new ReflectionClass($model);

        /**
         * Helper function to safely get protected property values
         */
        function getProtectedProperty($object, $propertyName, $default = null)
        {
            try {
                $reflection = new ReflectionClass($object);
                if ($reflection->hasProperty($propertyName)) {
                    $property = $reflection->getProperty($propertyName);
                    $property->setAccessible(true);
                    return $property->getValue($object);
                }
                return $default;
            } catch (ReflectionException $e) {
                return $default;
            }
        }

        /**
         * Helper function to format complex values
         */
        function formatComplexValue($value, $depth = 0, $maxDepth = 3)
        {
            if ($depth > $maxDepth) {
                return '<em style="color: #6c757d;">[max depth reached]</em>';
            }

            if (is_null($value)) {
                return '<em style="color: #6c757d;">null</em>';
            } elseif (is_bool($value)) {
                return $value ? '<span style="color: #28a745;">true</span>' : '<span style="color: #dc3545;">false</span>';
            } elseif (is_string($value)) {
                if (strlen($value) > 200) {
                    return '<span style="color: #007bff;">"' . htmlspecialchars(substr($value, 0, 200)) . '..."</span> <small>(' . strlen($value) . ' chars)</small>';
                }
                return '<span style="color: #007bff;">"' . htmlspecialchars($value) . '"</span>';
            } elseif (is_numeric($value)) {
                return '<span style="color: #fd7e14;">' . $value . '</span>';
            } elseif (is_array($value)) {
                if (empty($value)) {
                    return '<span style="color: #6c757d;">[]</span>';
                }
                $output = '<details style="margin: 2px 0;"><summary style="cursor: pointer; color: #17a2b8;">Array (' . count($value) . ' items)</summary>';
                $output .= '<div style="margin-left: 20px; border-left: 2px solid #e9ecef; padding-left: 10px;">';
                foreach ($value as $k => $v) {
                    $output .= '<div style="margin: 2px 0;"><strong>' . htmlspecialchars($k) . ':</strong> ' . formatComplexValue($v, $depth + 1, $maxDepth) . '</div>';
                }
                $output .= '</div></details>';
                return $output;
            } elseif (is_object($value)) {
                $className = get_class($value);
                return '<span style="color: #6f42c1;">' . $className . '</span> <small>object</small>';
            }

            return '<code>' . htmlspecialchars(print_r($value, true)) . '</code>';
        }

        /**
         * Get all relationship methods from the model
         */
        function getAllRelationshipMethods($model)
        {
            $reflection = new ReflectionClass($model);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            $relationships = [];
            $excludePatterns = [
                '/^__/', '/^get[A-Z]/', '/^set[A-Z]/', '/^is[A-Z]/', '/^has[A-Z]/',
                '/^append/', '/^remove/', '/^extend/', '/^unlock/', '/^save/',
                '/^create/', '/^update/', '/^delete/', '/^find/', '/^where/',
                '/^order/', '/^take/', '/^first/', '/^count/', '/^sum/', '/^min/',
                '/^max/', '/^avg/', '/^select/', '/^join/', '/^with/', '/^load/',
                '/^cast/', '/^provision/', '/^format/', '/^init/', '/^establish/',
                '/^setup/', '/^mutate/', '/^compose/', '/^clone/', '/^to[A-Z]/',
                '/^json/', '/^when/', '/^on[A-Z]/', '/^group/', '/^reorder/',
                '/^paginate/', '/^new$/'
            ];

            foreach ($methods as $method) {
                $methodName = $method->getName();
                
                // Skip if matches exclude patterns
                $skip = false;
                foreach ($excludePatterns as $pattern) {
                    if (preg_match($pattern, $methodName)) {
                        $skip = true;
                        break;
                    }
                }
                
                if ($skip || $method->getNumberOfRequiredParameters() > 0) {
                    continue;
                }

                // Check if method name looks like a relationship
                if (preg_match('/^[a-z][a-zA-Z]*$/', $methodName) && strlen($methodName) > 2) {
                    try {
                        // Try to call the method to see if it returns a Model
                        $result = $method->invoke($model);
                        if ($result instanceof \TypeRocket\Models\Model) {
                            $relationships[$methodName] = get_class($result);
                        }
                    } catch (Exception $e) {
                        // Method might be a relationship but requires parameters or has other issues
                        $relationships[$methodName] = 'Unknown (Error: ' . $e->getMessage() . ')';
                    }
                }
            }

            return $relationships;
        }

        /**
         * Get model configuration summary
         */
        function getModelConfigSummary($model)
        {
            $config = [];
            
            // Basic properties
            $props = [
                'fillable', 'guard', 'builtin', 'metaless', 'private', 'cast', 'static',
                'format', 'restMetaFields', 'with', 'fieldOptions', 'explicitProperties',
                'closed', 'cache', 'connection', 'table', 'resource', 'routeResource',
                'idColumn', 'resultsClass', 'dataCache'
            ];
            
            foreach ($props as $prop) {
                $value = getProtectedProperty($model, $prop);
                if ($value !== null) {
                    $config[$prop] = $value;
                }
            }
            
            return $config;
        }

        // === MAIN DEBUG OUTPUT ===

        // Model Identity Section
        echo '<div style="background: linear-gradient(45deg, #28a745, #20c997); color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px;">';
        echo '<h4 style="margin: 0 0 10px 0; font-size: 18px;">📋 Model Identity</h4>';
        
        $className = $reflection->getShortName();
        $fullClassName = get_class($model);
        $tableName = method_exists($model, 'getTable') ? $model->getTable() : 'N/A';
        $idColumn = method_exists($model, 'getIdColumn') ? $model->getIdColumn() : 'N/A';
        $connection = method_exists($model, 'getConnection') ? ($model->getConnection() ?: 'default') : 'default';
        $resource = getProtectedProperty($model, 'resource', 'N/A');
        $routeResource = getProtectedProperty($model, 'routeResource');
        
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; font-size: 14px;">';
        echo '<div><strong>Class:</strong> ' . $className . '</div>';
        echo '<div><strong>Table:</strong> ' . $tableName . '</div>';
        echo '<div><strong>Resource:</strong> ' . $resource . '</div>';
        echo '<div><strong>Route Resource:</strong> ' . ($routeResource ?: 'Auto-generated') . '</div>';
        echo '<div><strong>ID Column:</strong> ' . $idColumn . '</div>';
        echo '<div><strong>Connection:</strong> ' . $connection . '</div>';
        echo '</div>';
        echo '</div>';

        // Current Model State
        echo '<div style="background: white; border: 2px solid #6f42c1; border-radius: 6px; padding: 15px; margin-bottom: 20px;">';
        echo '<h4 style="margin: 0 0 15px 0; color: #6f42c1; font-size: 18px;">💾 Current Model State</h4>';
        
        $properties = method_exists($model, 'getProperties') ? $model->getProperties() : [];
        $propertiesUnaltered = getProtectedProperty($model, 'propertiesUnaltered', []);
        $explicitProperties = getProtectedProperty($model, 'explicitProperties', []);
        $modelId = method_exists($model, 'getID') ? $model->getID() : null;
        $hasProperties = method_exists($model, 'hasProperties') ? $model->hasProperties() : false;
        
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
        
        // Current Properties
        echo '<div>';
        echo '<h5 style="margin: 0 0 10px 0; color: #495057;">Current Properties</h5>';
        echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px; max-height: 300px; overflow-y: auto;">';
        echo '<div style="margin-bottom: 10px;"><strong>ID:</strong> ' . ($modelId ? formatComplexValue($modelId) : '<em style="color: #6c757d;">New Record</em>') . '</div>';
        echo '<div style="margin-bottom: 10px;"><strong>Has Properties:</strong> ' . ($hasProperties ? '<span style="color: #28a745;">Yes</span>' : '<span style="color: #dc3545;">No</span>') . '</div>';
        
        if (!empty($properties)) {
            foreach ($properties as $key => $value) {
                echo '<div style="margin: 5px 0; padding: 5px; background: white; border-radius: 3px;">';
                echo '<strong>' . htmlspecialchars($key) . ':</strong> ' . formatComplexValue($value);
                echo '</div>';
            }
        } else {
            echo '<em style="color: #6c757d;">No properties loaded</em>';
        }
        echo '</div></div>';
        
        // Unaltered Properties (if different)
        echo '<div>';
        echo '<h5 style="margin: 0 0 10px 0; color: #495057;">Original Properties</h5>';
        echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px; max-height: 300px; overflow-y: auto;">';
        
        if (!empty($propertiesUnaltered)) {
            foreach ($propertiesUnaltered as $key => $value) {
                $changed = isset($properties[$key]) && $properties[$key] !== $value;
                $style = $changed ? 'background: #fff3cd; border: 1px solid #ffeaa7;' : 'background: white;';
                echo '<div style="margin: 5px 0; padding: 5px; border-radius: 3px; ' . $style . '">';
                echo '<strong>' . htmlspecialchars($key) . ':</strong> ' . formatComplexValue($value);
                if ($changed) {
                    echo ' <small style="color: #856404;">(modified)</small>';
                }
                echo '</div>';
            }
        } else {
            echo '<em style="color: #6c757d;">No original properties</em>';
        }
        echo '</div></div>';
        echo '</div>';
        echo '</div>';

        // Field Configuration
        echo '<div style="background: white; border: 2px solid #007bff; border-radius: 6px; padding: 15px; margin-bottom: 20px;">';
        echo '<h4 style="margin: 0 0 15px 0; color: #007bff; font-size: 18px;">⚙️ Field Configuration</h4>';
        
        $fillable = getProtectedProperty($model, 'fillable', []);
        $guard = getProtectedProperty($model, 'guard', []);
        $builtin = getProtectedProperty($model, 'builtin', []);
        $metaless = getProtectedProperty($model, 'metaless', []);
        $private = getProtectedProperty($model, 'private', []);
        $cast = getProtectedProperty($model, 'cast', []);
        $format = getProtectedProperty($model, 'format', []);
        $static = getProtectedProperty($model, 'static', []);
        
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">';
        
        $fieldConfigs = [
            'Fillable Fields' => ['data' => $fillable, 'color' => '#28a745', 'desc' => 'Fields that can be mass assigned'],
            'Guarded Fields' => ['data' => $guard, 'color' => '#dc3545', 'desc' => 'Fields protected from mass assignment'],
            'Built-in Fields' => ['data' => $builtin, 'color' => '#6f42c1', 'desc' => 'Fields that are database columns, not meta'],
            'Meta-less Fields' => ['data' => $metaless, 'color' => '#fd7e14', 'desc' => 'Fields that should not be saved as meta'],
            'Private Fields' => ['data' => $private, 'color' => '#495057', 'desc' => 'Fields hidden from REST API'],
            'Cast Fields' => ['data' => $cast, 'color' => '#20c997', 'desc' => 'Fields with type casting rules'],
            'Format Fields' => ['data' => $format, 'color' => '#e83e8c', 'desc' => 'Fields with formatting functions'],
            'Static Fields' => ['data' => $static, 'color' => '#6c757d', 'desc' => 'Fields with static default values']
        ];
        
        foreach ($fieldConfigs as $title => $config) {
            echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 12px;">';
            echo '<h6 style="margin: 0 0 8px 0; color: ' . $config['color'] . '; font-size: 14px;">' . $title . '</h6>';
            echo '<div style="font-size: 11px; color: #6c757d; margin-bottom: 8px;">' . $config['desc'] . '</div>';
            
            if (empty($config['data'])) {
                echo '<em style="color: #6c757d;">None defined</em>';
            } else {
                echo '<div style="max-height: 150px; overflow-y: auto;">';
                echo formatComplexValue($config['data']);
                echo '</div>';
            }
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';

        // Relationships Section
        if ($expanded) {
            echo '<div style="background: white; border: 2px solid #fd7e14; border-radius: 6px; padding: 15px; margin-bottom: 20px;">';
            echo '<h4 style="margin: 0 0 15px 0; color: #fd7e14; font-size: 18px;">🔗 Relationships</h4>';
            
            $relationships = getAllRelationshipMethods($model);
            $with = getProtectedProperty($model, 'with', []);
            $currentRelationships = method_exists($model, 'getRelationships') ? $model->getRelationships() : [];
            $relatedBy = getProtectedProperty($model, 'relatedBy');
            $junction = getProtectedProperty($model, 'junction');
            
            echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
            
            // Detected Methods
            echo '<div>';
            echo '<h5 style="margin: 0 0 10px 0; color: #495057;">Detected Relationship Methods</h5>';
            echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px; max-height: 200px; overflow-y: auto;">';
            if (!empty($relationships)) {
                foreach ($relationships as $method => $returns) {
                    echo '<div style="margin: 3px 0; padding: 4px 8px; background: white; border-radius: 3px; display: flex; justify-content: space-between;">';
                    echo '<code style="color: #e83e8c;">' . $method . '()</code>';
                    echo '<small style="color: #6c757d;">' . basename($returns) . '</small>';
                    echo '</div>';
                }
            } else {
                echo '<em style="color: #6c757d;">No relationships detected</em>';
            }
            echo '</div></div>';
            
            // Eager Loading & Current
            echo '<div>';
            echo '<h5 style="margin: 0 0 10px 0; color: #495057;">Eager Loading & Current</h5>';
            echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px;">';
            
            echo '<div style="margin-bottom: 15px;">';
            echo '<strong>With (Eager Loading):</strong><br>';
            if (!empty($with)) {
                echo formatComplexValue($with);
            } else {
                echo '<em style="color: #6c757d;">No eager loading configured</em>';
            }
            echo '</div>';
            
            echo '<div style="margin-bottom: 15px;">';
            echo '<strong>Loaded Relationships:</strong><br>';
            if (!empty($currentRelationships)) {
                echo formatComplexValue($currentRelationships);
            } else {
                echo '<em style="color: #6c757d;">No relationships currently loaded</em>';
            }
            echo '</div>';
            
            if ($relatedBy) {
                echo '<div style="margin-bottom: 10px;">';
                echo '<strong>Related By:</strong><br>';
                echo formatComplexValue($relatedBy);
                echo '</div>';
            }
            
            if ($junction) {
                echo '<div>';
                echo '<strong>Junction Table:</strong><br>';
                echo formatComplexValue($junction);
                echo '</div>';
            }
            
            echo '</div></div>';
            echo '</div>';
            echo '</div>';
        }

        // Query Information
        echo '<div style="background: white; border: 2px solid #17a2b8; border-radius: 6px; padding: 15px; margin-bottom: 20px;">';
        echo '<h4 style="margin: 0 0 15px 0; color: #17a2b8; font-size: 18px;">📊 Query Information</h4>';
        
        $query = getProtectedProperty($model, 'query');
        $resultsClass = getProtectedProperty($model, 'resultsClass', 'N/A');
        
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
        
        // Query Object Info
        echo '<div>';
        echo '<h5 style="margin: 0 0 10px 0; color: #495057;">Query Object</h5>';
        echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px;">';
        
        if ($query) {
            echo '<div><strong>Query Class:</strong> ' . get_class($query) . '</div>';
            echo '<div><strong>Results Class:</strong> ' . basename($resultsClass) . '</div>';
            
            if (method_exists($model, 'getSuspectSQL')) {
                $lastSQL = $model->getSuspectSQL();
                if ($lastSQL) {
                    echo '<div style="margin-top: 10px;"><strong>Last SQL Query:</strong></div>';
                    echo '<pre style="background: #343a40; color: #f8f9fa; padding: 8px; border-radius: 3px; font-size: 11px; overflow-x: auto; margin: 5px 0;">';
                    echo htmlspecialchars($lastSQL);
                    echo '</pre>';
                }
            }
        } else {
            echo '<em style="color: #6c757d;">No query object available</em>';
        }
        echo '</div></div>';
        
        // Additional Query Info
        echo '<div>';
        echo '<h5 style="margin: 0 0 10px 0; color: #495057;">Query Configuration</h5>';
        echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px;">';
        
        $cache = getProtectedProperty($model, 'cache', true);
        $dataCache = getProtectedProperty($model, 'dataCache', []);
        $closed = getProtectedProperty($model, 'closed', false);
        
        echo '<div><strong>Cache Enabled:</strong> ' . ($cache ? '<span style="color: #28a745;">Yes</span>' : '<span style="color: #dc3545;">No</span>') . '</div>';
        echo '<div><strong>Model Closed:</strong> ' . ($closed ? '<span style="color: #dc3545;">Yes</span>' : '<span style="color: #28a745;">No</span>') . '</div>';
        echo '<div><strong>Data Cache Items:</strong> ' . count($dataCache) . '</div>';
        
        if (!empty($dataCache) && $expanded) {
            echo '<div style="margin-top: 10px;"><strong>Cached Data:</strong></div>';
            echo '<div style="max-height: 100px; overflow-y: auto;">';
            echo formatComplexValue($dataCache);
            echo '</div>';
        }
        
        echo '</div></div>';
        echo '</div>';
        echo '</div>';

        // Model Methods (if expanded)
        if ($expanded) {
            echo '<div style="background: white; border: 2px solid #6c757d; border-radius: 6px; padding: 15px; margin-bottom: 20px;">';
            echo '<h4 style="margin: 0 0 15px 0; color: #6c757d; font-size: 18px;">🔧 Available Methods</h4>';
            
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            $categorizedMethods = [
                'Query Methods' => [],
                'Relationship Methods' => [],
                'Property Methods' => [],
                'Utility Methods' => [],
                'Other Methods' => []
            ];
            
            foreach ($methods as $method) {
                $name = $method->getName();
                $params = $method->getNumberOfParameters();
                $required = $method->getNumberOfRequiredParameters();
                
                $signature = $name . '(' . ($params > 0 ? '...' : '') . ')';
                
                if (preg_match('/^(where|find|get|first|take|order|group|select|join|count|sum|min|max|avg)/', $name)) {
                    $categorizedMethods['Query Methods'][] = $signature;
                } elseif (preg_match('/^(has|belongs|with|load|attach|detach|sync)/', $name)) {
                    $categorizedMethods['Relationship Methods'][] = $signature;
                } elseif (preg_match('/^(get|set|append|remove|extend|unlock|cast|format|provision)/', $name)) {
                    $categorizedMethods['Property Methods'][] = $signature;
                } elseif (preg_match('/^(save|create|update|delete|clone|to|json|when|can)/', $name)) {
                    $categorizedMethods['Utility Methods'][] = $signature;
                } else {
                    $categorizedMethods['Other Methods'][] = $signature;
                }
            }
            
            echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">';
            
            foreach ($categorizedMethods as $category => $methods) {
                if (empty($methods)) continue;
                
                echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px;">';
                echo '<h6 style="margin: 0 0 8px 0; color: #495057; font-size: 13px;">' . $category . ' (' . count($methods) . ')</h6>';
                echo '<div style="max-height: 200px; overflow-y: auto; font-size: 11px;">';
                
                sort($methods);
                foreach ($methods as $method) {
                    echo '<div style="margin: 1px 0; padding: 2px 4px; background: white; border-radius: 2px;">';
                    echo '<code style="color: #6f42c1;">' . htmlspecialchars($method) . '</code>';
                    echo '</div>';
                }
                
                echo '</div></div>';
            }
            
            echo '</div>';
            echo '</div>';
        }

        // Form Integration
        echo '<div style="background: white; border: 2px solid #e83e8c; border-radius: 6px; padding: 15px; margin-bottom: 20px;">';
        echo '<h4 style="margin: 0 0 15px 0; color: #e83e8c; font-size: 18px;">📝 Form Integration</h4>';
        
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
        
        // Form Information
        echo '<div>';
        echo '<h5 style="margin: 0 0 10px 0; color: #495057;">Form Details</h5>';
        echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px;">';
        
        echo '<div><strong>Form Class:</strong> ' . get_class($form) . '</div>';
        echo '<div><strong>Model Connected:</strong> ' . (method_exists($form, 'getModel') && $form->getModel() ? '<span style="color: #28a745;">Yes</span>' : '<span style="color: #dc3545;">No</span>') . '</div>';
        
        if (method_exists($form, 'getAction')) {
            echo '<div><strong>Form Action:</strong> ' . ($form->getAction() ?: 'Not Set') . '</div>';
        }
        
        if (method_exists($form, 'getItemId')) {
            echo '<div><strong>Item ID:</strong> ' . ($form->getItemId() ?: 'Not Set') . '</div>';
        }
        
        echo '</div></div>';
        
        // Field Options
        echo '<div>';
        echo '<h5 style="margin: 0 0 10px 0; color: #495057;">Field Options</h5>';
        echo '<div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 10px;">';
        
        $fieldOptions = getProtectedProperty($model, 'fieldOptions', []);
        if (!empty($fieldOptions)) {
            echo formatComplexValue($fieldOptions);
        } else {
            echo '<em style="color: #6c757d;">No field options configured</em>';
        }
        
        echo '</div></div>';
        echo '</div>';
        echo '</div>';

        // Debug Footer
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        echo '<div style="text-align: center; padding-top: 15px; border-top: 2px solid #dee2e6; font-size: 12px; color: #6c757d;">';
        echo 'Debug completed in ' . $executionTime . 'ms | TypeRocket Model Debug v2.0';
        echo '</div>';
        
        echo '</div>';
    }
}

/**
 * Quick debug function for minimal output
 */
function debugModelQuick($form)
{
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $model = $form->getModel();
        if (!$model) {
            echo '<div style="color: #dc3545; padding: 8px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 3px; margin: 10px 0;">No model found</div>';
            return;
        }

        $reflection = new ReflectionClass($model);
        echo '<div style="background: #e3f2fd; border: 1px solid #1976d2; border-radius: 4px; padding: 10px; margin: 10px 0; font-family: monospace; font-size: 12px;">';
        echo '<strong style="color: #1976d2;">' . $reflection->getShortName() . '</strong> ';
        echo 'ID: ' . ($model->getID() ?: 'New') . ' | ';
        echo 'Table: ' . $model->getTable() . ' | ';
        echo 'Properties: ' . count($model->getProperties());
        echo '</div>';
    }
}

/**
 * Debug model errors
 */
function debugModelErrors($model)
{
    if (defined('WP_DEBUG') && WP_DEBUG && method_exists($model, 'getErrors')) {
        $errors = $model->getErrors();
        if (!empty($errors)) {
            echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; padding: 10px; margin: 10px 0; color: #721c24;">';
            echo '<strong>Model Errors:</strong><br>';
            foreach ($errors as $error) {
                echo '• ' . htmlspecialchars($error) . '<br>';
            }
            echo '</div>';
        }
    }
}

/**
 * Debug model relationships specifically
 */
function debugModelRelationships($model)
{
    if (!defined('WP_DEBUG') || !WP_DEBUG) return;
    
    echo '<div style="background: #fff3e0; border: 1px solid #f57c00; border-radius: 4px; padding: 15px; margin: 10px 0;">';
    echo '<h4 style="margin: 0 0 10px 0; color: #f57c00;">🔗 Relationship Debug</h4>';
    
    $reflection = new ReflectionClass($model);
    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    
    echo '<div style="columns: 2; column-gap: 20px;">';
    
    foreach ($methods as $method) {
        $name = $method->getName();
        
        // Skip obvious non-relationship methods
        if (preg_match('/^(get|set|has|is|append|remove|cast|provision|format|find|where|order|take|first|count|sum|min|max|avg|select|join|save|create|update|delete|clone|to|json|when|can|__)/i', $name)) {
            continue;
        }
        
        if ($method->getNumberOfRequiredParameters() === 0 && preg_match('/^[a-z][a-zA-Z]*$/', $name)) {
            try {
                $result = $method->invoke($model);
                if ($result instanceof \TypeRocket\Models\Model) {
                    $resultClass = get_class($result);
                    echo '<div style="break-inside: avoid; margin-bottom: 8px; padding: 6px; background: white; border-radius: 3px;">';
                    echo '<strong style="color: #f57c00;">' . $name . '()</strong><br>';
                    echo '<small style="color: #666;">Returns: ' . basename($resultClass) . '</small>';
                    
                    // Try to get related info
                    $relatedBy = null;
                    $refResult = new ReflectionClass($result);
                    if ($refResult->hasProperty('relatedBy')) {
                        $prop = $refResult->getProperty('relatedBy');
                        $prop->setAccessible(true);
                        $relatedBy = $prop->getValue($result);
                    }
                    
                    if ($relatedBy && isset($relatedBy['type'])) {
                        echo '<br><small style="color: #999;">Type: ' . $relatedBy['type'] . '</small>';
                    }
                    
                    echo '</div>';
                }
            } catch (Exception $e) {
                // Skip methods that can't be called
            }
        }
    }
    
    echo '</div></div>';
}

/**
 * Debug model field validation/provisioning
 */
function debugModelFieldProvisioning($model, $testFields = [])
{
    if (!defined('WP_DEBUG') || !WP_DEBUG) return;
    
    echo '<div style="background: #e8f5e8; border: 1px solid #4caf50; border-radius: 4px; padding: 15px; margin: 10px 0;">';
    echo '<h4 style="margin: 0 0 10px 0; color: #4caf50;">⚙️ Field Provisioning Test</h4>';
    
    if (empty($testFields)) {
        $testFields = [
            'name' => 'Test Value',
            'email' => 'test@example.com',
            'id' => 999,
            'password' => 'secret',
            'admin_field' => 'admin_value'
        ];
    }
    
    echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">';
    
    // Input fields
    echo '<div>';
    echo '<h5 style="margin: 0 0 8px 0;">Input Fields</h5>';
    echo '<pre style="background: #f5f5f5; padding: 8px; border-radius: 3px; font-size: 11px;">';
    echo htmlspecialchars(json_encode($testFields, JSON_PRETTY_PRINT));
    echo '</pre>';
    echo '</div>';
    
    // Provisioned fields
    echo '<div>';
    echo '<h5 style="margin: 0 0 8px 0;">After Provisioning</h5>';
    
    try {
        if (method_exists($model, 'provisionFields')) {
            $provisioned = $model->provisionFields($testFields);
            echo '<pre style="background: #f5f5f5; padding: 8px; border-radius: 3px; font-size: 11px;">';
            echo htmlspecialchars(json_encode($provisioned, JSON_PRETTY_PRINT));
            echo '</pre>';
            
            // Show what was filtered out
            $filtered = array_diff_key($testFields, $provisioned);
            if (!empty($filtered)) {
                echo '<div style="margin-top: 8px; padding: 6px; background: #ffebee; border-radius: 3px;">';
                echo '<strong style="color: #c62828;">Filtered Out:</strong> ';
                echo implode(', ', array_keys($filtered));
                echo '</div>';
            }
        } else {
            echo '<em style="color: #666;">provisionFields method not available</em>';
        }
    } catch (Exception $e) {
        echo '<div style="color: #c62828;">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

/**
 * Debug model casting
 */
function debugModelCasting($model)
{
    if (!defined('WP_DEBUG') || !WP_DEBUG) return;
    
    echo '<div style="background: #f3e5f5; border: 1px solid #9c27b0; border-radius: 4px; padding: 15px; margin: 10px 0;">';
    echo '<h4 style="margin: 0 0 10px 0; color: #9c27b0;">🎭 Casting Debug</h4>';
    
    $reflection = new ReflectionClass($model);
    
    // Get cast property
    $cast = [];
    if ($reflection->hasProperty('cast')) {
        $prop = $reflection->getProperty('cast');
        $prop->setAccessible(true);
        $cast = $prop->getValue($model);
    }
    
    if (empty($cast)) {
        echo '<em style="color: #666;">No casting rules defined</em>';
        echo '</div>';
        return;
    }
    
    echo '<div style="background: white; padding: 10px; border-radius: 3px; margin-bottom: 10px;">';
    echo '<strong>Casting Rules:</strong><br>';
    foreach ($cast as $field => $type) {
        echo '<div style="margin: 3px 0; padding: 3px 6px; background: #f8f9fa; border-radius: 2px;">';
        echo '<code style="color: #9c27b0;">' . htmlspecialchars($field) . '</code> → ';
        echo '<code style="color: #666;">' . htmlspecialchars($type) . '</code>';
        echo '</div>';
    }
    echo '</div>';
    
    // Test casting with current properties
    $properties = method_exists($model, 'getProperties') ? $model->getProperties() : [];
    if (!empty($properties)) {
        echo '<div style="background: white; padding: 10px; border-radius: 3px;">';
        echo '<strong>Cast Results for Current Properties:</strong><br>';
        
        foreach ($cast as $field => $type) {
            if (isset($properties[$field])) {
                $original = $properties[$field];
                try {
                    $casted = method_exists($model, 'getCast') ? $model->getCast($field) : $original;
                    echo '<div style="margin: 5px 0; padding: 5px; background: #f8f9fa; border-radius: 3px;">';
                    echo '<strong>' . htmlspecialchars($field) . ':</strong><br>';
                    echo '<small style="color: #666;">Original:</small> <code>' . htmlspecialchars(gettype($original)) . '</code> ' . htmlspecialchars(json_encode($original)) . '<br>';
                    echo '<small style="color: #666;">Cast:</small> <code>' . htmlspecialchars(gettype($casted)) . '</code> ' . htmlspecialchars(json_encode($casted));
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<div style="color: #c62828; margin: 3px 0;">Error casting ' . htmlspecialchars($field) . ': ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
        }
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Performance-focused model debug
 */
function debugModelPerformance($model, $operations = [])
{
    if (!defined('WP_DEBUG') || !WP_DEBUG) return;
    
    echo '<div style="background: #e1f5fe; border: 1px solid #0288d1; border-radius: 4px; padding: 15px; margin: 10px 0;">';
    echo '<h4 style="margin: 0 0 10px 0; color: #0288d1;">⚡ Performance Debug</h4>';
    
    $startTime = microtime(true);
    $startMemory = memory_get_usage();
    
    if (empty($operations)) {
        $operations = [
            'getProperties' => function($m) { return $m->getProperties(); },
            'getID' => function($m) { return $m->getID(); },
            'getTable' => function($m) { return $m->getTable(); },
            'hasProperties' => function($m) { return $m->hasProperties(); }
        ];
    }
    
    $results = [];
    
    foreach ($operations as $name => $operation) {
        $opStart = microtime(true);
        $opMemStart = memory_get_usage();
        
        try {
            if (is_callable($operation)) {
                $result = $operation($model);
                $opEnd = microtime(true);
                $opMemEnd = memory_get_usage();
                
                $results[$name] = [
                    'success' => true,
                    'time' => ($opEnd - $opStart) * 1000,
                    'memory' => $opMemEnd - $opMemStart,
                    'result_type' => gettype($result),
                    'result_size' => is_array($result) ? count($result) : (is_string($result) ? strlen($result) : 1)
                ];
            } else {
                $results[$name] = ['success' => false, 'error' => 'Invalid operation'];
            }
        } catch (Exception $e) {
            $results[$name] = ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    $totalTime = (microtime(true) - $startTime) * 1000;
    $totalMemory = memory_get_usage() - $startMemory;
    
    echo '<div style="background: white; padding: 10px; border-radius: 3px; margin-bottom: 10px;">';
    echo '<strong>Total: </strong>';
    echo number_format($totalTime, 2) . 'ms, ';
    echo number_format($totalMemory / 1024, 2) . 'KB';
    echo '</div>';
    
    echo '<table style="width: 100%; border-collapse: collapse; background: white; border-radius: 3px; overflow: hidden;">';
    echo '<thead style="background: #f8f9fa;"><tr>';
    echo '<th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">Operation</th>';
    echo '<th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">Time (ms)</th>';
    echo '<th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">Memory</th>';
    echo '<th style="padding: 8px; text-align: left; border-bottom: 1px solid #dee2e6;">Result</th>';
    echo '</tr></thead><tbody>';
    
    foreach ($results as $name => $data) {
        echo '<tr>';
        echo '<td style="padding: 6px 8px; border-bottom: 1px solid #f8f9fa;"><code>' . htmlspecialchars($name) . '</code></td>';
        
        if ($data['success']) {
            $timeColor = $data['time'] > 10 ? '#dc3545' : ($data['time'] > 5 ? '#ffc107' : '#28a745');
            echo '<td style="padding: 6px 8px; border-bottom: 1px solid #f8f9fa; color: ' . $timeColor . ';">' . number_format($data['time'], 3) . '</td>';
            
            $memColor = $data['memory'] > 1024 ? '#dc3545' : ($data['memory'] > 512 ? '#ffc107' : '#28a745');
            echo '<td style="padding: 6px 8px; border-bottom: 1px solid #f8f9fa; color: ' . $memColor . ';">' . number_format($data['memory']) . 'B</td>';
            
            echo '<td style="padding: 6px 8px; border-bottom: 1px solid #f8f9fa;"><small>' . $data['result_type'];
            if (isset($data['result_size'])) {
                echo ' (' . $data['result_size'] . ')';
            }
            echo '</small></td>';
        } else {
            echo '<td colspan="3" style="padding: 6px 8px; border-bottom: 1px solid #f8f9fa; color: #dc3545;">Error: ' . htmlspecialchars($data['error']) . '</td>';
        }
        
        echo '</tr>';
    }
    
    echo '</tbody></table>';
    echo '</div>';
}