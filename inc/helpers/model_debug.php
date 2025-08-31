<?php



function debugModel($form)

{


    if (defined('WP_DEBUG') && WP_DEBUG) {

        echo '<div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; font-family: -apple-system, BlinkMacSystemFont, sans-serif;">';
        echo '<h4 style="margin-top: 0; color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 8px;">🔍 TypeRocket Model Debug Information</h4>';

        // Get the model instance from the form
        $model = $form->getModel();

        // Create reflection to access protected properties
        $reflection = new ReflectionClass($model);

        /**
         * Helper function to safely get protected property values
         */
        function getProtectedProperty($object, $propertyName, $default = null)
        {
            try {
                $reflection = new ReflectionClass($object);
                $property = $reflection->getProperty($propertyName);
                $property->setAccessible(true);
                $value = $property->getValue($object);
                return $value !== null ? $value : $default;
            } catch (ReflectionException $e) {
                return $default;
            }
        }

        /**
         * Helper function to get relationship methods from the model
         */
        function getRelationshipMethods($model)
        {
            $reflection = new ReflectionClass($model);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            $relationships = [];

            foreach ($methods as $method) {
                $methodName = $method->getName();

                // Skip obvious non-relationship methods
                if (
                    in_array($methodName, ['__construct', '__toString', 'toArray', 'toJson', '__get', '__set', '__isset', '__unset']) ||
                    strpos($methodName, 'get') === 0 ||
                    strpos($methodName, 'set') === 0 ||
                    strpos($methodName, 'is') === 0 ||
                    strpos($methodName, 'has') === 0 ||
                    strpos($methodName, 'append') === 0 ||
                    strpos($methodName, 'remove') === 0 ||
                    $method->getNumberOfRequiredParameters() > 0
                ) {
                    continue;
                }

                try {
                    // Try to determine if it's a relationship by method name patterns
                    if (
                        preg_match('/^[a-z][a-zA-Z]*$/', $methodName) &&
                        !in_array($methodName, ['save', 'delete', 'create', 'update', 'find', 'where', 'first', 'get', 'all'])
                    ) {
                        $relationships[] = $methodName;
                    }
                } catch (Exception $e) {
                    // Skip methods that can't be analyzed
                }
            }

            return $relationships;
        }

        // Basic Model Information
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';

        // Left Column - Basic Info
        echo '<div style="background: white; padding: 12px; border: 1px solid #ced4da; border-radius: 3px;">';
        echo '<h5 style="margin: 0 0 10px 0; color: #28a745; font-size: 14px;">📋 Basic Information</h5>';
        echo '<table style="width: 100%; font-size: 12px; border-collapse: collapse;">';

        $className = $reflection->getShortName();
        $fullClassName = get_class($model);
        $tableName = method_exists($model, 'getTable') ? $model->getTable() : 'N/A';
        $idColumn = method_exists($model, 'getIdColumn') ? $model->getIdColumn() : 'id';
        $connection = method_exists($model, 'getConnection') ? ($model->getConnection() ?: 'default') : 'default';
        $resource = getProtectedProperty($model, 'resource', 'N/A');

        $basicInfo = [
            'Class Name' => $className,
            'Full Class' => $fullClassName,
            'Table Name' => $tableName,
            'Resource' => $resource,
            'ID Column' => $idColumn,
            'Connection' => $connection,
        ];

        foreach ($basicInfo as $key => $value) {
            echo '<tr><td style="font-weight: bold; padding: 3px 6px; border-bottom: 1px solid #eee;">' . $key . ':</td>';
            echo '<td style="padding: 3px 6px; border-bottom: 1px solid #eee; word-break: break-word;">' . htmlspecialchars($value) . '</td></tr>';
        }
        echo '</table></div>';

        // Right Column - Field Configuration
        echo '<div style="background: white; padding: 12px; border: 1px solid #ced4da; border-radius: 3px;">';
        echo '<h5 style="margin: 0 0 10px 0; color: #007bff; font-size: 14px;">⚙️ Field Configuration</h5>';

        $fillable = getProtectedProperty($model, 'fillable', []);
        $guard = getProtectedProperty($model, 'guard', []);
        $builtin = getProtectedProperty($model, 'builtin', []);
        $metaless = getProtectedProperty($model, 'metaless', []);
        $private = getProtectedProperty($model, 'private', []);
        $cast = getProtectedProperty($model, 'cast', []);

        $fieldConfig = [
            'Fillable' => $fillable,
            'Guarded' => $guard,
            'Builtin' => $builtin,
            'Meta-less' => $metaless,
            'Private' => $private,
            'Cast' => $cast,
        ];

        echo '<table style="width: 100%; font-size: 11px;">';
        foreach ($fieldConfig as $key => $fields) {
            $displayValue = empty($fields) ? '<em style="color: #6c757d;">None</em>' : (is_array($fields) ? implode(', ', array_keys($fields) ?: $fields) :
                htmlspecialchars(json_encode($fields)));
            echo '<tr><td style="font-weight: bold; padding: 2px 4px; vertical-align: top; width: 25%;">' . $key . ':</td>';
            echo '<td style="padding: 2px 4px; word-break: break-word;">' . $displayValue . '</td></tr>';
        }
        echo '</table></div>';
        echo '</div>';

        // Model Properties & Data
        echo '<div style="background: white; padding: 12px; border: 1px solid #ced4da; border-radius: 3px; margin-bottom: 15px;">';
        echo '<h5 style="margin: 0 0 10px 0; color: #6f42c1; font-size: 14px;">💾 Current Model Data</h5>';

        $properties = method_exists($model, 'getProperties') ? $model->getProperties() : [];
        $modelId = method_exists($model, 'getID') ? $model->getID() : null;

        if (!empty($properties)) {
            echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #e9ecef; padding: 8px; font-size: 11px; background: #f8f9fa;">';
            echo '<strong>ID:</strong> ' . ($modelId ? htmlspecialchars($modelId) : '<em>New Record</em>') . '<br><br>';

            foreach ($properties as $key => $value) {
                $displayValue = is_array($value) || is_object($value) ?
                    '<code>' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</code>' :
                    htmlspecialchars($value ?? '');
                echo '<strong>' . htmlspecialchars($key) . ':</strong> ' . $displayValue . '<br>';
            }
            echo '</div>';
        } else {
            echo '<em style="color: #6c757d;">No data loaded</em>';
        }
        echo '</div>';

        // Relationships
        echo '<div style="background: white; padding: 12px; border: 1px solid #ced4da; border-radius: 3px; margin-bottom: 15px;">';
        echo '<h5 style="margin: 0 0 10px 0; color: #fd7e14; font-size: 14px;">🔗 Relationships</h5>';

        $relationships = getRelationshipMethods($model);
        $with = getProtectedProperty($model, 'with', []);

        if (!empty($relationships)) {
            echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">';

            echo '<div><strong>Detected Methods:</strong><br>';
            echo '<div style="background: #f8f9fa; padding: 6px; border-radius: 3px; font-size: 11px;">';
            foreach ($relationships as $rel) {
                echo '<span style="display: inline-block; background: #e9ecef; padding: 2px 6px; margin: 2px; border-radius: 2px;">' . $rel . '</span>';
            }
            echo '</div></div>';

            echo '<div><strong>Eager Loading:</strong><br>';
            echo '<div style="background: #f8f9fa; padding: 6px; border-radius: 3px; font-size: 11px;">';
            if (!empty($with)) {
                foreach ((array)$with as $w) {
                    echo '<span style="display: inline-block; background: #d1ecf1; padding: 2px 6px; margin: 2px; border-radius: 2px;">' . $w . '</span>';
                }
            } else {
                echo '<em style="color: #6c757d;">None configured</em>';
            }
            echo '</div></div>';

            echo '</div>';
        } else {
            echo '<em style="color: #6c757d;">No relationships detected</em>';
        }
        echo '</div>';

        // Advanced Configuration
        echo '<div style="background: white; padding: 12px; border: 1px solid #ced4da; border-radius: 3px; margin-bottom: 15px;">';
        echo '<h5 style="margin: 0 0 10px 0; color: #dc3545; font-size: 14px;">🛠️ Advanced Configuration</h5>';

        $format = getProtectedProperty($model, 'format', []);
        $static = getProtectedProperty($model, 'static', []);
        $closed = getProtectedProperty($model, 'closed', false);
        $cache = method_exists($model, 'getCache') ? $model->getCache() : getProtectedProperty($model, 'cache', true);
        $resultsClass = getProtectedProperty($model, 'resultsClass', 'Results');

        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 11px;">';

        echo '<div>';
        echo '<strong>Format Fields:</strong><br>';
        if (!empty($format)) {
            echo '<pre style="background: #f8f9fa; padding: 6px; margin: 4px 0; border-radius: 3px; font-size: 10px; overflow-x: auto;">';
            foreach ($format as $field => $callback) {
                $callbackInfo = is_callable($callback) ? (is_string($callback) ? $callback : 'Closure') : 'Invalid';
                echo htmlspecialchars($field) . ' => ' . $callbackInfo . "\n";
            }
            echo '</pre>';
        } else {
            echo '<em style="color: #6c757d;">None</em>';
        }
        echo '</div>';

        echo '<div>';
        echo '<strong>Configuration:</strong><br>';
        $configInfo = [
            'Closed Model' => $closed ? 'Yes' : 'No',
            'Cache Enabled' => $cache ? 'Yes' : 'No',
            'Results Class' => basename(str_replace('\\', '/', $resultsClass)),
            'Static Fields' => empty($static) ? 'None' : count($static) . ' defined'
        ];

        echo '<table style="width: 100%;">';
        foreach ($configInfo as $key => $value) {
            echo '<tr><td style="font-weight: bold; padding: 2px 4px;">' . $key . ':</td>';
            echo '<td style="padding: 2px 4px;">' . $value . '</td></tr>';
        }
        echo '</table>';
        echo '</div>';

        echo '</div>';
        echo '</div>';

        // Query Information (if available)
        if (method_exists($model, 'getSuspectSQL')) {
            $lastSQL = $model->getSuspectSQL();
            if ($lastSQL) {
                echo '<div style="background: white; padding: 12px; border: 1px solid #ced4da; border-radius: 3px;">';
                echo '<h5 style="margin: 0 0 10px 0; color: #20c997; font-size: 14px;">📊 Last SQL Query</h5>';
                echo '<pre style="background: #f8f9fa; padding: 10px; border: 1px solid #e9ecef; border-radius: 3px; overflow-x: auto; font-size: 10px; margin: 0;">';
                echo htmlspecialchars($lastSQL);
                echo '</pre>';
                echo '</div>';
            }
        }

        echo '</div>';
    }
}
