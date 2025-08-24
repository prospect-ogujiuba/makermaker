<?php
namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;

class ServiceComplexityFields extends Fields
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
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:wp_b2bcnc_service_complexity,slug,' . $this->getModelId(),
            'description' => '?string',
            'sort_order' => '?integer|min:0',
            'is_active' => '?boolean'
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
            'name.required' => 'Complexity level name is required.',
            'name.string' => 'Complexity level name must be text.',
            'name.max' => 'Complexity level name cannot exceed 100 characters.',
            
            // Slug validation messages
            'slug.required' => 'Complexity slug is required.',
            'slug.string' => 'Complexity slug must be text.',
            'slug.max' => 'Complexity slug cannot exceed 100 characters.',
            'slug.unique' => 'This slug is already in use. Please choose a different one.',
            
            // Sort order validation messages
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order cannot be negative.',
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
        $allowed_tags = '<p><br><strong><em><ul><ol><li>';
        return trim(strip_tags((string) $value, $allowed_tags));
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
}