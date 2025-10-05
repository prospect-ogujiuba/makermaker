<?php

use TypeRocket\Http\Request;
use Doctrine\Inflector\InflectorFactory;

/**
 * Helpers for MakerMaker WordPress/TypeRocket application.
 *
 * @package makermaker
 */

// Output HTML options for select dropdowns
function outputSelectOptions($options, $currentValue, $valueKey = null, $labelKey = null): void
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

// Render search actions for TypeRocket admin pages
function renderAdvancedSearchActions(string $resource): void
{ ?>
    <div class="tr-search-actions">
        <div>
            <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>?page=<?= $resource ?>_index" class="button">Reset Filters</a>
            <button type="submit" class="button">Search</button>
        </div>
    </div>

    <input type="checkbox" id="search-toggle" class="search-toggle-input">
    <label for="search-toggle" class="button">Toggle Advanced Search</label>
<?php
}

// Convert strings to kebab_case or kebab-case
function mm_kebab(string $s, string $separator = '_'): string
{
    // Normalize separator: only allow '-' or '_'
    $sep = ($separator === '-') ? '-' : '_';

    // Split camelCase / PascalCase with chosen separator
    $k = preg_replace('/([a-z])([A-Z])/', '$1' . $sep . '$2', $s);
    $k = strtolower($k);

    // Replace non-alphanumeric/separator characters with chosen separator
    $k = preg_replace('/[^a-z0-9' . preg_quote($sep, '/') . ']+/', $sep, $k);

    // Collapse multiple separators
    $pattern = '/' . preg_quote($sep, '/') . '+/';
    $k = preg_replace($pattern, $sep, $k);

    return trim($k, $sep);
}



// Create TypeRocket resource page + REST endpoint
function mm_create_custom_resource(
    string $resourceKey,
    string $controller,
    string $title,
    bool $hasAddButton = true,
    ?string $restSlug = null,
    bool $registerRest = true
): \TypeRocket\Register\Page {
    $fqcn = '\MakerMaker\Controllers\\' . $controller;

    // Create admin page
    $resourcePage = tr_resource_pages("{$resourceKey}@{$fqcn}", $title);

    if ($hasAddButton) {
        $adminPageSlug = strtolower($resourceKey) . '_add';
        $resourcePage->addNewButton(admin_url('admin.php?page=' . $adminPageSlug));
    }

    // Register REST endpoint
    if ($registerRest) {
        $slug = $restSlug ?: mm_kebab(pluralize($resourceKey), '-');
        \TypeRocket\Register\Registry::addCustomResource($slug, ['controller' => $fqcn]);
    }

    return $resourcePage;
}

function checkIntRange($args)
{
    /**
     * @var $option3
     * @var $option
     * @var $option2
     * @var $name
     * @var $field_name
     * @var $value
     * @var $type
     * @var \TypeRocket\Utility\Validator $validator
     */
    extract($args);

    // Check if value is numeric first
    if (!is_numeric($value)) {
        return ' must be a valid number';
    }

    $numValue = (int) $value;

    // For callback validators:
    // $option = function name (checkIntRange)
    // $option2 = first parameter (min value)  
    // $option3 = second parameter (max value)
    $min = isset($option2) ? (int) $option2 : 0;
    $max = isset($option3) ? (int) $option3 : 255;

    // Ensure min is not greater than max
    if ($min > $max) {
        $temp = $min;
        $min = $max;
        $max = $temp;
    }

    // Check for valid range
    if ($numValue < $min || $numValue > $max) {
        return " must be between {$min} and {$max}";
    }

    return true;
}

/**
 * Auto-generate or sanitize a code/slug field
 * - If code field is empty: generates from source field
 * - If code field has user input: sanitizes it to proper format
 * 
 * @param array|\TypeRocket\Http\Fields &$fields Reference to fields array or Fields object
 * @param string $codeField The field name for the code (default: 'code')
 * @param string $sourceField The field name to generate from (default: 'name') 
 * @param string $separator Separator for kebab case and between addon and code (default: '-')
 * @param string|null $addon Optional text to add to the generated code
 * @param string $placement Where to place the addon: 'prefix', 'suffix' (default: 'prefix')
 * @param bool $uppercase Whether to uppercase the result (default: false)
 * @return void
 */
