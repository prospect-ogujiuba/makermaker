<?php
namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;

class ServiceAddonsFields extends Fields
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
    protected function fillable() {
        return [];
    }

    /**
     * Validation Rules
     *
     * @return array
     */
    protected function rules() {
        return [
            'service_id' => 'required|integer|exists:wp_b2bcnc_services,id',
            'addon_name' => 'required|string|max:200',
            'addon_description' => '?string',
            'addon_type' => 'required|in:upgrade,additional,extended_warranty,training,support',
            'price' => 'required|numeric|min:0',
            'is_recurring' => '?boolean',
            'billing_frequency' => '?in:monthly,quarterly,annually',
            'is_active' => '?boolean',
            'sort_order' => '?integer|min:0',
        ];
    }

    /**
     * Custom Error Messages
     *
     * @return array
     */
    protected function messages() {
        return [
            // Service ID validation messages
            'service_id.required' => 'Service is required.',
            'service_id.integer' => 'Service must be a valid ID.',
            'service_id.exists' => 'Selected service does not exist.',
            
            // Addon name validation messages
            'addon_name.required' => 'Addon name is required.',
            'addon_name.string' => 'Addon name must be text.',
            'addon_name.max' => 'Addon name cannot exceed 200 characters.',
            
            // Addon type validation messages
            'addon_type.required' => 'Addon type is required.',
            'addon_type.in' => 'Invalid addon type selected.',
            
            // Price validation messages
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            
            // Billing frequency validation messages
            'billing_frequency.in' => 'Invalid billing frequency selected.',
            
            // Sort order validation messages
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order cannot be negative.',
        ];
    }

    /**
     * Sanitize addon_name field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeAddonName($value)
    {
        if (empty($value)) {
            return null;
        }
        
        return trim(strip_tags((string) $value));
    }

    /**
     * Sanitize addon_description field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeAddonDescription($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Allow basic HTML tags in description
        $allowed_tags = '<p><br><strong><em><ul><ol><li><a>';
        return trim(strip_tags((string) $value, $allowed_tags));
    }

    /**
     * Process price field
     * 
     * @param mixed $value
     * @return float
     */
    public function sanitizePrice($value)
    {
        if ($value === null || $value === '') {
            return 0.00;
        }
        
        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^0-9.]/', '', (string) $value);
        return max(0, (float) $cleaned);
    }

    /**
     * Process is_recurring field
     * 
     * @param mixed $value
     * @return bool
     */
    public function sanitizeIsRecurring($value)
    {
        return (bool) $value;
    }

    /**
     * Process billing_frequency field - only set if is_recurring is true
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeBillingFrequency($value)
    {
        // If not recurring, billing frequency should be null
        if (!$this->getProperty('is_recurring')) {
            return null;
        }
        
        $allowed = ['monthly', 'quarterly', 'annually'];
        return in_array($value, $allowed) ? $value : null;
    }

    /**
     * Process is_active field
     * 
     * @param mixed $value
     * @return bool
     */
    public function sanitizeIsActive($value)
    {
        if ($value === null || $value === '') {
            return true; // Default to active
        }
        
        return (bool) $value;
    }

    /**
     * Process sort_order field
     * 
     * @param mixed $value
     * @return int
     */
    public function sanitizeSortOrder($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }
        
        return max(0, (int) $value);
    }
}