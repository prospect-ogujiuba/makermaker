<?php

/**
 * ServiceRelationship Form View
 * 
 * This view displays a form for creating/editing ServiceRelationship.
 * Add your form fields and functionality here.
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\ServiceDeliverable;

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
                )->withColumn(
                    $form->select('relation_type')
                        ->setLabel('Relation Type')
                        ->setHelp('The main service that this coverage applies to')
                        ->setOptions(
                            [
                                'Select Relation Type' => null,
                                'Prerequisite' => 'prerequisite',
                                'Dependency' => 'dependency',
                                'Incompatible With' => 'incompatible_with',
                                'Substitute For' => 'substitute_for'
                            ]
                        )
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('related_service_id')
                        ->setLabel('Service Deliverable')
                        ->setHelp('The deliverable that will be offered by this service')
                        ->setOptions(['Select Delivery Method' => null])
                        ->setModelOptions(Service::class, 'name', 'id')
                        ->markLabelRequired()
                ),
        
            $form->row()
                ->withColumn(
                    $form->textarea('notes')
                        ->setLabel('Primary Service')
                        ->setHelp('The main service that this coverage applies to')
                        ->markLabelRequired()
                )

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