function autoGenerateCode(&$fields, $codeField = 'code', $sourceField = 'name', $separator = '-', $addon = null, $placement = 'prefix', $uppercase = false)
{
    // Handle TypeRocket Fields objects
    if (is_object($fields) && method_exists($fields, 'getArrayCopy')) {
        $fieldsArray = $fields->getArrayCopy();

        $sourceValue = '';
        $needsProcessing = false;

        // Determine source value for code generation
        if (!isset($fieldsArray[$codeField]) || empty($fieldsArray[$codeField])) {
            // Code field is empty - generate from source field
            if (isset($fieldsArray[$sourceField]) && !empty($fieldsArray[$sourceField])) {
                $sourceValue = $fieldsArray[$sourceField];
                $needsProcessing = true;
            }
        } else {
            // Code field has user input - sanitize it
            $sourceValue = $fieldsArray[$codeField];
            $needsProcessing = true;
        }

        if ($needsProcessing && $sourceValue !== '') {
            $generatedCode = $uppercase ? strtoupper(mm_kebab($sourceValue, $separator)) : mm_kebab($sourceValue, $separator);

            // Add addon if provided (only when generating from source field, not when sanitizing user input)
            if ($addon !== null && $addon !== '' && (!isset($fieldsArray[$codeField]) || empty($fieldsArray[$codeField]))) {
                $processedAddon = $uppercase ? strtoupper(mm_kebab($addon, $separator)) : mm_kebab($addon, $separator);

                $generatedCode = $placement === 'suffix'
                    ? $generatedCode . $separator . $processedAddon
                    : $processedAddon . $separator . $generatedCode;
            }

            $fieldsArray[$codeField] = $generatedCode;
            $fields->exchangeArray($fieldsArray);
        }
    }
    // Handle regular arrays
    else {
        $sourceValue = '';
        $needsProcessing = false;

        // Determine source value for code generation
        if (!isset($fields[$codeField]) || empty($fields[$codeField])) {
            // Code field is empty - generate from source field
            if (isset($fields[$sourceField]) && !empty($fields[$sourceField])) {
                $sourceValue = $fields[$sourceField];
                $needsProcessing = true;
            }
        } else {
            // Code field has user input - sanitize it
            $sourceValue = $fields[$codeField];
            $needsProcessing = true;
        }

        if ($needsProcessing && $sourceValue !== '') {
            $generatedCode = $uppercase ? strtoupper(mm_kebab($sourceValue, $separator)) : mm_kebab($sourceValue, $separator);

            // Add addon if provided (only when generating from source field, not when sanitizing user input)
            if ($addon !== null && $addon !== '' && (!isset($fields[$codeField]) || empty($fields[$codeField]))) {
                $processedAddon = $uppercase ? strtoupper(mm_kebab($addon, $separator)) : mm_kebab($addon, $separator);

                $generatedCode = $placement === 'suffix'
                    ? $generatedCode . $separator . $processedAddon
                    : $processedAddon . $separator . $generatedCode;
            }

            $fields[$codeField] = $generatedCode;
        }
    }
}

/**
 * Check for self-reference in parent-child relationships
 * 
 * This function prevents entities from referencing themselves as parents
 * and can detect circular references in hierarchical data structures.
 * 
 * @param array $args - Standard TypeRocket validator args
 * @return true|string - Returns true if valid, error message if invalid
 */
