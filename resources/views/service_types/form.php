<?php

/**
 * ServiceType Form
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
        'Service Type Information',
        'Define the service type characteristics and delivery options',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setHelp('Display name for this service type')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., Installation')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('code')
                        ->setLabel('Type Code')
                        ->setHelp('Unique identifier code (auto-generated from name if left empty)')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., INSTALLATION')
                ),

            $form->row()
                ->withColumn(
                    $form->textarea('description')
                        ->setLabel('Description')
                        ->setHelp('Detailed description of this service type and what it includes')
                        ->setAttribute('placeholder', 'e.g., On-site installation and configuration services')
                        ->setAttribute('rows', '3')
                )
                ->withColumn()
        ]
    ),

    $form->fieldset(
        'Delivery Characteristics',
        'Define how this service type is delivered and estimated timing',
        [
            $form->row()
                ->withColumn(
                    $form->toggle('requires_site_visit')
                        ->setLabel('Requires Site Visit')
                        ->setHelp('Whether this service type requires physical presence at customer location')
                )
                ->withColumn(
                    $form->toggle('supports_remote')
                        ->setLabel('Supports Remote Delivery')
                        ->setHelp('Whether this service can be delivered remotely')
                        ->setAttribute('value', '1')
                ),

            $form->row()
                ->withColumn(
                    $form->number('estimated_duration_hours')
                        ->setLabel('Estimated Duration (Hours)')
                        ->setHelp('Average time in hours to complete this type of service')
                        ->setAttribute('min', '0')
                        ->setAttribute('step', '0.25')
                        ->setAttribute('placeholder', 'e.g., 4.50')
                )
                ->withColumn()
        ]
    )

])->setDescription('Service Type Configuration');

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
                    ->setAttribute('value', $service->name ?? 'B2CNC-' . $service->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("SKU")
                    ->setAttribute('value', $service->sku)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("Service ID")
                    ->setAttribute('value', $service->id ?? "Service #{$service->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );


            $service_fields[] = $row;
        }
    } else {
        $service_fields[] = $form->text('No Services')
            ->setAttribute('value', 'No services are currently associated with this deliverable')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services using this service type',
        $service_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
