<?php

/**
 * ServiceDeliveryMethodAssignment Form View
 * 
 * This view displays a form for creating/editing ServiceDeliveryMethodAssignment.
 * Add your form fields and functionality here.
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\ServiceDeliveryMethod;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Delivery Method Assignment',
        'Define how this service will be delivered',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Service')
                        ->setHelp('Select the service this delivery method applies to')
                        ->setModelOptions(Service::class, 'name', 'id','Select Service')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('delivery_method_id')
                        ->setLabel('Delivery Method')
                        ->setHelp('Select how this service will be delivered (e.g., On-site, Remote)')
                        ->setModelOptions(ServiceDeliveryMethod::class, 'name', 'id', 'Select Delivery Method')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->number('lead_time_days')
                        ->setLabel('Lead Time (days)')
                        ->setHelp('Number of days required to prepare and start service delivery')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->number('sla_hours')
                        ->setLabel('Service Level Hours')
                        ->setHelp('Service level agreement response time in hours')
                ),
            $form->row()
                ->withColumn(
                    $form->number('surcharge')
                        ->setLabel('Delivery Surcharge')
                        ->setHelp('Additional cost for this delivery method (enter 0 if no surcharge)')
                        ->markLabelRequired()
                )
                ->withColumn()
        ]
    )
])->setDescription('Service Delivery Method Assignment');

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