function checkSelfReference($args)
{
    /**
     * @var $option - table name (required)
     * @var $option2 - parent column name (default: 'parent_id')
     * @var $option3 - primary key column name (default: 'id')
     * @var $value - the parent_id value being validated
     * @var $validator - TypeRocket Validator instance
     * @var $weak - whether this is an optional field
     */
    extract($args);

    // Check if this is an optional field and the value is considered "empty" by TypeRocket standards
    if (isset($weak) && $weak && \TypeRocket\Utility\Data::emptyOrBlankRecursive($value)) {
        return true;
    }

    // If no value provided, it's valid (nullable parent)
    // Handle all possible "empty" states from select dropdowns
    if (
        $value === null || $value === '' || $value === 0 || $value === '0' ||
        (is_string($value) && trim($value) === '') ||
        (is_array($value) && empty($value))
    ) {
        return true;
    }

    // Get current record ID from route args
    $request = Request::new();
    $route_args = $request->getDataGet('route_args');
    $currentId = $route_args[0] ?? null;

    // Convert to integer for comparison
    $parentId = (int) $value;

    // If no current ID (new record), no self-reference possible
    if (!$currentId) {
        return true;
    }

    // Convert current ID to integer for comparison
    $currentId = (int) $currentId;

    // Direct self-reference check
    if ($currentId === $parentId) {
        return ' cannot reference itself as parent';
    }

    // Check for circular reference by traversing up the hierarchy
    $tableName = $option; // Required: table name
    $parentColumn = $option2 ?? 'parent_id';
    $idColumn = $option3 ?? 'id';

    if (hasCircularReference($parentId, $currentId, $tableName, $parentColumn, $idColumn)) {
        return ' would create a circular reference';
    }

    return true;
}

/**
 * Detect circular references in hierarchical data
 * 
 * @param int $parentId - The proposed parent ID
 * @param int $currentId - The current record ID
 * @param string $tableName - Database table name
 * @param string $parentColumn - Parent column name
 * @param string $idColumn - Primary key column name
 * @param array $visited - Track visited nodes to prevent infinite loops
 * @return bool
 */
function hasCircularReference($parentId, $currentId, $tableName, $parentColumn, $idColumn, $visited = [])
{
    global $wpdb;

    // Prevent infinite loops
    if (in_array($parentId, $visited)) {
        return true;
    }

    $visited[] = $parentId;

    // Get the parent's parent
    $query = $wpdb->prepare(
        "SELECT {$parentColumn} FROM {$tableName} WHERE {$idColumn} = %d",
        $parentId
    );

    $grandParentId = $wpdb->get_var($query);

    // If no grandparent, no circular reference
    if (!$grandParentId) {
        return false;
    }

    // If grandparent is our current record, we have a circle
    if ((int) $grandParentId === $currentId) {
        return true;
    }

    // Recursively check up the chain
    return hasCircularReference($grandParentId, $currentId, $tableName, $parentColumn, $idColumn, $visited);
}

/**
 * Convert empty string to NULL
 * 
 * @param mixed $value
 * @return mixed
 */
function convertEmptyToNull($value)
{
    return ($value === '' || $value === null) ? null : $value;
}

/**
 * Custom validation callback for enum options
 *
 * @param mixed $value The value being validated
 * @param string $encodedOptions Base64 encoded JSON array of valid options
 * @return bool
 */
function validateEnumOption($value, $encodedOptions)
{
    $validOptions = json_decode(base64_decode($encodedOptions), true);

    if (!is_array($validOptions)) {
        return false;
    }

    return in_array((string)$value, $validOptions, true);
}

/**
 * Simple database operation wrapper for TypeRocket controllers
 * 
 * @param callable $operation The model operation to execute
 * @param \TypeRocket\Http\Response $response The response object
 * @param string $successMessage Message to show on success
 * @return bool True if operation succeeded, false if it failed
 */
function tryDatabaseOperation(callable $operation, $response, $successMessage = 'Operation completed successfully')
{
    global $wpdb;

    // Clear any previous errors
    $wpdb->last_error = '';

    try {
        $result = $operation();

        // Check for WordPress database errors first
        if (!empty($wpdb->last_error)) {
            error_log("WordPress database error: " . $wpdb->last_error);

            // Clean up the error message to hide database/table names
            $cleanError = cleanDatabaseError($wpdb->last_error);
            $response->flashNext("Database error: " . $cleanError, 'error');
            return false;
        }

        // TypeRocket models return the model instance on success, false/null on failure
        if ($result === false || $result === null) {
            $response->flashNext('Database operation failed - unknown error', 'error');
            return false;
        }

        // Check if the model has errors
        if (is_object($result) && method_exists($result, 'getErrors') && $result->getErrors()) {
            $errors = $result->getErrors();
            $errorMessage = is_array($errors) ? implode('; ', $errors) : $errors;
            $response->flashNext("Error: " . $errorMessage, 'error');
            return false;
        }

        $response->flashNext($successMessage, 'success');
        return true;
    } catch (\TypeRocket\Exceptions\ModelException $e) {
        // TypeRocket model validation or database errors
        error_log("TypeRocket ModelException: " . $e->getMessage());
        $response->flashNext("Model error: " . $e->getMessage(), 'error');
        return false;
    } catch (\Exception $e) {
        // Any other unexpected errors
        error_log("Unexpected error: " . $e->getMessage());
        $response->flashNext("Unexpected error: " . $e->getMessage(), 'error');
        return false;
    }
}

