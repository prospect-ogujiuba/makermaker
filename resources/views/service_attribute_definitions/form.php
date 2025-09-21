<?php

/**
 * Enhanced ServiceAttributeDefinition Form
 */

use MakerMaker\Models\ServiceType;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Attribute Definition',
        'Define the attribute definition characteristics and pricing impact',
        [
            $form->row()
                ->withColumn(
                    $form->text('label')
                        ->setLabel('Attribute Name')
                        ->setHelp('Display name for this attribute')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., Number of Phone Lines')
                        ->markLabelRequired()
                )
                ->withColumn($form->select('service_type_id')
                    ->setLabel('Service Type')
                    ->setHelp('Service type this attribute applies to')
                    ->setOptions(['Select Service Type' => NULL])
                    ->setModelOptions(ServiceType::class, 'name', 'id')
                    ->markLabelRequired()),

            $form->row()
                ->withColumn(
                    $form->text('code')
                        ->setLabel('Attribute Code')
                        ->setHelp('Unique identifier code for system reference (uppercase)')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., PHONE_LINES')
                )
                ->withColumn(
                    $form->text('unit')
                        ->setLabel('Unit')
                        ->setHelp('Unit of measurement for this attribute (e.g., "lines", "GB", "users")')
                        ->setAttribute('maxlength', '32')
                        ->setAttribute('placeholder', 'e.g., lines')
                ),

            $form->row()
                ->withColumn(
                    $form->select('data_type')
                        ->setLabel('Attribute Data Type')
                        ->setHelp('Type of data this attribute stores')
                        ->setOptions([
                            'Integer' => 'int',
                            'Decimal' => 'decimal',
                            'Bool' => 'bool',
                            'Text' => 'text',
                            'Enum' => 'enum',
                            'Date' => 'date',
                            'DateTime' => 'datetime',
                            'Json' => 'json',
                            'Url' => 'url',
                            'Email' => 'email',
                        ])
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->toggle('required')
                        ->setLabel('Required')
                        ->setHelp('Whether this attribute must have a value when configuring services')

                ),

            $form->repeater('enum_options')
                ->setLabel('Enum Options')
                ->setHelp('Select options for this attribute')
                ->setFields(
                    $form->row(
                        $form->text('option')
                            ->setLabel('Option')
                            ->setAttribute('placeholder', 'e.g., Power Consumption'),
                    )
                )
                ->setTitle('Attribute Options')
                ->confirmRemove()
                ->markLabelRequired()
                ->when('data_type', '=', 'enum')
        ]
    )

])->setDescription('Attribute Definition');

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

            // Name Column (main content)
            $row->column(
                $form->text("Service Name")
                    ->setAttribute('value', $service->name ?? "Service #{$service->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // Additional info column (optional)
            $row->column(
                $form->text("SKU")
                    ->setAttribute('value', $service->sku ?? 'B2CNC-' . $service->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // ID Column (smaller width)
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
            ->setAttribute('value', 'No Services are currently associated with this attribute definition')
            ->setAttribute('readonly', true)
            ->setAttribute('name', false);
    }

    if ($attribute_values && count($attribute_values) > 0) {
        foreach ($attribute_values as $attribute_value) {
            $row = $form->row();

            $row->column(
                $form->text("Service Name")
                    ->setAttribute('value', $attribute_value->service->name ?? 'B2CNC-' . $attribute_value->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("Attribute Definition")
                    ->setAttribute('value', $attribute_value->attributedefinition->label ?? "Attribute Value #{$attribute_value->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("Attribute Value")
                    ->setAttribute('value', $attribute_value->value)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $attribute_value_fields[] = $row;
        }
    } else {
        $attribute_value_fields[] = $form->text('No Attribute Values')
            ->setAttribute('value', 'No Attribute Values are currently associated with this deliverable')
            ->setAttribute('readonly', true)
            ->setAttribute('name', false);
    }


    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services using this attribute definition',
        $service_fields
    ));

    $relationshipNestedTabs->tab('Attribute Value', 'admin-post', $form->fieldset(
        'Related Attribute Value',
        'Attribute Value using this attribute definition',
        $attribute_value_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
