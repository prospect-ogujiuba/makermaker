<?php

/**
 * ServiceAddons Form
 * File: resources/views/admin/service_addons/form.php
 * 
 * Form for creating and editing service addons
 */

use MakerMaker\Models\ServiceAddons;
use MakerMaker\Models\Service;

/** @var \TypeRocket\Elements\Form $form */
/** @var \MakerMaker\Models\ServiceAddons $serviceAddon */
/** @var string $button */

echo $form->open();

/**
 * Basic Information Section
 */
$basicInfo = $form->fieldset(
    'Basic Information',
    'Core addon information and service relationship',
    [
        $form->row()
            ->withColumn(
                $form->select('Service')
                    ->setName('service_id')
                    ->setModelOptions(Service::class, 'name')
                    ->setHelp('Select the parent service for this addon')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->text('Addon Name')
                    ->setName('addon_name')
                    ->setHelp('Enter the name of the addon')
                    ->setAttribute('maxlength', '200')
                    ->markLabelRequired()
            ),

        $form->textarea('Description')
            ->setName('addon_description')
            ->setHelp('Detailed description of what this addon provides')
    ]
);

/**
 * Configuration Section
 */
$configuration = $form->fieldset(
    'Addon Configuration',
    'Addon type, pricing, and billing settings',
    [
        $form->row()
            ->withColumn(
                $form->select('Addon Type')
                    ->setName('addon_type')
                    ->setOptions([
                        'Upgrade' => 'upgrade',
                        'Additional Feature' => 'additional', 
                        'Extended Warranty' => 'extended_warranty',
                        'Training' => 'training',
                        'Support Package' => 'support'
                    ])
                    ->setHelp('Choose the type of addon this represents')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->text('Price')
                    ->setName('price')
                    ->setType('number')
                    ->setAttribute('step', '0.01')
                    ->setAttribute('min', '0')
                    ->setHelp('Enter the addon price')
                    ->markLabelRequired()
            ),

        $form->row()
            ->withColumn(
                $form->toggle('Recurring Billing')
                    ->setName('is_recurring')
                    ->setText('This is a recurring addon')
                    ->setHelp('Check if this addon has recurring billing')
            )
            ->withColumn(
                $form->select('Billing Frequency')
                    ->setName('billing_frequency')
                    ->setOptions([
                        'Not Applicable' => '',
                        'Monthly' => 'monthly',
                        'Quarterly' => 'quarterly', 
                        'Annually' => 'annually'
                    ])
                    ->setHelp('How often this addon is billed (only for recurring addons)')
            )
    ]
);

/**
 * Status and Ordering Section
 */
$statusOrdering = $form->fieldset(
    'Status & Ordering',
    'Activation status and display order',
    [
        $form->row()
            ->withColumn(
                $form->toggle('Active Status')
                    ->setName('is_active')
                    ->setText('Addon is active and available')
                    ->setHelp('Uncheck to disable this addon')
                    ->setDefault(true)
            )
            ->withColumn(
                $form->text('Sort Order')
                    ->setName('sort_order')
                    ->setType('number')
                    ->setAttribute('min', '0')
                    ->setDefault(0)
                    ->setHelp('Display order (lower numbers appear first)')
            )
    ]
);

// Save button
$save = $form->save($button ?? 'Save Service Addon');

// Create tabs layout
$tabs = \TypeRocket\Elements\Tabs::new()
    ->setFooter($save)
    ->layoutLeft();

// Add tabs
$tabs->tab('Basic Information', 'admin-post', [$basicInfo])
    ->setDescription('Addon name, service, and description');

$tabs->tab('Configuration', 'admin-settings', [$configuration])
    ->setDescription('Addon type, pricing, and billing settings');

$tabs->tab('Status & Order', 'admin-generic', [$statusOrdering])
    ->setDescription('Activation status and display order');

// Render the tabbed interface
$tabs->render();

echo $form->close();