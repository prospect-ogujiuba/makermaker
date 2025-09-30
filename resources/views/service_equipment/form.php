<?php

/**
 * ServiceEquipment Form View
 * 
 * This view displays a form for creating/editing ServiceEquipment.
 * Add your form fields and functionality here.
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\Equipment;

// Form instance
echo $form->open();

echo to_resource('ServiceEquipment', 'index', 'Back To Service Equipment');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Equipment Assignment',
        'Define equipment requirements for this service',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Service')
                        ->setHelp('Select the service that requires this equipment')
                        ->setModelOptions(Service::class, 'name', 'id', 'Select Service')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('equipment_id')
                        ->setLabel('Equipment')
                        ->setHelp('Select the equipment needed for this service')
                        ->setModelOptions(Equipment::class, 'name', 'id', 'Select Equipment')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->number('quantity')
                        ->setLabel('Quantity')
                        ->setAttribute('step', '0.001')
                        ->setAttribute('min', '0.001')
                        ->setAttribute('max', '10000')
                        ->setHelp('Number of units required (0.001 - 10,000)')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('quantity_unit')
                        ->setLabel('Quantity Unit')
                        ->setAttribute('maxlength', '16')
                        ->setAttribute('placeholder', 'e.g., each, feet, meters')
                        ->setHelp('Unit of measurement for quantity')
                ),
            $form->row()
                ->withColumn(
                    $form->toggle('cost_included')
                        ->setLabel('Cost Included')
                        ->setHelp('Check if equipment cost is included in service price')
                        ->setText('Yes', 'No')
                )
                ->withColumn( $form->toggle('required')
                        ->setLabel('Required')
                        ->setHelp('Check if this equipment is mandatory for the service')
                        ->setText('Yes', 'No'))

        ]
    )
])->setDescription('Service Equipment');

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

    $relationshipNestedTabs = \TypeRocket\Elements\Tabs::new()
        ->layoutTop();

    if ($equipment && count($equipment) > 0) {
        foreach ($equipment as $Equipment) {
            $row = $form->row();

            $row->column(
                $form->text("Equipment Name")
                    ->setAttribute('value', $Equipment->name ?? 'B2CNC-' . $Equipment->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("Equipment Manufacturer")
                    ->setAttribute('value', $Equipment->manufacturer ?? "Service #{$Equipment->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("SKU")
                    ->setAttribute('value', $Equipment->sku)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $service_equipment_fields[] = $row;
        }
    } else {
        $service_equipment_fields[] = $form->text('No Equipment')
            ->setAttribute('value', 'No equipment are currently associated with this service')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Equipment', 'admin-post', $form->fieldset(
        'Related Equipment',
        'Equipment assigned to this service',
        $service_equipment_fields
    ));

    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
