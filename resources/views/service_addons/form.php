<?php

/**
 * ServiceAddon Form
 */

use MakerMaker\Models\Service;

// Form instance
echo $form->open();

echo to_resource('ServiceAddon', 'index', 'Back To Service Addons');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Addon Configuration',
        'Define addon relationships between services',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Primary Service')
                        ->setHelp('Select the main service that this addon can be added to')
                        ->setModelOptions(Service::class, 'name', 'id', 'Select Primary Service')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('addon_service_id')
                        ->setLabel('Addon Service')
                        ->setHelp('Select the service that will be offered as an addon option')
                        ->setModelOptions(Service::class, 'name', 'id', 'Select Addon Service')
                        ->markLabelRequired()
                )
        ]
    ),

    $form->fieldset(
        'Addon Requirements',
        'Quantity limits and requirements',
        [
            $form->row()
                ->withColumn(
                    $form->toggle('required')
                        ->setLabel('Required Addon')
                        ->setHelp('Check if this addon must be included when purchasing the primary service')
                        ->setText('This addon is required')
                )
                ->withColumn(),
            $form->row()
                ->withColumn(
                    $form->number('min_qty')
                        ->setLabel('Minimum Quantity')
                        ->setHelp('Minimum quantity required when this addon is selected (0 = optional)')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('min', '0')
                        ->setDefault('0.000')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->number('max_qty')
                        ->setLabel('Maximum Quantity')
                        ->setHelp('Maximum quantity allowed (leave empty for no limit)')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('min', '0')
                )
        ]
    )

])->setDescription('Service Addon Configuration');

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
                            ->setLabel('Service Addon ID')
                            ->setHelp('System generated unique identifier')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    )
                    ->withColumn(),
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