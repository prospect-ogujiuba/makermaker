<?php

/**
 * Enhanced Service Form
 */

use MakerMaker\Models\ServiceCategory;
use MakerMaker\Models\ServiceType;
use MakerMaker\Models\ServiceComplexity;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Information',
        'Core service details and identification',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Service Name')
                        ->setHelp('Name for this service (e.g., "Advanced Network Setup")')
                        ->setAttribute('maxlength', '255')
                        ->setAttribute('placeholder', 'e.g., VoIP Phone System Installation')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('sku')
                        ->setLabel('Service SKU')
                        ->setHelp('Unique service identifier for billing and inventory')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., B2C-VOIP-001')
                ),
            $form->row()
                ->withColumn(
                    $form->text('slug')
                        ->setLabel('Service Slug')
                        ->setHelp('URL-friendly identifier (auto-generated from name if empty)')
                        ->setAttribute('maxlength', '128')
                        ->setAttribute('placeholder', 'e.g., voip-phone-system-installation')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('default_unit')
                        ->setLabel('Default Pricing Unit')
                        ->setHelp('Default unit for pricing this service')
                ),
            $form->row()
                ->withColumn(
                    $form->textarea('short_desc')
                        ->setLabel('Short Description')
                        ->setHelp('Brief summary for listings and previews (max 512 characters)')
                        ->setAttribute('maxlength', '512')
                        ->setAttribute('rows', '3')
                        ->setAttribute('placeholder', 'Brief description of the service for listings...')
                ),
            $form->row()
                ->withColumn(
                    $form->textarea('long_desc')
                        ->setLabel('Long Description')
                        ->setHelp('Detailed service description, requirements, and deliverables')
                        ->setAttribute('placeholder', 'Detailed description of the service, what\'s included, requirements...')
                )
        ]
    ),

    $form->fieldset(
        'Service Classification',
        'Categorize and classify this service',
        [
            $form->row()
                ->withColumn(
                    $form->select('category_id')
                        ->setLabel('Service Category')
                        ->setHelp('Primary category for this service')
                        ->setOptions(['Select Category' => null])
                        ->setModelOptions(ServiceCategory::class, 'name', 'id')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('service_type_id')
                        ->setLabel('Service Type')
                        ->setHelp('Type classification for this service')
                        ->setOptions(['Select Service Type' => null])
                        ->setModelOptions(ServiceType::class, 'name', 'id')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->select('complexity_id')
                        ->setLabel('Complexity Level')
                        ->setHelp('Complexity classification for pricing and resource allocation')
                        ->setOptions(['Select Complexity' => null])
                        ->setModelOptions(ServiceComplexity::class, 'name', 'id')
                        ->markLabelRequired()
                )
                ->withColumn(),
        ]
    ),

    $form->fieldset(
        'Service Status',
        'Availability and add-on configuration',
        [
            $form->row()
                ->withColumn(
                    $form->toggle('is_active')
                        ->setLabel('Active Service')
                        ->setHelp('Whether this service is currently available for purchase')
                        ->setText('Service is active and available')
                )
                ->withColumn(
                    $form->toggle('is_addon')
                        ->setLabel('Add-on Service')
                        ->setHelp('Mark as an add-on service (requires a primary service)')
                        ->setText('This is an add-on service')
                )
        ]
    ),

    $form->fieldset(
        'Service Metadata',
        'Additional configuration and properties',
        [
            $form->row()
                ->withColumn(
                    $form->repeater('metadata')
                        ->setLabel('Service Metadata (JSON)')
                        ->setHelp('Additional service configuration in JSON format')
                        ->setAttribute('rows', '8')
                        ->setAttribute('placeholder', '{"pricing_rules": {}, "requirements": [], "deliverables": []}')
                        ->setAttribute('data-format', 'json')
                )
        ]
    )

])->setDescription('Service Details');

// Conditional System Info Tab
if (isset($current_id)) {
    // System Info Tab
    $tabs->tab('System', 'info', [
        $form->fieldset(
            'System Info',
            'Core system metadata fields',
            [
                $form->row()
                    ->withColumn(
                        $form->text('id')
                            ->setLabel('Service ID')
                            ->setHelp('System generated unique identifier')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    )
                    ->withColumn(
                        $form->text('version')
                            ->setLabel('Version')
                            ->setHelp('Optimistic locking version number')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    ),
                $form->row()
                    ->withColumn(
                        $form->text('created_at')
                            ->setLabel('Created At')
                            ->setHelp('Record creation timestamp')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    )
                    ->withColumn(
                        $form->text('updated_at')
                            ->setLabel('Updated At')
                            ->setHelp('Last update timestamp')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    ),
                $form->row()
                    ->withColumn(
                        $form->text('created_by_user')
                            ->setLabel('Created By')
                            ->setHelp('User who originally created this record')
                            ->setAttribute('value', $createdBy->user_nicename ?? 'System')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    )
                    ->withColumn(
                        $form->text('updated_by_user')
                            ->setLabel('Last Updated By')
                            ->setHelp('User who last updated this record')
                            ->setAttribute('value', $updatedBy->user_nicename ?? 'System')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    ),
                $form->row()
                    ->withColumn(
                        $form->text('deleted_at')
                            ->setLabel('Deleted At')
                            ->setHelp('Timestamp when this record was soft-deleted, if applicable')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                            ->setAttribute('disabled', true)
                    )
                    ->withColumn()
            ]
        )
    ])->setDescription('System information');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
