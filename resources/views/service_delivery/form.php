<?php

/**
 * ServiceDelivery Form View
 * 
 * This view displays a form for creating/editing ServiceDelivery.
 * Add your form fields and functionality here.
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\DeliveryMethod;

// Form instance
echo $form->open();

echo to_resource('ServiceDelivery', 'index', 'Back To Service Deliveries');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Delivery',
        'Define how this service will be delivered',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Service')
                        ->setHelp('Select the service this delivery method applies to')
                        ->setModelOptions(Service::class, 'name', 'id', 'Select Service')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('delivery_method_id')
                        ->setLabel('Delivery Method')
                        ->setHelp('Select how this service will be delivered (e.g., On-site, Remote)')
                        ->setModelOptions(DeliveryMethod::class, 'name', 'id', 'Select Delivery Method')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->number('lead_time_days')
                        ->setLabel('Lead Time (Days)')
                        ->setHelp('Number of days required to prepare and start service delivery')
                        ->setAttribute('min', 0)
                        ->setAttribute('step', 1)
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->number('sla_hours')
                        ->setLabel('SLA Hours')
                        ->setHelp('Service level agreement response time in hours')
                        ->setAttribute('min', 0)
                        ->setAttribute('step', 1)
                )
                ->withColumn(
                    $form->number('surcharge')
                        ->setLabel('Delivery Surcharge')
                        ->setHelp('Additional cost for this delivery method')
                        ->setAttribute('min', 0)
                        ->setAttribute('step', 0.01)
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->toggle('is_default')
                        ->setLabel('Default Method')
                        ->setHelp('Check if this is the default delivery method for this service')
                        ->setText('Yes', 'No')
                )
                ->withColumn()
                ->withColumn()
        ]
    )
])->setDescription('Service Delivery');

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
                            ->setLabel('Service Deliverable Assignment ID')
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
