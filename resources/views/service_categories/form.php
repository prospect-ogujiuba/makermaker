<?php

/**
 * Service Category Form - Hierarchical Category Management
 * File: resources/views/admin/service-category/form.php
 * 
 * This form handles Service Category creation and editing with hierarchical
 * parent/child relationships, icons, and ordering based on the database structure.
 */

use MakerMaker\Models\ServiceCategory;

/** @var \App\Elements\Form $form */
/** @var \MakerMaker\Models\ServiceCategory $serviceCategory */
/** @var string $button */

// Helper function to get parent category options
function getParentCategoryOptions($currentId = null)
{
    $categories = [];
    
    $options = ['None (Top Level)' => ''];
    
    return $options;
}

// Common dashicon options for service categories
function getIconOptions()
{
    return [
        'Communications' => 'dashicons-phone',
        'Networking' => 'dashicons-networking',
        'Security' => 'dashicons-shield-alt',
        'Settings/Management' => 'dashicons-admin-settings',
        'Tools/Installation' => 'dashicons-admin-tools',
        'Support' => 'dashicons-sos',
        'Cloud/Hosting' => 'dashicons-cloud',
        'Analytics' => 'dashicons-chart-bar',
        'Users/Access' => 'dashicons-admin-users',
        'Lock/Security' => 'dashicons-lock',
        'Building/Location' => 'dashicons-building',
        'Desktop/Computer' => 'dashicons-desktop',
        'Tablet/Mobile' => 'dashicons-tablet',
        'Camera/Video' => 'dashicons-video-alt3',
        'Microphone/Audio' => 'dashicons-microphone',
        'Email/Messages' => 'dashicons-email-alt',
        'Cart/E-commerce' => 'dashicons-cart',
        'Portfolio/Projects' => 'dashicons-portfolio',
        'Performance' => 'dashicons-performance',
        'Location/GPS' => 'dashicons-location',
        'Money/Finance' => 'dashicons-money-alt',
        'Star/Featured' => 'dashicons-star-filled',
        'Heart/Favorites' => 'dashicons-heart',
        'Lightbulb/Ideas' => 'dashicons-lightbulb',
        'Hammer/Maintenance' => 'dashicons-hammer',
        'Universal Access' => 'dashicons-universal-access-alt',
        'Backup/Archive' => 'dashicons-backup',
        'Update/Refresh' => 'dashicons-update',
        'Download' => 'dashicons-download',
        'Upload' => 'dashicons-upload'
    ];
}

echo $form->open();

/**
 * Category Information Section
 * Core category details based on wp_b2bcnc_service_categories table
 */
$categoryInfo = $form->fieldset(
    'Category Information',
    'Basic service category information and hierarchy settings',
    [
        $form->row()
            ->withColumn(
                $form->text('Category Name')
                    ->setName('name')
                    ->setHelp('Service category name (max 100 characters)')
                    ->setAttribute('maxlength', '100')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->text('URL Slug')
                    ->setName('slug')
                    ->setHelp('URL-friendly slug (max 100 characters, auto-generated if empty)')
                    ->setAttribute('maxlength', '100')
            ),

        $form->select('Parent Category')
            ->setName('parent_id')
            ->setOptions(getParentCategoryOptions($serviceCategory->id ?? null))
            ->setHelp('Select parent category to create hierarchical structure'),

        $form->textarea('Description')
            ->setName('description')
            ->setHelp('Detailed description of this service category')
            ->setAttribute('rows', '4'),

        $form->row()
            ->withColumn(
                $form->select('Category Icon')
                    ->setName('icon')
                    ->setOptions(getIconOptions())
                    ->setHelp('Choose a dashicon for visual identification')
            )
            ->withColumn(
                $form->text('Sort Order')
                    ->setName('sort_order')
                    ->setType('number')
                    ->setAttribute('min', '0')
                    ->setDefault(0)
                    ->setHelp('Display order (lower numbers appear first)')
            ),

        $form->toggle('Active Category')
            ->setName('is_active')
            ->setText('This category is active and visible')
            ->setDefault(true)
    ]
);

// Save button
$save = $form->save($button ?? 'Save Category');

// Create simple layout without tabs (categories are simpler than services)
echo $categoryInfo;
echo $save;

echo $form->close();