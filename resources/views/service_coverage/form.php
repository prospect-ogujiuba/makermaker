<?php

/**
 * ServiceCoverage Form
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\CoverageArea;

// Form instance
echo $form->open();

echo to_resource('ServiceCoverage', 'index', 'Back To Service Coverage');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Coverage',
        'Define coverage relationships between services and geographic areas',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Service')
                        ->setHelp('Select the service that will be available in this coverage area')
                        ->setModelOptions(Service::class, 'name', 'id', 'Select Service')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('coverage_area_id')
                        ->setLabel('Coverage Area')
                        ->setHelp('Select the geographic area where this service is available')
                        ->setModelOptions(CoverageArea::class, 'name', 'id', 'Select Coverage Area')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->number('delivery_surcharge')
                        ->setLabel('Delivery Surcharge')
                        ->setHelp('Additional cost for delivery to this area (0.00 for no surcharge)')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('min', '0')
                        ->setAttribute('placeholder', 'e.g., 0.00')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->number('lead_time_adjustment_days')
                        ->setLabel('Lead Time Adjustment (Days)')
                        ->setHelp('Additional days needed for delivery to this area (use negative for faster, 0 for standard)')
                        ->setAttribute('placeholder', 'e.g., 0')
                        ->markLabelRequired()
                )
        ]
    )

])->setDescription('Service Coverage');

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
                            ->setLabel('Service Coverage ID')
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
