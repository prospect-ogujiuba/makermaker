<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;

class ClientFields extends Fields
{
    /**
     * Run On Import
     *
     * Validate and then redirect on failure with errors, immediately
     * when imported by the application container resolver.
     *
     * @var bool
     */
    protected $run = true;

    /**
     * Model Fillable Property Override
     *
     * @return array
     */
    protected function fillable()
    {
        return [];
    }

    /**
     * Validation Rules
     *
     * @return array
     */
    protected function rules()
    {
        return [
            // Company Information
            'company_name' => 'required|max:255',
            'legal_name' => 'max:255',
            'business_number' => 'max:50',

            // Contact Information
            'contact_firstname' => 'required|max:100',
            'contact_lastname' => 'required|max:100',
            'contact_title' => 'max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:20',
            'mobile' => 'max:20',
            'website' => 'url|max:255',

            // Physical Address
            'street' => 'required|max:255',
            'city' => 'required|max:100',
            'province' => 'required',
            'postal_code' => 'required|max:7',
            'country' => 'max:3',

            // Business Details
            'industry' => 'max:100',
            'company_size' => '',
            'annual_revenue' => '',

            // Billing Address (optional)
            'billing_street' => 'max:255',
            'billing_city' => 'max:100',
            'billing_province' => '',
            'billing_postal_code' => 'max:7',
            'billing_country' => 'max:3',

            // Financial Information
            'tax_number' => 'max:50',
            'payment_terms' => '',
            'credit_limit' => 'numeric',

            // Account Management
            'notes' => '',
            'status' => 'required',
            'priority' => 'required',
            'source' => '',
            'assigned_to' => 'numeric',

            // Dates - these should be handled by the form fields, not validation
            'onboarded_at' => '',
            'last_contact_at' => ''
        ];
    }

    /**
     * Custom Error Messages
     *
     * @return array
     */
    protected function messages()
    {
        return [];
    }
}
