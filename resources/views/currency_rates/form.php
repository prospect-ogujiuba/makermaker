<?php

/**
 * CurrencyRate Form
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
        'Currency Exchange Rate',
        'Define currency exchange rate configuration',
        [
            $form->row()
                ->withColumn(
                    $form->select('from_currency')
                        ->setLabel('From Currency')
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

                        ->setHelp('Source currency for conversion (3-letter ISO code)')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('to_currency')
                        ->setLabel('To Currency')
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

                        ->setHelp('Target currency for conversion (3-letter ISO code)')
                        ->markLabelRequired()
                ),

            $form->row()
                ->withColumn(
                    $form->number('exchange_rate')
                        ->setLabel('Exchange Rate')
                        ->setAttribute('min', '0.000001')
                        ->setAttribute('step', '0.000001')
                        ->setAttribute('placeholder', '1.351351')
                        ->setHelp('Exchange rate value (up to 6 decimal places)')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->date('effective_date')
                        ->setLabel('Effective Date')
                        ->setHelp('Date when this exchange rate becomes effective')
                        ->setAttribute('placeholder', 'e.g., ' . date('Y-m-d'))
                        ->markLabelRequired()
                ),

            $form->row()
                ->withColumn(
                    $form->select('source')
                        ->setLabel('Rate Source')
                        ->setOptions([
                            'Select Source'          => NULL,
                            'Manual Entry'           => 'manual',
                            'Bank of Canada'         => 'bank_of_canada',
                            'Federal Reserve'        => 'federal_reserve',
                            'European Central Bank'  => 'ecb',
                            'Bank of England'        => 'bank_of_england',
                            'XE.com API'             => 'xe_api',
                            'Fixer.io API'           => 'fixer_api',
                            'Open Exchange Rates'    => 'openexchange_api',
                        ])
                        ->setHelp('Source of the exchange rate data')
                        ->setAttribute('value', 'manual')
                )
                ->withColumn()
        ]
    )

])->setDescription('Currency Rates');

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
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