/**
 * Clean database error message to hide sensitive information
 * 
 * @param string $error The raw database error
 * @return string Cleaned error message
 */
function cleanDatabaseError($error)
{
    // Remove database and table name patterns like `database`.`table_name`
    $cleaned = preg_replace('/`[^`]+`\.`[^`]+`/', 'Entity', $error);

    // Remove "for `database`.`table`" patterns entirely
    $cleaned = preg_replace('/\s+for\s+`[^`]+`\.`[^`]+`/', '', $cleaned);

    return $cleaned;
}

/**
 * Validate currency is exactly 3 uppercase letters
 */
function validateCurrency($value, $field_name)
{
    if (!preg_match('/^[A-Z]{3}$/', $value)) {
        return 'Currency must be exactly 3 uppercase letters (e.g., CAD, USD)';
    }
    return true;
}

/**
 * Validate approval status enum
 */
function validateApprovalStatus($value, $field_name)
{
    $valid_statuses = ['draft', 'pending', 'approved', 'rejected'];
    if (!in_array($value, $valid_statuses)) {
        return 'Approval status must be one of: ' . implode(', ', $valid_statuses);
    }
    return true;
}

/**
 * Validate date range - valid_to must be after valid_from
 */
function validateDateRange($value, $field_name)
{
    $request = Request::new();
    $fields = $request->getFields();
    $valid_from = $fields['valid_from'] ?? null;

    if ($value && $valid_from) {
        $from_time = strtotime($valid_from);
        $to_time = strtotime($value);

        if ($to_time <= $from_time) {
            return 'Valid to date must be after valid from date';
        }
    }
    return true;
}

/**
 * Check minimum numeric value
 */
function checkMinValue($value, $field_name, $min_value)
{
    if ($value !== null && $value !== '' && (float)$value < (float)$min_value) {
        return "must be greater than or equal to {$min_value}";
    }
    return true;
}

/**
 * Generate a link to a TypeRocket resource page
 *
 * @param string $resource The resource name (e.g., 'PriceRecord', 'Service')
 * @param string $action The action (index, add, edit, show, delete)
 * @param string $text The link text to display
 * @param int|null $id The resource ID (required for edit, show, delete actions)
 * @param string $icon The icon class (default: 'bi bi-arrow-up-right')
 * @return string The complete HTML link
 */
function toResourceUrl($resource, $action = 'index', $text = 'Back', $id = null, $icon = 'box-arrow-up-right')
{
    // Convert resource name to lowercase with underscores for TypeRocket convention
    $resource_slug = strtolower(preg_replace('/([A-Z])/', '$1', lcfirst($resource)));

    // Build the page parameter
    $page = $resource_slug . '_' . $action;

    // Start building the URL
    $url_params = ['page' => $page];

    // Add route_args for actions that need an ID
    if (in_array($action, ['edit', 'show', 'delete']) && $id !== null) {
        $url_params['route_args'] = [$id];
    }

    // Build the admin URL
    $admin_url = admin_url('admin.php?' . http_build_query($url_params));

    // Generate the HTML link
    $icon_html = $icon ? "<i class='bi bi-{$icon}'></i> " : '';

    return "<a href='{$admin_url}'>{$icon_html}{$text}</a>";
}

/**
 * Global helper function for easy access
 */
if (!function_exists('to_resource')) {
    function to_resource($resource, $action = 'index', $text = null, $id = null, $icon = null, $class = 'button')
    {
        return \MakerMaker\Helpers\SmartResourceHelper::link($resource, $action, $text, $id, $icon, $class);
    }
}

if (!function_exists('resource_url')) {
    function resource_url($resource, $action = 'index', $id = null)
    {
        return \MakerMaker\Helpers\SmartResourceHelper::url($resource, $action, $id);
    }
}

