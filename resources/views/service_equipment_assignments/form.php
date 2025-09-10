<?php

/**
 * ServiceEquipmentAssignment Form View
 * 
 * This view displays a form for creating/editing ServiceEquipmentAssignment.
 * Add your form fields and functionality here.
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\ServiceEquipment;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Deliverable Assignment',
        'Define coverage relationships between services',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Primary Service')
                        ->setHelp('The main service that this coverage applies to')
                        ->setOptions(['Select Primary Service' => null])
                        ->setModelOptions(Service::class, 'name', 'id')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('equipment_id')
                        ->setLabel('Equipment')
                        ->setHelp('The deliverable that will be offered by this service')
                        ->setOptions(['Select Delivery Method' => null])
                        ->setModelOptions(ServiceEquipment::class, 'name', 'id')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->number('quantity')
                        ->setLabel('Quantity')
                        ->setHelp('The main service that this coverage applies to')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->toggle('required')
                        ->setLabel('Required')
                        ->setHelp('The deliverable that will be offered by this service')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->toggle('substitute_ok')
                        ->setLabel('Substitute Ok')
                        ->setHelp('The main service that this coverage applies to')
                        ->markLabelRequired()
                )
                ->withColumn()
        ]
    )

])->setDescription('Service Deliverable Assignment');

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
