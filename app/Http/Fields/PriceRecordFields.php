<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;

class PriceRecordFields extends Fields
{
    /**
     * Run On Import
     *
     * Price Record records are read-only, so validation is not needed
     * for user input, but we still validate data structure for system-generated records
     *
     * @var bool
     */
    protected $run = true;

    /**
     * Model Fillable Property Override
     * 
     * PriceRecord records are audit logs and should not be fillable
     * to maintain data integrity
     *
     * @return array
     */
    protected function fillable()
    {
        return [];
    }

    /**
     * Validation Rules - Complete compliance version
     *
     * These rules ensure data integrity for system-generated records
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'service_price_id' => 'required|numeric',
            'change_type' => 'required',
            'old_amount' => '?numeric|min:0',
            'new_amount' => '?numeric|min:0',
            'old_setup_fee' => '?numeric|min:0',
            'new_setup_fee' => '?numeric|min:0',
            'old_currency' => '?size:3',
            'new_currency' => '?size:3',
            'old_unit' => '?max:32',
            'new_unit' => '?max:32',
            'old_valid_from' => '',
            'new_valid_from' => '',
            'old_valid_to' => '',
            'new_valid_to' => '',
            'old_is_current' => '',
            'new_is_current' => '',
            'old_service_id' => '?numeric',
            'new_service_id' => '?numeric',
            'old_pricing_tier_id' => '?numeric',
            'new_pricing_tier_id' => '?numeric',
            'old_pricing_model_id' => '?numeric',
            'new_pricing_model_id' => '?numeric',
            'old_approval_status' => '',
            'new_approval_status' => '',
            'old_approved_by' => '?numeric|min:1',
            'new_approved_by' => '?numeric|min:1',
            'old_approved_at' => '',
            'new_approved_at' => '',
            'change_description' => '?max:512'
        ];
    }

    /**
     * Custom Error Messages
     *
     * @return array
     */
    protected function messages()
    {
        return [
            'service_price_id.required' => 'Service Price ID is required for Price Record records.',
            'service_price_id.numeric' => 'Service Price ID must be a valid number.',
            'change_type.required' => 'Change type is required for Price Record records.',
            'old_amount.numeric' => 'Old amount must be a valid number.',
            'old_amount.min' => 'Old amount cannot be negative.',
            'new_amount.numeric' => 'New amount must be a valid number.',
            'new_amount.min' => 'New amount cannot be negative.',
            'old_setup_fee.numeric' => 'Old setup fee must be a valid number.',
            'old_setup_fee.min' => 'Old setup fee cannot be negative.',
            'new_setup_fee.numeric' => 'New setup fee must be a valid number.',
            'new_setup_fee.min' => 'New setup fee cannot be negative.',
            'old_currency.size' => 'Old currency must be exactly 3 characters.',
            'new_currency.size' => 'New currency must be exactly 3 characters.',
            'old_unit.max' => 'Old unit cannot exceed 32 characters.',
            'new_unit.max' => 'New unit cannot exceed 32 characters.',
            'old_valid_from.date' => 'Old valid from must be a valid date.',
            'new_valid_from.date' => 'New valid from must be a valid date.',
            'old_valid_to.date' => 'Old valid to must be a valid date.',
            'new_valid_to.date' => 'New valid to must be a valid date.',
            'old_service_id.numeric' => 'Old service ID must be a valid number.',
            'new_service_id.numeric' => 'New service ID must be a valid number.',
            'old_pricing_tier_id.numeric' => 'Old pricing tier ID must be a valid number.',
            'new_pricing_tier_id.numeric' => 'New pricing tier ID must be a valid number.',
            'old_pricing_model_id.numeric' => 'Old pricing model ID must be a valid number.',
            'new_pricing_model_id.numeric' => 'New pricing model ID must be a valid number.',
            'old_approval_status.in' => 'Old approval status must be one of: draft, pending, approved, rejected.',
            'new_approval_status.in' => 'New approval status must be one of: draft, pending, approved, rejected.',
            'old_approved_by.numeric' => 'Old approved by must be a valid user ID.',
            'old_approved_by.min' => 'Old approved by must be a valid user ID.',
            'new_approved_by.numeric' => 'New approved by must be a valid user ID.',
            'new_approved_by.min' => 'New approved by must be a valid user ID.',
            'old_approved_at.date' => 'Old approved at must be a valid date.',
            'new_approved_at.date' => 'New approved at must be a valid date.',
            'change_description.max' => 'Change reason cannot exceed 512 characters.',
            'changed_at.required' => 'Changed at timestamp is required.',
            'changed_at.date' => 'Changed at must be a valid date.',
            'changed_by.required' => 'Changed by user ID is required.',
            'changed_by.numeric' => 'Changed by must be a valid user ID.',
            'changed_by.min' => 'Changed by must be a valid user ID.',
        ];
    }
}
