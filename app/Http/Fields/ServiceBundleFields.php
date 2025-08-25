<?php
namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;

class ServiceBundleFields extends Fields
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
            'name' => 'required|string|max:200',
            'slug' => 'required|string|max:200|unique:wp_b2bcnc_service_bundles,slug,' . $this->getModelId(),
            'description' => '?string',
            'bundle_type' => 'required|in:package,addon_group,maintenance_plan,enterprise',
            'base_price' => '?numeric|min:0',
            'discount_percentage' => '?numeric|min:0|max:100',
            'is_active' => '?boolean',
            'min_commitment_months' => '?integer|min:0'
        ];
    }

    /**
     * Custom Error Messages
     *
     * @return array
     */
    protected function messages() {
        return [
            // Name validation messages
            'name.required' => 'Bundle name is required.',
            'name.string' => 'Bundle name must be text.',
            'name.max' => 'Bundle name cannot exceed 200 characters.',
            
            // Slug validation messages
            'slug.required' => 'Bundle slug is required.',
            'slug.string' => 'Bundle slug must be text.',
            'slug.max' => 'Bundle slug cannot exceed 200 characters.',
            'slug.unique' => 'This slug is already in use. Please choose a different one.',
            
            // Bundle type validation messages
            'bundle_type.required' => 'Bundle type is required.',
            'bundle_type.in' => 'Invalid bundle type selected.',
            
            // Price validation messages
            'base_price.numeric' => 'Base price must be a valid number.',
            'base_price.min' => 'Base price cannot be negative.',
            
            // Discount validation messages
            'discount_percentage.numeric' => 'Discount percentage must be a valid number.',
            'discount_percentage.min' => 'Discount percentage cannot be negative.',
            'discount_percentage.max' => 'Discount percentage cannot exceed 100%.',
            
            // Commitment validation messages
            'min_commitment_months.integer' => 'Minimum commitment must be a whole number.',
            'min_commitment_months.min' => 'Minimum commitment cannot be negative.',
        ];
    }

    /**
     * Get model ID for unique validation
     */
    protected function getModelId()
    {
        return $this->getModel() ? $this->getModel()->getID() : null;
    }

    /**
     * Sanitize the name field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeName($value)
    {
        if (empty($value)) {
            return null;
        }
        
        return trim(strip_tags((string) $value));
    }

    /**
     * Sanitize the slug field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeSlug($value)
    {
        if (empty($value)) {
            // Auto-generate from name if slug is empty
            $name = $this->getProperty('name');
            if ($name) {
                $slug = strtolower(trim($name));
                $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
                $slug = preg_replace('/-+/', '-', $slug);
                $slug = trim($slug, '-');
                return $slug;
            }
            return null;
        }
        
        // Convert to lowercase, replace spaces and special chars with hyphens
        $slug = strtolower(trim((string) $value));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }

    /**
     * Sanitize description field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeDescription($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Allow basic HTML tags in description
        $allowed_tags = '<p><br><strong><em><ul><ol><li><a><h3><h4>';
        return trim(strip_tags((string) $value, $allowed_tags));
    }

    /**
     * Process base_price field
     * 
     * @param mixed $value
     * @return float|null
     */
    public function sanitizeBasePrice($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^0-9.]/', '', (string) $value);
        return $cleaned ? max(0, (float) $cleaned) : null;
    }

    /**
     * Process discount_percentage field
     * 
     * @param mixed $value
     * @return float
     */
    public function sanitizeDiscountPercentage($value)
    {
        if ($value === null || $value === '') {
            return 0.00;
        }
        
        // Remove percentage symbol and formatting
        $cleaned = preg_replace('/[^0-9.]/', '', (string) $value);
        return $cleaned ? max(0, min(100, (float) $cleaned)) : 0.00;
    }

    /**
     * Process min_commitment_months field
     * 
     * @param mixed $value
     * @return int|null
     */
    public function sanitizeMinCommitmentMonths($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        return max(0, (int) $value);
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
     * Process bundle services data
     * 
     * @param mixed $value
     * @return array|null
     */
    public function sanitizeBundleServices($value)
    {
        if (!is_array($value)) {
            return null;
        }
        
        $cleaned = [];
        foreach ($value as $serviceData) {
            if (!is_array($serviceData) || empty($serviceData['service_id'])) {
                continue;
            }
            
            $cleaned[] = [
                'service_id' => (int) $serviceData['service_id'],
                'quantity' => max(1, (int) ($serviceData['quantity'] ?? 1)),
                'is_optional' => !empty($serviceData['is_optional']),
                'sort_order' => (int) ($serviceData['sort_order'] ?? 0)
            ];
        }
        
        return $cleaned;
    }

    /**
     * Custom validation for bundle type and pricing consistency
     */
    protected function afterValidation()
    {
        $bundleType = $this->getProperty('bundle_type');
        $basePrice = $this->getProperty('base_price');
        $discountPercentage = $this->getProperty('discount_percentage');
        
        // Maintenance plans should typically have commitment
        if ($bundleType === 'maintenance_plan') {
            $commitment = $this->getProperty('min_commitment_months');
            if (!$commitment || $commitment < 1) {
                $this->addCustomError('min_commitment_months', 'Maintenance plans typically require a minimum commitment period.');
            }
        }
        
        // Enterprise bundles should have custom pricing
        if ($bundleType === 'enterprise' && !$basePrice) {
            $this->addCustomError('base_price', 'Enterprise bundles should have a base price set.');
        }
        
        // Validate discount percentage makes sense
        if ($discountPercentage > 0 && !$basePrice) {
            $this->addCustomError('base_price', 'Base price is required when discount percentage is set.');
        }
    }

    /**
     * Add custom validation error
     */
    private function addCustomError($field, $message)
    {
        $errors = $this->getErrors();
        $errors[$field] = $errors[$field] ?? [];
        $errors[$field][] = $message;
        $this->setErrors($errors);
    }
}