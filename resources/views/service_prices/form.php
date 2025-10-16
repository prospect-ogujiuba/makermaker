<?php

/**
 * Service Price Form
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\PricingTier;
use MakerMaker\Models\PricingModel;

// Form instance
echo $form->open();

echo to_resource('ServicePrice', 'index', 'Back To Service Pricing');

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
                        ->setHelp('Select the service this pricing applies to')
                        ->setModelOptions(Service::class, 'name', 'id', 'Select Service')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('pricing_tier_id')
                        ->setLabel('Pricing Tier')
                        ->setHelp('Select the pricing tier (e.g., Basic, Standard, Premium)')
                        ->setModelOptions(PricingTier::class, 'name', 'id', 'Select Pricing Tier')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->select('pricing_model_id')
                        ->setLabel('Pricing Model')
                        ->setHelp('Select how this service is priced (e.g., Fixed, Hourly, Monthly)')
                        ->setModelOptions(PricingModel::class, 'name', 'id', 'Select Pricing Model')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('currency')
                        ->setLabel('Currency')
                        ->setHelp('Currency for this pricing entry')
                        ->setOptions([
                            'Select a Currency' => NULL,
                            'CAD - Canadian Dollar'   => 'CAD',
                            'USD - US Dollar'         => 'USD',
                            'EUR - Euro'              => 'EUR',
                            'GBP - British Pound'     => 'GBP',
                            'AUD - Australian Dollar' => 'AUD',
                            'JPY - Japanese Yen'      => 'JPY',
                            'CHF - Swiss Franc'       => 'CHF',
                            'MXN - Mexican Peso'      => 'MXN',
                        ])
                )
        ]
    ),

    $form->fieldset(
        'Pricing Configuration',
        'Amount, unit, and pricing details',
        [
            $form->row()
                ->withColumn(
                    $form->number('amount')
                        ->setLabel('Amount')
                        ->setHelp('Base price amount (leave empty for quote-only services)')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('min', '0')
                        ->setAttribute('placeholder', '0.00')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('unit')
                        ->setLabel('Pricing Unit')
                        ->setHelp('Unit of measurement for pricing (e.g., "hour", "device", "month")')
                        ->setAttribute('maxlength', '32')
                        ->setAttribute('placeholder', 'e.g., hour, device, project')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->number('setup_fee')
                        ->setLabel('Setup Fee')
                        ->setHelp('One-time initial setup fee')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('min', '0')
                        ->setAttribute('placeholder', 'e.g., hour, device, project')
                )
                ->withColumn()
        ]
    ),

    $form->fieldset(
        'Validity & Approval',
        'Effective dates and approval workflow',
        [
            $form->row()
                ->withColumn(
                    $form->date('valid_from')
                        ->setLabel('Valid From')
                        ->setHelp('Date when this pricing becomes effective')
                        ->setFormat('yy-mm-dd')
                        ->setAttribute('placeholder', 'Format: ' . date('Y-m-d H:i:s'))
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->date('valid_to')
                        ->setLabel('Valid To')
                        ->setHelp('Date when this pricing expires (leave empty for no expiration)')
                        ->setFormat('yy-mm-dd')
                        ->setAttribute('placeholder', 'Format: ' . date('Y-m-d H:i:s'))
                ),
            $form->row()
                ->withColumn(
                    $form->toggle('is_current')
                        ->setLabel('Current Pricing')
                        ->setHelp('Mark as the current active pricing for this service/tier/model combination')
                        ->setAttribute('value', '1')
                )
                ->withColumn(
                    $form->select('approval_status')
                        ->setLabel('Approval Status')
                        ->setHelp('Current approval status of this pricing')
                        ->setOptions([
                            'Select Status' => NULL,
                            'Draft' => 'draft',
                            'Pending Approval' => 'pending',
                            'Approved' => 'approved',
                            'Rejected' => 'rejected'
                        ])
                        ->setDefault('draft')
                        ->markLabelRequired()
                )
        ]
    )

])->setDescription('Service Price Configuration');

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
