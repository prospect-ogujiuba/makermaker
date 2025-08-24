<?php
namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use MakerMaker\Models\ServiceCategory;

/**
 * ServiceCategoryFields
 * 
 * Validation and field processing for service category forms
 * Handles hierarchical validation and slug generation
 */
class ServiceCategoryFields extends Fields
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
     * Define which fields can be mass assigned
     *
     * @return array
     */
    protected function fillable() 
    {
        return [
            'parent_id',
            'name',
            'slug',
            'description',
            'icon',
            'sort_order',
            'is_active'
        ];
    }

    /**
     * Validation Rules
     * 
     * Define validation rules for each field
     *
     * @return array
     */
    protected function rules() 
    {
        $categoryId = $this->getModelId();
        
        return [
            // Required fields
            'name' => 'required|string|max:100',
            
            // Slug validation with uniqueness check
            'slug' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9-]+$/',
                'unique:b2bcnc_service_categories,slug,' . ($categoryId ?: 'NULL')
            ],
            
            // Parent validation (prevent self-reference and circular references)
            'parent_id' => [
                'nullable',
                'integer',
                'exists:b2bcnc_service_categories,id',
                function ($attribute, $value, $fail) use ($categoryId) {
                    if ($value && $categoryId && $value == $categoryId) {
                        $fail('A category cannot be its own parent.');
                    }
                    
                    if ($value && $categoryId) {
                        // Check for circular reference
                        if ($this->wouldCreateCircularReference($categoryId, $value)) {
                            $fail('This parent selection would create a circular reference.');
                        }
                    }
                }
            ],
            
            // Optional fields with constraints
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean'
        ];
    }

    /**
     * Custom Error Messages
     * 
     * Define custom validation error messages
     *
     * @return array
     */
    protected function messages() 
    {
        return [
            // Name validation messages
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name cannot exceed 100 characters.',
            
            // Slug validation messages
            'slug.required' => 'Category slug is required.',
            'slug.regex' => 'Category slug can only contain lowercase letters, numbers, and hyphens.',
            'slug.max' => 'Category slug cannot exceed 100 characters.',
            'slug.unique' => 'This slug is already in use. Please choose a different one.',
            
            // Parent validation messages
            'parent_id.exists' => 'Selected parent category does not exist.',
            'parent_id.integer' => 'Parent category must be a valid ID.',
            
            // Other field messages
            'description.max' => 'Description cannot exceed 1000 characters.',
            'icon.max' => 'Icon field cannot exceed 50 characters.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be 0 or greater.',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
            'is_active.boolean' => 'Active status must be true or false.'
        ];
    }

    // ==========================================
    // DATA PROCESSING METHODS
    // ==========================================

    /**
     * Process data before validation
     * Clean and normalize field values
     */
    protected function boot()
    {
        parent::boot();
        
        // Auto-generate slug from name if not provided
        if (empty($this->getField('slug')) && !empty($this->getField('name'))) {
            $this->setField('slug', $this->generateSlugFromName($this->getField('name')));
        }
        
        // Ensure slug is lowercase and properly formatted
        if ($slug = $this->getField('slug')) {
            $this->setField('slug', $this->sanitizeSlug($slug));
        }
        
        // Set default values
        if ($this->getField('sort_order') === null) {
            $this->setField('sort_order', $this->getNextSortOrder());
        }
        
        if ($this->getField('is_active') === null) {
            $this->setField('is_active', true);
        }
        
        // Convert empty parent_id to null
        if ($this->getField('parent_id') === '') {
            $this->setField('parent_id', null);
        }
    }

    // ==========================================
    // VALIDATION HELPER METHODS
    // ==========================================

    /**
     * Check if setting a parent would create a circular reference
     * 
     * @param int $categoryId Current category ID
     * @param int $parentId Proposed parent ID
     * @return bool
     */
    protected function wouldCreateCircularReference($categoryId, $parentId)
    {
        if (!$categoryId || !$parentId) {
            return false;
        }

        // Get the proposed parent and walk up its hierarchy
        $current = ServiceCategory::find($parentId);
        $visited = [];
        
        while ($current && $current->parent_id) {
            // If we find our own ID in the parent hierarchy, it's circular
            if ($current->parent_id == $categoryId) {
                return true;
            }
            
            // Prevent infinite loops in case of existing bad data
            if (in_array($current->parent_id, $visited)) {
                break;
            }
            
            $visited[] = $current->parent_id;
            $current = $current->parent;
        }
        
        return false;
    }

    /**
     * Get the next available sort order
     * 
     * @return int
     */
    protected function getNextSortOrder()
    {
        $maxOrder = ServiceCategory::max('sort_order');
        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Generate a slug from the name
     * 
     * @param string $name
     * @return string
     */
    protected function generateSlugFromName($name)
    {
        // Use WordPress sanitize_title if available, otherwise manual sanitization
        if (function_exists('sanitize_title')) {
            return sanitize_title($name);
        }
        
        return $this->sanitizeSlug($name);
    }

    /**
     * Sanitize a slug value
     * 
     * @param string $slug
     * @return string
     */
    protected function sanitizeSlug($slug)
    {
        // Convert to lowercase
        $slug = strtolower($slug);
        
        // Replace spaces and underscores with hyphens
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        
        // Remove any characters that aren't alphanumeric or hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        
        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-{2,}/', '-', $slug);
        
        // Trim hyphens from start and end
        $slug = trim($slug, '-');
        
        return $slug;
    }

    /**
     * Get the current model ID for validation context
     * 
     * @return int|null
     */
    protected function getModelId()
    {
        // Try to get from request/context - this depends on your TypeRocket setup
        $model = $this->getModel();
        
        if ($model && $model instanceof ServiceCategory && $model->exists) {
            return $model->getID();
        }
        
        // Fallback: try to get from request parameters
        $request = tr_request();
        if ($request && method_exists($request, 'getRouteVar')) {
            return $request->getRouteVar('id');
        }
        
        return null;
    }

    // ==========================================
    // FIELD SANITIZATION METHODS
    // ==========================================

    /**
     * Sanitize the name field
     * 
     * @param mixed $value
     * @return string
     */
    public function sanitizeName($value)
    {
        return trim(strip_tags((string) $value));
    }

    /**
     * Sanitize the description field
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
        $allowed_tags = '<p><br><strong><em><ul><ol><li><a>';
        return trim(strip_tags((string) $value, $allowed_tags));
    }

    /**
     * Sanitize the icon field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeIcon($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Remove any dangerous characters, allow dashicons format
        return preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $value);
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
            return $this->getNextSortOrder();
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
            return true;
        }
        
        return (bool) $value;
    }
}