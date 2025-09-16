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
    $k = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $s));
    $k = preg_replace('/[^a-z0-9\-]+/', '-', $k);
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