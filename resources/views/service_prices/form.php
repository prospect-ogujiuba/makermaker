<?php

/**
 * Enhanced Service Price Form
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\ServicePricingTier;
use MakerMaker\Models\ServicePricingModel;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Price Information',
        'Core pricing details and configuration',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Service')
                        ->setHelp('The service this price applies to')
                        ->setOptions(['Select Service' => null])
                        ->setModelOptions(Service::class, 'name', 'id')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('pricing_tier_id')
                        ->setLabel('Pricing Tier')
                        ->setHelp('The pricing tier for this service price')
                        ->setOptions(['Select Pricing Tier' => null])
                        ->setModelOptions(ServicePricingTier::class, 'name', 'id')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->select('pricing_model_id')
                        ->setLabel('Pricing Model')
                        ->setHelp('The pricing model for this service price')
                        ->setOptions(['Select Pricing Model' => null])
                        ->setModelOptions(ServicePricingModel::class, 'name', 'id')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('currency')
                        ->setLabel('Currency')
                        ->setHelp('Currency for this price')
                        ->setOptions([
                            'CAD' => 'CAD - Canadian Dollar',
                            'USD' => 'USD - US Dollar',
                            'EUR' => 'EUR - Euro',
                            'GBP' => 'GBP - British Pound'
                        ])
                        ->setDefault('CAD')
                        ->markLabelRequired()
                )
        ]
    ),

    $form->fieldset(
        'Pricing Configuration',
        'Amount, unit, and quantity settings',
        [
            $form->row()
                ->withColumn(
                    $form->number('amount')
                        ->setLabel('Amount')
                        ->setHelp('Price amount (leave empty for quote-based pricing)')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('min', '0')
                        ->setAttribute('placeholder', '0.00')
                )
                ->withColumn(
                    $form->text('unit')
                        ->setLabel('Pricing Unit')
                        ->setHelp('Unit for pricing calculation')
                ),
            $form->row()
                ->withColumn(
                    $form->number('setup_fee')
                        ->setLabel('Setup Fee')
                        ->setHelp('One-time setup fee for this service')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('min', '0')
                        ->setDefault('0.00')
                        ->markLabelRequired()
                )
                ->withColumn()
        ]
    ),

    $form->fieldset(
        'Effective Period',
        'When this pricing is active',
        [
            $form->row()
                ->withColumn(
                    $form->date('effective_from')
                        ->setLabel('Effective From')
                        ->setHelp('Date when this pricing becomes effective')
                        ->setDefault(date('Y-m-d'))
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->date('effective_to')
                        ->setLabel('Effective To')
                        ->setHelp('Date when this pricing expires (leave empty for indefinite)')
                )
        ]
    ),

    $form->fieldset(
        'Price Notes',
        'Informational price details and properties',
        [
            $form->row()
                ->withColumn(
                    $form->repeater('notes')
                        ->setLabel('Service Notes')
                        ->setHelp('Additional service price info')
                        ->setFields($form->row()
                            ->withColumn(
                                $form->text('Note')->setName('note')->setHelp('Note to save to database')
                            ))
                )
        ]
    )

])->setDescription('Service Price Details');

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
                            ->setLabel('Service Price ID')
                            ->setHelp('System generated unique identifier')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    )
                    ->withColumn(
                        $form->text('version')
                            ->setLabel('Version')
                            ->setHelp('Optimistic locking version number')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    ),
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
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
