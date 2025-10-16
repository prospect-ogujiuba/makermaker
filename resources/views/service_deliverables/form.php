<?php

/**
 * ServiceDeliverable Form View
 * 
 * This view displays a form for creating/editing ServiceDeliverable.
 * Add your form fields and functionality here.
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\Deliverable;

// Form instance
echo $form->open();

echo to_resource('ServiceDeliverable', 'index', 'Back To Service Deliverables');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Deliverable',
        'Define which deliverables are included with this service and their order',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Service')
                        ->setHelp('Select the service that will include this deliverable')
                        ->setModelOptions(Service::class, 'name', 'id', 'Select Service')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('deliverable_id')
                        ->setLabel('Deliverable')
                        ->setHelp('Select the deliverable that will be included with this service')
                        ->setModelOptions(Deliverable::class, 'name', 'id', 'Select Deliverable')
                        ->markLabelRequired()
                ),
            $form->row()
                 ->withColumn(
                    $form->number('sequence_order')
                        ->setLabel('Sequence Order')
                        ->setHelp('Order in which this deliverable should be completed (leave empty for no specific order)')
                        ->setAttribute('min', '1')
                        ->setAttribute('placeholder', 'e.g., 1, 2, 3...')
                )->withColumn(
                    $form->toggle('is_optional')
                        ->setLabel('Is Optional')
                        ->setHelp('Can the customer choose to exclude this deliverable?')
                )
               
        ]
    )
])->setDescription('Service Deliverable');

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
                            ->setLabel('Service Deliverable ID')
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
