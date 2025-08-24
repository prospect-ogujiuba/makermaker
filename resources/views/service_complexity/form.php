<?php

/**
 * ServiceComplexity Form
 * File: resources/views/admin/service_complexity/form.php
 * 
 * Form for creating and editing service complexity levels
 */

use MakerMaker\Models\ServiceComplexity;

/** @var \TypeRocket\Elements\Form $form */
/** @var \MakerMaker\Models\ServiceComplexity $serviceComplexity */
/** @var string $button */

echo $form->open();

/**
 * Basic Information Section
 */
$basicInfo = $form->fieldset(
    'Complexity Level Information',
    'Basic complexity level details and configuration',
    [
        $form->row()
            ->withColumn(
                $form->text('Complexity Name')
                    ->setName('name')
                    ->setHelp('Name of the complexity level (e.g., Basic, Intermediate, Advanced)')
                    ->setAttribute('maxlength', '100')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->text('URL Slug')
                    ->setName('slug')
                    ->setHelp('URL-friendly slug (auto-generated if empty)')
                    ->setAttribute('maxlength', '100')
                    ->markLabelRequired()
            ),

        $form->textarea('Description')
            ->setName('description')
            ->setHelp('Detailed description of what this complexity level represents and when it should be used')
    ]
);

/**
 * Configuration Section
 */
$configuration = $form->fieldset(
    'Display & Status Configuration',
    'Sort order and availability settings',
    [
        $form->row()
            ->withColumn(
                $form->text('Sort Order')
                    ->setName('sort_order')
                    ->setType('number')
                    ->setAttribute('min', '0')
                    ->setDefault(0)
                    ->setHelp('Display order (lower numbers appear first in dropdowns)')
            )
            ->withColumn(
                $form->toggle('Active Status')
                    ->setName('is_active')
                    ->setText('This complexity level is active and available for selection')
                    ->setHelp('Inactive complexity levels are hidden from service forms')
                    ->setDefault(true)
            )
    ]
);

// Save button
$save = $form->save($button ?? 'Save Complexity Level');

// Simple layout without tabs (complexity levels are simple lookup data)
echo $basicInfo;
echo $configuration;
echo $save;

echo $form->close();
?>

<style>
.badge { padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
.badge-success { background-color: #00a32a; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.badge-warning { background-color: #dba617; color: white; }
.badge-info { background-color: #007cba; color: white; }
.text-muted { color: #6c757d !important; }
.services-count { display: inline-block; text-align: center; }
.sort-order { font-weight: bold; }
code { background-color: #f1f1f1; padding: 2px 4px; border-radius: 2px; font-size: 12px; }
</style>

<script>
jQuery(document).ready(function($) {
    // Auto-generate slug from name if slug is empty
    $('input[name="name"]').on('input', function() {
        const slugField = $('input[name="slug"]');
        
        // Only auto-generate if slug field is empty
        if (!slugField.val()) {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .trim()
                .replace(/[^a-z0-9-]/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
            
            slugField.val(slug);
        }
    });
    
    // Validate slug format
    $('input[name="slug"]').on('input', function() {
        const slug = $(this).val();
        const validSlug = slug.toLowerCase()
            .replace(/[^a-z0-9-]/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-+|-+$/g, '');
            
        if (slug !== validSlug) {
            $(this).val(validSlug);
        }
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        const name = $('input[name="name"]').val().trim();
        const slug = $('input[name="slug"]').val().trim();
        
        if (!name) {
            e.preventDefault();
            alert('Please enter a complexity level name.');
            $('input[name="name"]').focus();
            return false;
        }
        
        if (!slug) {
            e.preventDefault();
            alert('Please enter a slug for this complexity level.');
            $('input[name="slug"]').focus();
            return false;
        }
        
        const sortOrder = parseInt($('input[name="sort_order"]').val());
        if (isNaN(sortOrder) || sortOrder < 0) {
            $('input[name="sort_order"]').val(0);
        }
    });
});
</script>