/**
 * Get entity name by ID for better descriptions
 */
function getEntityName($modelClass, $id)
{
    if (!$id) {
        return 'N/A';
    }

    try {
        $entity = $modelClass::new()->findById($id);
        if ($entity) {
            // Try common name fields
            return $entity->name ?? $entity->title ?? "ID: $id";
        }
    } catch (\Exception $e) {
        // If lookup fails, just return the ID
    }

    return "ID: $id";
}

/**
 * Get user display name
 */
function getUserName($userId)
{
    if (!$userId) {
        return 'N/A';
    }

    try {
        $user = \TypeRocket\Models\WPUser::new()->findById($userId);
        if ($user) {
            return $user->display_name ?? $user->user_login ?? "User #$userId";
        }
    } catch (\Exception $e) {
        // If lookup fails, just return the ID
    }

    return "User #$userId";
}

/**
 * Pluralize a word, handling delimiters (kebab-case, snake_case, etc.)
 * Only pluralizes the last segment when delimiters are present.
 * 
 * Examples:
 * - "equipment" -> "equipment"
 * - "service-equipment" -> "service-equipment" (equipment is uncountable)
 * - "user-profile" -> "user-profiles"
 * - "api_endpoint" -> "api_endpoints"
 * - "ServiceEquipment" -> "ServiceEquipments"
 * 
 * @param string $word The word to pluralize
 * @return string The pluralized word
 */
function pluralize($word)
{
    static $inflector = null;

    if ($inflector === null) {
        $inflector = InflectorFactory::create()->build();
    }

    // Detect delimiter
    $delimiter = null;
    $delimiters = ['-', '_', '.', ' '];

    foreach ($delimiters as $d) {
        if (strpos($word, $d) !== false) {
            $delimiter = $d;
            break;
        }
    }

    // Check for PascalCase/camelCase
    if ($delimiter === null && preg_match('/[a-z][A-Z]/', $word)) {
        // Split on capital letters
        $parts = preg_split('/(?=[A-Z])/', $word, -1, PREG_SPLIT_NO_EMPTY);

        if (count($parts) > 1) {
            // Pluralize the last part
            $lastIndex = count($parts) - 1;
            $parts[$lastIndex] = $inflector->pluralize($parts[$lastIndex]);
            return implode('', $parts);
        }
    }

    // No delimiter found, pluralize the whole word
    if ($delimiter === null) {
        return $inflector->pluralize($word);
    }

    // Split by delimiter, pluralize last part only
    $parts = explode($delimiter, $word);
    $lastIndex = count($parts) - 1;
    $parts[$lastIndex] = $inflector->pluralize($parts[$lastIndex]);

    return implode($delimiter, $parts);
}

/**
 * Singularize a word, handling delimiters
 * Only singularizes the last segment when delimiters are present.
 * 
 * @param string $word The word to singularize
 * @return string The singularized word
 */
function singularize($word)
{
    static $inflector = null;

    if ($inflector === null) {
        $inflector = InflectorFactory::create()->build();
    }

    // Detect delimiter
    $delimiter = null;
    $delimiters = ['-', '_', '.', ' '];

    foreach ($delimiters as $d) {
        if (strpos($word, $d) !== false) {
            $delimiter = $d;
            break;
        }
    }

    // Check for PascalCase/camelCase
    if ($delimiter === null && preg_match('/[a-z][A-Z]/', $word)) {
        // Split on capital letters
        $parts = preg_split('/(?=[A-Z])/', $word, -1, PREG_SPLIT_NO_EMPTY);

        if (count($parts) > 1) {
            // Singularize the last part
            $lastIndex = count($parts) - 1;
            $parts[$lastIndex] = $inflector->singularize($parts[$lastIndex]);
            return implode('', $parts);
        }
    }

    // No delimiter found, singularize the whole word
    if ($delimiter === null) {
        return $inflector->singularize($word);
    }

    // Split by delimiter, singularize last part only
    $parts = explode($delimiter, $word);
    $lastIndex = count($parts) - 1;
    $parts[$lastIndex] = $inflector->singularize($parts[$lastIndex]);

    return implode($delimiter, $parts);
}
