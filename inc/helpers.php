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

add_action('typerocket_user_fields', function($form) {
    echo $form->text('Job Title');
    echo $form->gallery('Photo Gallery');
});