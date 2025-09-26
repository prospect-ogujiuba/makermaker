<?php

/**
 * Enhanced Service Form
 */

use MakerMaker\Models\ServiceCategory;
use MakerMaker\Models\ServiceType;
use MakerMaker\Models\ComplexityLevel;

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
                        ->setAttribute('maxlength', '64')
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
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., voip-phone-system-installation')
                )
                ->withColumn(),
            $form->row()
                ->withColumn(
                    $form->text('short_desc')
                        ->setLabel('Short Description')
                        ->setHelp('Brief summary for listings and previews (max 512 characters)')
                        ->setAttribute('maxlength', '512')
                        ->setAttribute('placeholder', 'Brief description of the service for listings...')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->textarea('long_desc')
                        ->setLabel('Long Description')
                        ->setHelp('Detailed service description, requirements, and deliverables')
                        ->setAttribute('maxlength', '2000')
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
                        ->setModelOptions(ServiceCategory::class, 'name', 'id', 'Select Category')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('service_type_id')
                        ->setLabel('Service Type')
                        ->setHelp('Type classification for this service')
                        ->setModelOptions(ServiceType::class, 'name', 'id', 'Select Service Type')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->select('complexity_id')
                        ->setLabel('Complexity Level')
                        ->setHelp('Complexity classification for pricing and resource allocation')
                        ->setModelOptions(ComplexityLevel::class, 'name', 'id', 'Service Complexity')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('skill_level')
                        ->setLabel('Skill Level Required')
                        ->setHelp('Minimum skill level required to deliver this service')
                        ->setOptions([
                            'Select Skill Level' => NULL,
                            'Entry Level' => 'entry',
                            'Intermediate' => 'intermediate',
                            'Advanced' => 'advanced',
                            'Expert' => 'expert',
                            'Specialist' => 'specialist'
                        ])
                        ->setAttribute('placeholder', 'Select skill level')
                )
        ]
    ),

    $form->fieldset(
        'Service Configuration',
        'Quantity limits, timing, and service flags',
        [
            $form->row()
                ->withColumn(
                    $form->toggle('is_active')
                        ->setLabel('Active Service')
                        ->setHelp('Whether this service is currently available for purchase')
                )
                ->withColumn(
                    $form->toggle('is_featured')
                        ->setLabel('Featured Service')
                        ->setHelp('Mark as featured service for highlighted display')
                ),
            $form->row()
                ->withColumn(
                    $form->number('minimum_quantity')
                        ->setLabel('Minimum Quantity')
                        ->setHelp('Minimum order quantity for this service')
                        ->setAttribute('min', '0')
                        ->setAttribute('step', '0.01')
                        ->setDefault('1')
                        ->setAttribute('placeholder', '1.000')
                )
                ->withColumn(
                    $form->number('maximum_quantity')
                        ->setLabel('Maximum Quantity')
                        ->setHelp('Maximum order quantity (leave empty for unlimited)')
                        ->setAttribute('min', '1')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('placeholder', 'e.g., 100.000')
                ),
            $form->row()
                ->withColumn(
                    $form->number('estimated_hours')
                        ->setLabel('Estimated Hours')
                        ->setHelp('Estimated time to complete this service')
                        ->setAttribute('min', '0')
                        ->setAttribute('step', '0.25')
                        ->setAttribute('placeholder', 'e.g., 8.50')
                )
                ->withColumn()
        ]
    ),

    $form->fieldset(
        'Service Metadata',
        'Additional configuration and properties',
        [
            $form->repeater('metadata')
                ->setLabel('Metadata')
                ->setHelp('Additional service configuration in JSON format')
                ->setFields(
                    $form->row(
                        $form->text('key')
                            ->setLabel('Key'),
                        $form->text('value')
                            ->setLabel('Value'),
                    )
                )
                ->setTitle('Attribute Options')
                ->confirmRemove()
        ]
    )

])->setDescription('Service Configuration');

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
