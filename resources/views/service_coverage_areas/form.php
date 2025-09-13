<?php

/**
 * ServiceCoverageArea Form
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
        'Coverage Area',
        'Define the coverage area characteristics and code',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setHelp('Descriptive name for this coverage area (e.g., "Tiered", "Hourly", "Monthly Subscription", "Per Unit")')
                        ->setAttribute('maxlength', '100')
                        ->setAttribute('placeholder', 'e.g., Advanced Implementation')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('code')
                        ->setLabel('Coverage Code')
                        ->setHelp('i.e. Greater Toronto Area = GTA')
                        ->setAttribute('min', '1')
                        ->setAttribute('step', '1')
                        ->markLabelRequired()
                )
        ]
    )

])->setDescription('Coverage area information');

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

    $service_coverages_fields = [];

    if ($serviceCoverages && count($serviceCoverages) > 0) {
        foreach ($serviceCoverages as $serviceCoverage) {
            $row = $form->row();

            // Name Column (main content)
            $row->column(
                $form->text("Service ID")
                    ->setAttribute('value', $serviceCoverage->id ?? "Service #{$serviceCoverage->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // Additional info column (optional)
            $row->column(
                $form->text("Service Name")
                    ->setAttribute('value', $serviceCoverage->name ?? 'B2CNC-' . $serviceCoverage->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // ID Column (smaller width)
            $row->column(
                $form->text("SKU")
                    ->setAttribute('value', $serviceCoverage->sku)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $service_coverages_fields[] = $row;
        }
    } else {
        $service_coverages_fields[] = $form->text('No Services')
            ->setAttribute('value', 'No services are currently associated with this coverage area')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services using this coverage area',
        $service_coverages_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
