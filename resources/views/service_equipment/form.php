<?php

/**
 * ServiceEquipment Form
 */

// Form instance
echo $form->open();

echo to_resource('Equipment', 'index', 'Back To Equipment');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Equipment',
        'Define the service equipment characteristics',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setHelp('Display name for this equipment item')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., Cisco Catalyst Switch')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('manufacturer')
                        ->setLabel('Equipment Manufacturer')
                        ->setAttribute('maxlength', '64')
                        ->setHelp('Company or brand name that produces this equipment')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->text('sku')
                        ->setLabel('SKU')
                        ->setHelp('Stock Keeping Unit or model number for this equipment')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'Auto-generated from name if left empty')
                        ->markLabelRequired()
                )
                ->withColumn(),

            $form->repeater('specs')
                ->setLabel('Equipment Specifications')
                ->setHelp('Technical specifications and key features of this equipment')
                ->setFields(
                    $form->row(
                        $form->text('specification_name')
                            ->setLabel('Specification')
                            ->setAttribute('placeholder', 'e.g., Power Consumption'),
                        $form->text('specification_value')
                            ->setLabel('Value')
                            ->setAttribute('placeholder', 'e.g., 65W')
                    )
                )
                ->setTitle('Equipment Specification')
                ->confirmRemove()
                ->markLabelRequired()
        ]
    )

])->setDescription('Service Equipment');

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
                            ->setHelp('User ID who originally created this record')
                            ->setAttribute('value', $createdBy->user_nicename)
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)


                    )
                    ->withColumn(
                        $form->text('updated_by_user')
                            ->setLabel('Last Updated By')
                            ->setHelp('User ID who last updated this record')
                            ->setAttribute('value', $updatedBy->user_nicename)
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

    // Nested Tabs for Related Entities
    $relationshipNestedTabs = \TypeRocket\Elements\Tabs::new()
        ->layoutTop();

    $service_fields = [];

    if ($services && count($services) > 0) {
        foreach ($services as $service) {
            $row = $form->row();

            // Name Column (main content)
            $row->column(
                $form->text("Service ID")
                    ->setAttribute('value', $service->id ?? "Service #{$service->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // Additional info column (optional)
            $row->column(
                $form->text("Service Name")
                    ->setAttribute('value', $service->name ?? 'B2CNC-' . $service->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // ID Column (smaller width)
            $row->column(
                $form->text("SKU")
                    ->setAttribute('value', $service->sku)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $service_fields[] = $row;
        }
    } else {
        $service_fields[] = $form->text('No Services')
            ->setAttribute('value', 'No services are currently associated with this equipment')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services with this is a equipment',
        $service_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
