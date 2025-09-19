<?php

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

// Convert CamelCase to kebab-case
function mm_kebab(string $s): string
{
    // Split camelCase/PascalCase
    $k = preg_replace('/([a-z])([A-Z])/', '$1-$2', $s);
    $k = strtolower($k);

    // Replace everything else non a-z/0-9/hyphen with hyphen
    $k = preg_replace('/[^a-z0-9-]+/', '-', $k);

    // Collapse multiple hyphens
    $k = preg_replace('/-+/', '-', $k);

    return trim($k, '-');
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
        $slug = $restSlug ?: mm_kebab($resourceKey);
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
 * Auto-generate a code/slug field if it's empty
 * 
 * @param array|\TypeRocket\Http\Fields &$fields Reference to fields array or Fields object
 * @param string $codeField The field name for the code (default: 'code')
 * @param string $sourceField The field name to generate from (default: 'name') 
 * @param bool $uppercase Whether to uppercase the result (default: true)
 * @param string|null $addon Optional text to add to the generated code
 * @param string $separator Separator between addon and code (default: '-')
 * @param string $placement Where to place the addon: 'prefix', 'suffix' (default: 'prefix')
 * @return void
 */
function autoGenerateCode(&$fields, $codeField = 'code', $sourceField = 'name', $uppercase = false, $addon = null, $separator = '-', $placement = 'prefix')
{
    // Handle TypeRocket Fields objects
    if (is_object($fields) && method_exists($fields, 'getArrayCopy')) {
        $fieldsArray = $fields->getArrayCopy();
        
        if (!$fieldsArray[$codeField] || $fieldsArray[$codeField] == NULL) {
            $generatedCode = $uppercase ? strtoupper(mm_kebab($fieldsArray[$sourceField])) : mm_kebab($fieldsArray[$sourceField]);
            
            // Add addon if provided
            if ($addon !== null && $addon !== '') {
                $processedAddon = $uppercase ? strtoupper(mm_kebab($addon)) : mm_kebab($addon);
                
                $generatedCode = $placement === 'suffix' 
                    ? $generatedCode . $separator . $processedAddon
                    : $processedAddon . $separator . $generatedCode;
            }
            
            $fieldsArray[$codeField] = $generatedCode;
        }
        
        $fields->exchangeArray($fieldsArray);
    } 
    // Handle regular arrays
    else {
        if (!$fields[$codeField] || $fields[$codeField] == NULL) {
            $generatedCode = $uppercase ? strtoupper(mm_kebab($fields[$sourceField])) : mm_kebab($fields[$sourceField]);
            
            // Add addon if provided
            if ($addon !== null && $addon !== '') {
                $processedAddon = $uppercase ? strtoupper(mm_kebab($addon)) : mm_kebab($addon);
                
                $generatedCode = $placement === 'suffix' 
                    ? $generatedCode . $separator . $processedAddon
                    : $processedAddon . $separator . $generatedCode;
            }
            
            $fields[$codeField] = $generatedCode;
        }
    }
}
