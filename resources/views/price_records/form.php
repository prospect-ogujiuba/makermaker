<?php

/**
 * Price Record Form (Read-Only View) - Complete Compliance Version
 */

use MakerMaker\Models\ServicePrice;
use MakerMaker\Models\Service;
use MakerMaker\Models\PricingTier;
use MakerMaker\Models\PricingModel;

// Form instance
echo $form->open();

echo to_resource('PriceRecord', 'index', 'Back To Price Record');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter('')
    ->layoutLeft();

// Main Tab - Change Overview
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Change Information',
        'Details about this Price Record record',
        [
            $form->row()
                ->withColumn(
                    $form->text('service_price_id')
                        ->setLabel('Service Price ID')
                        ->setHelp('The service price record that was changed')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('change_type')
                        ->setLabel('Change Type')
                        ->setHelp('Type of change that occurred')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                ),
            $form->row()
                ->withColumn(
                    $form->textarea('change_description')
                        ->setLabel('Change Reason')
                        ->setHelp('Explanation for why this change was made')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                        ->setAttribute('rows', '4')
                )
        ]
    ),
])->setDescription('Change Overview');

// Financial Changes Tab
$tabs->tab('Financial', 'money', [
    $form->fieldset(
        'Amount Changes',
        'Before and after pricing amounts',
        [
            $form->row()
                ->withColumn(
                    $form->number('old_amount')
                        ->setLabel('Old Amount')
                        ->setHelp('Previous price amount')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                        ->setAttribute('step', '0.01')
                )
                ->withColumn(
                    $form->number('new_amount')
                        ->setLabel('New Amount')
                        ->setHelp('Updated price amount')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                        ->setAttribute('step', '0.01')
                ),
            $form->row()
                ->withColumn(
                    $form->number('old_setup_fee')
                        ->setLabel('Old Setup Fee')
                        ->setHelp('Previous setup fee amount')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                        ->setAttribute('step', '0.01')
                )
                ->withColumn(
                    $form->number('new_setup_fee')
                        ->setLabel('New Setup Fee')
                        ->setHelp('Updated setup fee amount')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                        ->setAttribute('step', '0.01')
                )
        ]
    ),

    $form->fieldset(
        'Currency & Unit Changes',
        'Before and after currency and pricing unit',
        [
            $form->row()
                ->withColumn(
                    $form->text('old_currency')
                        ->setLabel('Old Currency')
                        ->setHelp('Previous currency code')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_currency')
                        ->setLabel('New Currency')
                        ->setHelp('Updated currency code')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                ),
            $form->row()
                ->withColumn(
                    $form->text('old_unit')
                        ->setLabel('Old Unit')
                        ->setHelp('Previous pricing unit')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_unit')
                        ->setLabel('New Unit')
                        ->setHelp('Updated pricing unit')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
        ]
    )
])->setDescription('Financial Changes');

// Temporal Changes Tab
$tabs->tab('Dates', 'calendar', [
    $form->fieldset(
        'Validity Period Changes',
        'Before and after effective dates',
        [
            $form->row()
                ->withColumn(
                    $form->text('old_valid_from')
                        ->setLabel('Old Valid From')
                        ->setHelp('Previous effective start date')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_valid_from')
                        ->setLabel('New Valid From')
                        ->setHelp('Updated effective start date')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                ),
            $form->row()
                ->withColumn(
                    $form->text('old_valid_to')
                        ->setLabel('Old Valid To')
                        ->setHelp('Previous expiration date')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_valid_to')
                        ->setLabel('New Valid To')
                        ->setHelp('Updated expiration date')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
        ]
    ),

    $form->fieldset(
        'Current Status Changes',
        'Before and after current/active status',
        [
            $form->row()
                ->withColumn(
                    $form->text('old_is_current')
                        ->setLabel('Old Is Current')
                        ->setHelp('Previous current status (1=Yes, 0=No)')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_is_current')
                        ->setLabel('New Is Current')
                        ->setHelp('Updated current status (1=Yes, 0=No)')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
        ]
    )
])->setDescription('Temporal Changes');

// Relationship Changes Tab
$tabs->tab('Relationships', 'networking', [
    $form->fieldset(
        'Service Changes',
        'Before and after service association',
        [
            $form->row()
                ->withColumn(
                    $form->text('old_service_id')
                        ->setLabel('Old Service ID')
                        ->setHelp('Previous service association')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_service_id')
                        ->setLabel('New Service ID')
                        ->setHelp('Updated service association')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
        ]
    ),

    $form->fieldset(
        'Pricing Tier Changes',
        'Before and after pricing tier',
        [
            $form->row()
                ->withColumn(
                    $form->text('old_pricing_tier_id')
                        ->setLabel('Old Pricing Tier ID')
                        ->setHelp('Previous pricing tier')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_pricing_tier_id')
                        ->setLabel('New Pricing Tier ID')
                        ->setHelp('Updated pricing tier')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
        ]
    ),

    $form->fieldset(
        'Pricing Model Changes',
        'Before and after pricing model',
        [
            $form->row()
                ->withColumn(
                    $form->text('old_pricing_model_id')
                        ->setLabel('Old Pricing Model ID')
                        ->setHelp('Previous pricing model')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_pricing_model_id')
                        ->setLabel('New Pricing Model ID')
                        ->setHelp('Updated pricing model')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
        ]
    )
])->setDescription('Relationship Changes');

// Approval Workflow Tab
$tabs->tab('Approval', 'yes', [
    $form->fieldset(
        'Approval Status Changes',
        'Before and after approval status',
        [
            $form->row()
                ->withColumn(
                    $form->text('old_approval_status')
                        ->setLabel('Old Approval Status')
                        ->setHelp('Previous approval status')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_approval_status')
                        ->setLabel('New Approval Status')
                        ->setHelp('Updated approval status')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
        ]
    ),

    $form->fieldset(
        'Approver Changes',
        'Before and after approver assignment',
        [
            $form->row()
                ->withColumn(
                    $form->text('old_approved_by_user')
                        ->setLabel('Old Approved By')
                        ->setHelp('Previous approver')
                        ->setAttribute('value', $oldApprovedBy->display_name ?? 'N/A')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_approved_by_user')
                        ->setLabel('New Approved By')
                        ->setHelp('Updated approver')
                        ->setAttribute('value', $newApprovedBy->display_name ?? 'N/A')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                ),
            $form->row()
                ->withColumn(
                    $form->text('old_approved_at')
                        ->setLabel('Old Approved At')
                        ->setHelp('Previous approval timestamp')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('new_approved_at')
                        ->setLabel('New Approved At')
                        ->setHelp('Updated approval timestamp')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
        ]
    )
])->setDescription('Approval Workflow');

// System Info Tab
$tabs->tab('System', 'info', [
    $form->fieldset(
        'System Info',
        'Core system metadata fields',
        [
            $form->row()
                ->withColumn(
                    $form->text('id')
                        ->setLabel('Price Record ID')
                        ->setHelp('System generated unique identifier')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn(
                    $form->text('changed_at')
                        ->setLabel('Changed At')
                        ->setHelp('When this change occurred')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                ),
            $form->row()
                ->withColumn(
                    $form->text('changed_by_user')
                        ->setLabel('Changed By')
                        ->setHelp('User who made this change')
                        ->setAttribute('value', $changedBy->display_name ?? 'System')
                        ->setAttribute('readonly', true)
                        ->setAttribute('name', false)
                )
                ->withColumn()
        ]
    )
])->setDescription('System Information');

$tabs->render();

echo $form->close();
