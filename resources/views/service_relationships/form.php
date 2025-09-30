<?php

/**
 * ServiceRelationship Form View
 * 
 * This view displays a form for creating/editing ServiceRelationship.
 * Add your form fields and functionality here.
 */

use MakerMaker\Models\Service;

// Form instance
echo $form->open();

echo to_resource('ServiceRelationship', 'index', 'Back To Service Dependencies');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Relationship',
        'Define relationships between services',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Primary Service')
                        ->setHelp('Select the main service in this relationship')
                        ->setModelOptions(Service::class, 'name', 'id', 'Select Service')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('relation_type')
                        ->setLabel('Relationship Type')
                        ->setHelp('Select how these services relate to each other')
                        ->setOptions([
                            'Select Relation Type' => null,
                            'Prerequisite - Must complete first' => 'prerequisite',
                            'Dependency - Service relies on' => 'dependency',
                            'Incompatible With - Cannot coexist' => 'incompatible_with',
                            'Substitute For - Alternative solution' => 'substitute_for',
                            'Complements - Work better together' => 'complements',
                            'Replaces - Supersedes older service' => 'replaces',
                            'Requires - Needs to function' => 'requires',
                            'Enables - Makes other services possible' => 'enables',
                            'Conflicts With - Technical conflicts' => 'conflicts_with'
                        ])
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('related_service_id')
                        ->setLabel('Related Service')
                        ->setHelp('Select the service that has this relationship with the primary service')
                        ->setModelOptions(Service::class, 'name', 'id', 'Select Related Service')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->number('strength')
                        ->setLabel('Relationship Strength')
                        ->setHelp('Strength indicator: 1=weak, 10=critical')
                        ->setAttribute('min', 1)
                        ->setAttribute('max', 10)
                        ->setAttribute('step', 1)
                        ->setDefault(5)
                )
                ->withColumn(),
            $form->row()
                ->withColumn(
                    $form->textarea('notes')
                        ->setLabel('Relationship Notes')
                        ->setHelp('Additional details about this service relationship')
                )
                ->withColumn()
        ]
    )
])->setDescription('Service Relationship');

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
                            ->setLabel('Service Relationship ID')
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