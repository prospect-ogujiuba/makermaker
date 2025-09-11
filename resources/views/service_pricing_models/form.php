<?php

/**
 * ServicePricingModel Form
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
        'Pricing Model Overview',
        'Define the pricing model characteristics and pricing impact',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setHelp('Descriptive name for this pricing model (e.g., "Tiered", "Hourly", "Monthly Subscription", "Per Unit")')
                        ->setAttribute('maxlength', '100')
                        ->setAttribute('placeholder', 'e.g., Advanced Implementation')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('code')
                        ->setLabel('Pricing Model Code')
                        ->setHelp('i.e. Annual Contract = ANNUAL')
                        ->setAttribute('min', '1')
                        ->setAttribute('step', '1')
                        ->markLabelRequired()
                )
        ]
    )

])->setDescription('Pricing Model');

// Conditional
if (isset($current_id)) {
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

    ])->setDescription('System Information');

    $relationshipNestedTabs = \TypeRocket\Elements\Tabs::new()
        ->layoutTop();

    if ($servicePrices && count($servicePrices) > 0) {
        foreach ($servicePrices as $servicePrice) {
            $row = $form->row();

            $row->column(
                $form->text("Pricing Tier")
                    ->setAttribute('value', $servicePrice->pricingTier->name ?? 'B2CNC-' . $servicePrice->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("Service Name")
                    ->setAttribute('value', $servicePrice->service->name ?? "Service #{$servicePrice->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("Pricing Currency")
                    ->setAttribute('value', $servicePrice->amount)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $row->column(
                $form->text("Pricing Currency")
                    ->setAttribute('value', $servicePrice->currency)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $service_price_fields[] = $row;
        }
    } else {
        $service_price_fields[] = $form->text('No Prices')
            ->setAttribute('value', 'No prices are currently associated with this pricing model')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Prices', 'admin-post', $form->fieldset(
        'Related Prices',
        'Prices using this pricing model',
        $service_price_fields
    ));

    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
