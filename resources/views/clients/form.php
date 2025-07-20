<?php

/** @var \TypeRocket\Elements\Form $form */

use MakerMaker\Models\Client;
use App\Models\User;

$date = date('Y-m-d');

// Get the client and related data
$client = Client::new()->find($form->getModel()->id);

echo $form->save($button)->setFields([
    // Show Associations Information Toggle
    $form->fieldset(
        'Client Information Display',
        'Toggle different information sections',
        [
            $form->row()
                ->withColumn($form->toggle('Account Details')->setName('show_account'))
                ->withColumn($form->toggle('History')->setName('show_history'))
                ->withColumn($form->toggle('System Info')->setName('show_system_info')),

            // Account Information Display
            $form->fieldset(
                'Account Information',
                'Account status and management details',
                [
                    $form->row()
                        ->withColumn(
                            $form->text('Client Status')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->status ? 
                                    ucfirst($client->status) : 'New')
                        )
                        ->withColumn(
                            $form->text('Priority Level')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->priority ? 
                                    ucfirst($client->priority) : 'Normal')
                        ),
                    $form->row()
                        ->withColumn(
                            $form->text('Days Since Onboarding')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->onboarded_at ? 
                                    floor((time() - strtotime($client->onboarded_at)) / (60 * 60 * 24)) . ' days' : 
                                    'Not onboarded')
                        )
                        ->withColumn(
                            $form->text('Days Since Last Contact')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->last_contact_at ? 
                                    floor((time() - strtotime($client->last_contact_at)) / (60 * 60 * 24)) . ' days' : 
                                    'New client')
                        ),
                    $form->row()
                        ->withColumn(
                            $form->text('Account Manager')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->assigned_to ? 
                                    User::new()->find($client->assigned_to)->display_name ?? 'Unknown User' : 
                                    'Unassigned')
                        )
                        ->withColumn(
                            $form->text('Lead Source')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->source ? 
                                    ucwords(str_replace('-', ' ', $client->source)) : 'Unknown')
                        )
                ]
            )->when('show_account', '=', true),

            // History Information
            $form->fieldset(
                'History Information',
                'Important dates and timeline',
                [
                    $form->row()
                        ->withColumn(
                            $form->text('Created Date')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->created_at ? 
                                    date('M d, Y', strtotime($client->created_at)) : 'New')
                        )
                        ->withColumn(
                            $form->text('Last Updated')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->updated_at ? 
                                    date('M d, Y g:i A', strtotime($client->updated_at)) : 'New')
                        ),
                    $form->row()
                        ->withColumn(
                            $form->text('Onboarded Date')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->onboarded_at ? 
                                    date('M d, Y', strtotime($client->onboarded_at)) : 'Not onboarded')
                        )
                        ->withColumn(
                            $form->text('Last Contact Date')
                                ->setAttribute('readonly', 'readonly')
                                ->setAttribute('value', $client && $client->last_contact_at ? 
                                    date('M d, Y g:i A', strtotime($client->last_contact_at)) : 'No contact recorded')
                        )
                ]
            )->when('show_history', '=', true),

            // System Info
            $form->fieldset(
                'System Information',
                'System generated client information',
                [
                    $form->row()
                        ->withColumn(
                            $form->text('ID')->setName('id')
                                ->setAttribute('readonly', 'readonly')
                                ->setHelp('System-generated Client Record ID')
                        ),
                    $form->row()
                        ->withColumn(
                            $form->text('Created At')->setName('created_at')
                                ->setAttribute('readonly', 'readonly')
                                ->setHelp('Record creation timestamp')
                        )->withColumn(
                            $form->text('Updated At')->setName('updated_at')
                                ->setAttribute('readonly', 'readonly')
                                ->setHelp('Last update timestamp')
                        )->withColumn(
                            $form->text('Deleted At')->setName('deleted_at')
                                ->setAttribute('readonly', 'readonly')
                                ->setHelp('Soft delete timestamp')
                        )
                ]
            )->when('show_system_info', '=', true)
        ]
    )->when($form->getModel()->id, '!=', '0'),

    // Company Information
    $form->fieldset('Company Information', 'Basic company details and registration information', [
        $form->row()
            ->withColumn(
                $form->text('Company Name')->setName('company_name')
                    ->markLabelRequired()
                    ->setHelp('Legal or operating business name')
                    ->setAttribute('maxlength', '255')
            )
            ->withColumn(
                $form->text('Legal Name')->setName('legal_name')
                    ->setHelp('Full legal business name if different from company name')
                    ->setAttribute('maxlength', '255')
            ),
        $form->row()
            ->withColumn(
                $form->text('Business Number')->setName('business_number')
                    ->setHelp('Business registration number or tax ID')
                    ->setAttribute('maxlength', '50')
            )
            ->withColumn(
                $form->text('Website')->setName('website')
                    ->setAttribute('type', 'url')
                    ->setHelp('Company website URL')
                    ->setAttribute('maxlength', '255')
            )
    ]),

    // Contact Information
    $form->fieldset('Primary Contact', 'Main point of contact for this client', [
        $form->row()
            ->withColumn(
                $form->text('First Name')->setName('contact_firstname')
                    ->markLabelRequired()
                    ->setHelp('Contact person first name')
                    ->setAttribute('maxlength', '100')
            )
            ->withColumn(
                $form->text('Last Name')->setName('contact_lastname')
                    ->markLabelRequired()
                    ->setHelp('Contact person last name')
                    ->setAttribute('maxlength', '100')
            ),
        $form->row()
            ->withColumn(
                $form->text('Title')->setName('contact_title')
                    ->setHelp('Job title or position')
                    ->setAttribute('maxlength', '100')
            ),
        $form->row()
            ->withColumn(
                $form->text('Email')->setName('email')
                    ->markLabelRequired()
                    ->setAttribute('type', 'email')
                    ->setHelp('Primary email address')
                    ->setAttribute('maxlength', '255')
            )
            ->withColumn(
                $form->text('Phone')->setName('phone')
                    ->markLabelRequired()
                    ->setHelp('10-digit phone number')
                    ->setAttribute('maxlength', '20')
                    ->setAttribute('pattern', '[0-9]{10}')
            ),
        $form->row()
            ->withColumn(
                $form->text('Mobile')->setName('mobile')
                    ->setHelp('Mobile phone number')
                    ->setAttribute('maxlength', '20')
                    ->setAttribute('pattern', '[0-9]{10}')
            )
    ]),

    // Physical Address
    $form->fieldset('Physical Address', 'Primary business location', [
        $form->text('Street Address')->setName('street')
            ->markLabelRequired()
            ->setHelp('Full street address')
            ->setAttribute('maxlength', '255'),
        $form->row()
            ->withColumn(
                $form->text('City')->setName('city')
                    ->markLabelRequired()
                    ->setHelp('City name')
                    ->setAttribute('maxlength', '100')
            )
            ->withColumn(
                $form->select('Province')->setName('province')
                    ->setOptions([
                        'Ontario' => 'ON',
                        'British Columbia' => 'BC',
                        'Alberta' => 'AB',
                        'Saskatchewan' => 'SK',
                        'Manitoba' => 'MB',
                        'Quebec' => 'QC',
                        'New Brunswick' => 'NB',
                        'Nova Scotia' => 'NS',
                        'Prince Edward Island' => 'PE',
                        'Newfoundland and Labrador' => 'NL',
                        'Yukon' => 'YT',
                        'Northwest Territories' => 'NT',
                        'Nunavut' => 'NU'
                    ])
                    ->markLabelRequired()
                    ->setDefault('ON')
            )
            ->withColumn(
                $form->text('Postal Code')->setName('postal_code')
                    ->markLabelRequired()
                    ->setHelp('Format: A1A 1A1')
                    ->setAttribute('maxlength', '7')
                    ->setAttribute('pattern', '[A-Z][0-9][A-Z] [0-9][A-Z][0-9]')
            )
    ]),

    // Billing Address
    $form->fieldset('Billing Address', 'If different from physical address', [
        $form->text('Billing Street')->setName('billing_street')
            ->setHelp('Leave blank to use physical address')
            ->setAttribute('maxlength', '255'),
        $form->row()
            ->withColumn(
                $form->text('Billing City')->setName('billing_city')
                    ->setAttribute('maxlength', '100')
            )
            ->withColumn(
                $form->select('Billing Province')->setName('billing_province')
                    ->setOptions([
                        'Select Province' => '',
                        'Ontario' => 'ON',
                        'British Columbia' => 'BC',
                        'Alberta' => 'AB',
                        'Saskatchewan' => 'SK',
                        'Manitoba' => 'MB',
                        'Quebec' => 'QC',
                        'New Brunswick' => 'NB',
                        'Nova Scotia' => 'NS',
                        'Prince Edward Island' => 'PE',
                        'Newfoundland and Labrador' => 'NL',
                        'Yukon' => 'YT',
                        'Northwest Territories' => 'NT',
                        'Nunavut' => 'NU'
                    ])
            )
            ->withColumn(
                $form->text('Billing Postal Code')->setName('billing_postal_code')
                    ->setAttribute('maxlength', '7')
                    ->setAttribute('pattern', '[A-Z][0-9][A-Z] [0-9][A-Z][0-9]')
            )
    ]),

    // Business Details
    $form->fieldset('Business Details', 'Company size and industry information', [
        $form->row()
            ->withColumn(
                $form->text('Industry')->setName('industry')
                    ->setHelp('Primary business industry')
                    ->setAttribute('maxlength', '100')
            ),
        $form->row()
            ->withColumn(
                $form->select('Company Size')->setName('company_size')
                    ->setOptions([
                        'Select Size' => '',
                        '1-10 employees' => '1-10',
                        '11-50 employees' => '11-50',
                        '51-200 employees' => '51-200',
                        '201-500 employees' => '201-500',
                        '500+ employees' => '500+'
                    ])
            )
            ->withColumn(
                $form->select('Annual Revenue')->setName('annual_revenue')
                    ->setOptions([
                        'Select Revenue Range' => '',
                        'Under $1M' => 'under-1m',
                        '$1M - $5M' => '1m-5m',
                        '$5M - $25M' => '5m-25m',
                        '$25M - $100M' => '25m-100m',
                        'Over $100M' => 'over-100m'
                    ])
            )
    ]),

    // Financial Information
    $form->fieldset('Financial Information', 'Payment terms and credit details', [
        $form->row()
            ->withColumn(
                $form->text('Tax Number')->setName('tax_number')
                    ->setHelp('HST/GST number or tax ID')
                    ->setAttribute('maxlength', '50')
            ),
        $form->row()
            ->withColumn(
                $form->select('Payment Terms')->setName('payment_terms')
                    ->setOptions([
                        'Net 15 days' => 'net-15',
                        'Net 30 days' => 'net-30',
                        'Net 60 days' => 'net-60',
                        'Due on receipt' => 'due-on-receipt'
                    ])
                    ->setDefault('net-30')
            )
            ->withColumn(
                $form->number('Credit Limit')->setName('credit_limit')
                    ->setHelp('Maximum credit amount in CAD')
                    ->setAttribute('step', '0.01')
                    ->setAttribute('min', '0')
            )
    ]),

    // Account Management
    $form->fieldset('Account Management', 'Status, priority and assignment', [
        $form->row()
            ->withColumn(
                $form->select('Status')->setName('status')
                    ->setOptions([
                        'Prospect' => 'prospect',
                        'Active' => 'active',
                        'Inactive' => 'inactive',
                        'Suspended' => 'suspended'
                    ])
                    ->markLabelRequired()
                    ->setDefault('prospect')
            )
            ->withColumn(
                $form->select('Priority')->setName('priority')
                    ->setOptions([
                        'Low' => 'low',
                        'Normal' => 'normal',
                        'High' => 'high',
                        'Critical' => 'critical'
                    ])
                    ->markLabelRequired()
                    ->setDefault('normal')
            ),
        $form->row()
            ->withColumn(
                $form->select('Lead Source')->setName('source')
                    ->setOptions([
                        'Select Source' => '',
                        'Website' => 'website',
                        'Referral' => 'referral',
                        'Cold Call' => 'cold-call',
                        'Trade Show' => 'trade-show',
                        'Social Media' => 'social-media',
                        'Other' => 'other'
                    ])
            )
            ->withColumn(
                $form->select('Assigned To')->setName('assigned_to')
                    ->setOptions(['Unassigned' => ''])
                    ->setModelOptions(User::class, 'display_name', 'ID')
                    ->setHelp('Account manager')
            )
    ]),

    // Notes and Dates
    $form->fieldset('Additional Information', 'Notes and important dates', [
        $form->textarea('Notes')->setName('notes')
            ->setHelp('Internal notes and comments')
            ->setAttribute('rows', '4'),
        $form->row()
            ->withColumn(
                $form->date('Onboarded At')->setName('onboarded_at')
                    ->setHelp('When client was fully onboarded')
            )
            ->withColumn(
                $form->date('Last Contact At')->setName('last_contact_at')
                    ->setHelp('Last communication date')
            )
    ])
]);