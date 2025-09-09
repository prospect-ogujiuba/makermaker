<?php

/**
 * ServiceAttributeValue Form
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\ServiceAttributeDefinition;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

$dataType = null;
if ($form->getModel() && method_exists($form->getModel(), 'attributeDefinition')) {
    $rel = $form->getModel()->attributeDefinition;
    if ($rel) {
        $dataType = $rel->data_type;
    }
}

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Attribute Value',
        'Define the attribute value for this service attribute definition',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Service')
                        ->setHelp('Service this attribute value applies to')
                        ->setOptions(['Select Service' => NULL])
                        ->setModelOptions(Service::class, 'name', 'id')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('attribute_definition_id')
                        ->setLabel('Attribute Definition')
                        ->setHelp('Attribute definition this value applies to')
                        ->setOptions(['Select Attribute Definition' => NULL])
                        ->setModelOptions(ServiceAttributeDefinition::class, 'label', 'id')
                        ->markLabelRequired()
                ),

            $form->row()
                ->withColumn(
                    $form->text('data_type_display')
                        ->setLabel('Expected Data Type')
                        ->setHelp('Data type for this attribute (from definition)')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)              // keep it out of POST
                        ->setAttribute('value',$dataType ?: 'Select attribute definition') // <-- use setValue, not setAttribute('value', ...)
                        ->setAttribute('class', 'tr-field-readonly')
                )
                ->withColumn(
                    $form->text('value')
                        ->setLabel('Value')
                        ->setHelp('Enter the value for this attribute (will be automatically typed based on the selected attribute definition)')
                        ->markLabelRequired()
                )
        ]
    )
])->setDescription('Attribute Value');

// Conditional
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
                            ->setLabel('ID')
                            ->setHelp('System generated ID')
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
                            ->setAttribute('value', $createdBy->user_nicename ?? '')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    )
                    ->withColumn(
                        $form->text('updated_by_user')
                            ->setLabel('Last Updated By')
                            ->setHelp('User who last updated this record')
                            ->setAttribute('value', $updatedBy->user_nicename ?? '')
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
                    )
                    ->withColumn()
            ]
        )
    ])->setDescription('System information');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
