<?php

/**
 * CoverageArea Form
 */

// Form instance
echo $form->open();

echo to_resource('CoverageArea', 'index', 'Back To Coverage Area');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
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
                        ->setHelp('Display name for this coverage area')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., Local Area')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('code')
                        ->setLabel('Coverage Area Code')
                        ->setHelp('Computer friendly code/slug')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'Auto-generated from name if left empty')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->text('country_code')
                        ->setLabel('Country Code')
                        ->setHelp('Two-letter ISO country code (e.g., US, CA, GB)')
                        ->setAttribute('maxlength', '2')
                        ->setAttribute('placeholder', 'e.g., US')
                        ->setAttribute('style', 'text-transform: uppercase;')
                )
                ->withColumn(
                    $form->select('region_type')
                        ->setLabel('Region Type')
                        ->setHelp('Type of geographic region')
                        ->setOptions([
                            'Select Region Type' => NULL,
                            'City' => 'city',
                            'Province' => 'province',
                            'State' => 'state',
                            'Country' => 'country',
                            'Continent' => 'continent',
                            'Global' => 'global'
                        ])
                ),
            $form->row()
                ->withColumn(
                    $form->text('timezone')
                        ->setLabel('Timezone')
                        ->setHelp('IANA timezone identifier (e.g., America/New_York)')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., America/New_York')
                )
                ->withColumn(
                    $form->text('postal_code_pattern')
                        ->setLabel('Postal Code Pattern')
                        ->setHelp('Regex pattern for validating postal codes in this area')
                        ->setAttribute('maxlength', '32')
                        ->setAttribute('placeholder', 'e.g., ^\d{5}(-\d{4})?$')
                )
        ]
    )

])->setDescription('Coverage Area');

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


            // Additional info column (optional)
            $row->column(
                $form->text("Service Name")
                    ->setAttribute('value', $serviceCoverage->service->name ?? 'B2CNC-' . $serviceCoverage->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // ID Column (smaller width)
            $row->column(
                $form->text("SKU")
                    ->setAttribute('value', $serviceCoverage->service->sku)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // Name Column (main content)
            $row->column(
                $form->text("Service ID")
                    ->setAttribute('value', $serviceCoverage->id ?? "Service #{$serviceCoverage->id}")
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
        'Services covering area area',
        $service_coverages_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
