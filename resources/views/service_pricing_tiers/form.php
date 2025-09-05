<?php

/**
 * ServicePricingTier Form - Complete Data Management
 */

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('OVERVIEW', 'admin-settings', [
    $form->fieldset(
        'Pricing Tier Overview',
        'Define the pricing tier characteristics and sorting order',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setHelp('Descriptive name for this pricing tier (e.g., "Small Business", "Enterprise", "Government")')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., Enterprise')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('code')
                        ->setLabel('Code')
                        ->setHelp('Unique code for this pricing tier (e.g., "SMB", "ENT", "GOV")')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., ENT')
                        ->markLabelRequired()
                ),

            $form->row()
                ->withColumn(
                    $form->number('sort_order')
                        ->setLabel('Sort Order')
                        ->setHelp('Numeric order for displaying tiers (0 = first, higher numbers = later in list)')
                        ->setAttribute('min', '0')
                        ->setAttribute('step', '1')
                        ->setDefault(0)
                )
        ]
    )
])->setDescription('Pricing Tier Information');

// Show relationship info if editing
if (isset($current_id) && $current_id) {
    $relationshipNestedTabs = tr_tabs()->layoutTop();

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

    // Service Prices Tab
    $service_price_fields = [];
    if (isset($servicePrices) && !empty($servicePrices)) {
        foreach ($servicePrices as $servicePrice) {
            $row = $form->row();

            // Service Name column
            $row->column(
                $form->text("Service")
                    ->setAttribute('value', $servicePrice->service->name ?? "Service #{$servicePrice->service_id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)
            );

            // Pricing Model column
            $row->column(
                $form->text("Pricing Model")
                    ->setAttribute('value', $servicePrice->pricingModel->name ?? "Model #{$servicePrice->pricing_model_id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)
            );

            // Amount column
            $row->column(
                $form->text("Amount")
                    ->setAttribute('value', $servicePrice->currency . ' ' . number_format($servicePrice->amount, 2))
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)
            );


            $service_price_fields[] = $row;
        }
    } else {
        $service_price_fields[] = $form->text('No Prices')
            ->setAttribute('value', 'No prices are currently associated with this pricing tier')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Prices', 'admin-post', $form->fieldset(
        'Related Prices',
        'Prices using this pricing tier',
        $service_price_fields
    ));

    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Relationship information');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
