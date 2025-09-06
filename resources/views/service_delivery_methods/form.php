<?php

/**
 * ServiceDeliveryMethod Form
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
        'Delivery Method Overview',
        'Define the delivery method characteristics and code',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setHelp('Descriptive name for this delivery method (e.g., "Tiered", "Hourly", "Monthly Subscription", "Per Unit")')
                        ->setAttribute('maxlength', '100')
                        ->setAttribute('placeholder', 'e.g., Advanced Implementation')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('code')
                        ->setLabel('Delivery method Code')
                        ->setHelp('i.e. Annual Contract = ANNUAL')
                        ->setAttribute('min', '1')
                        ->setAttribute('step', '1')
                        ->markLabelRequired()
                )
        ]
    )

])->setDescription('Delivery method information');

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

    // Nested Tabs for relationship information
    $relationshipNestedTabs = \TypeRocket\Elements\Tabs::new()
        ->layoutTop();

    $services_fields = [];


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

            $services_fields[] = $row;
        }
    } else {
        $service_fields[] = $form->text('No Services')
            ->setAttribute('value', 'No services are currently associated with this delivery method')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services using this delivery method',
        $services_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationship', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Relationship information');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
