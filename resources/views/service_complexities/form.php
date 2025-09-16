<?php

/**
 * ServiceComplexity Form
 */

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [

    $form->fieldset(
        'Service Complexity',
        'Define service complexity',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setAttribute('placeholder', 'e.g., Advanced Implementation')
                        ->setAttribute('maxlength', '64')
                        ->setHelp('Descriptive name for this complexity level (max 64 characters)')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->number('level')
                        ->setLabel('Complexity Level')
                        ->setAttribute('placeholder', 'e.g., 9')
                        ->setAttribute('maxlength', '3')
                        ->setAttribute('min', '1')
                        ->setAttribute('max', '255')
                        ->setAttribute('step', '1')
                        ->setHelp('Numeric value from 0-255 representing difficulty/complexity ranking')
                        ->markLabelRequired()
                ),

            $form->row()
                ->withColumn(
                    $form->number('price_multiplier')
                        ->setLabel('Price Multiplier')
                        ->setAttribute('min', '0')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('placeholder', '1.00')
                        ->setHelp('Decimal factor to adjust base service pricing (1.00 = no change, 1.5 = 50% increase)')
                )
                ->withColumn(),
        ]
    )

])->setDescription('Service Complexity');

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



    if ($services && count($services) > 0) {
        foreach ($services as $service) {
            $row = $form->row();

            $row->column(
                $form->text("Service Name")
                    ->setAttribute('value', $service->name ?? "Service #{$service->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("SKU")
                    ->setAttribute('value', $service->sku ?? 'B2CNC-' . $service->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("ID")
                    ->setAttribute('value', $service->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $service_fields[] = $row;
        }
    } else {
        $service_fields[] = $form->text('No Services')
            ->setAttribute('value', 'No services are currently associated with this complexity level')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services using this complexity level',
        $service_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
