<?php
namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;

class ServiceAttributeFields extends Fields
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
            'attribute_name' => 'required|string|max:100',
            'attribute_value' => '?string',
            'attribute_type' => 'required|in:text,number,boolean,json,url,email',
            'is_configurable' => '?boolean',
            'display_order' => '?integer|min:0'
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
            
            // Attribute name validation messages
            'attribute_name.required' => 'Attribute name is required.',
            'attribute_name.string' => 'Attribute name must be text.',
            'attribute_name.max' => 'Attribute name cannot exceed 100 characters.',
            
            // Attribute type validation messages
            'attribute_type.required' => 'Attribute type is required.',
            'attribute_type.in' => 'Invalid attribute type selected.',
            
            // Display order validation messages
            'display_order.integer' => 'Display order must be a number.',
            'display_order.min' => 'Display order cannot be negative.',
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
     * Sanitize the attribute_name field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeAttributeName($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Clean attribute name - allow letters, numbers, underscores, spaces, and hyphens
        $cleaned = preg_replace('/[^a-zA-Z0-9_\s-]/', '', trim($value));
        return $cleaned;
    }

    /**
     * Sanitize the attribute_value field based on attribute_type
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeAttributeValue($value)
    {
        $attributeType = $this->getProperty('attribute_type');
        
        if (empty($value)) {
            return null;
        }

        // Create temporary attribute instance for sanitization
        $tempAttribute = new \MakerMaker\Models\ServiceAttribute();
        $tempAttribute->attribute_type = $attributeType;
        
        return $tempAttribute->sanitizeValue($value);
    }

    /**
     * Process is_configurable field
     * 
     * @param mixed $value
     * @return bool
     */
    public function sanitizeIsConfigurable($value)
    {
        return (bool) $value;
    }

    /**
     * Process display_order field
     * 
     * @param mixed $value
     * @return int
     */
    public function sanitizeDisplayOrder($value)
    {
        if ($value === null || $value === '') {
            return 0; // Will be auto-generated in model beforeSave
        }
        
        return max(0, (int) $value);
    }

    /**
     * Custom validation after standard validation
     */
    protected function afterValidation()
    {
        $attributeType = $this->getProperty('attribute_type');
        $attributeValue = $this->getProperty('attribute_value');
        
        // Skip value validation if value is empty
        if (empty($attributeValue)) {
            return;
        }

        // Validate value based on type
        if ($attributeType && $attributeValue) {
            $tempAttribute = new \MakerMaker\Models\ServiceAttribute();
            $tempAttribute->attribute_type = $attributeType;
            $tempAttribute->attribute_value = $attributeValue;
            
            if (!$tempAttribute->isValidValue()) {
                $this->addCustomError('attribute_value', $this->getTypeSpecificError($attributeType));
            }
        }

        // Check for duplicate attribute names within the same service
        $serviceId = $this->getProperty('service_id');
        $attributeName = $this->getProperty('attribute_name');
        $modelId = $this->getModelId();
        
        if ($serviceId && $attributeName) {
            $existingAttribute = \MakerMaker\Models\ServiceAttribute::new()
                ->where('service_id', $serviceId)
                ->where('attribute_name', $attributeName)
                ->where('id', '!=', $modelId ?: 0)
                ->first();
                
            if ($existingAttribute) {
                $this->addCustomError('attribute_name', 'This attribute name already exists for this service.');
            }
        }
    }

    /**
     * Get type-specific error message
     */
    private function getTypeSpecificError($type)
    {
        switch ($type) {
            case 'number':
                return 'Attribute value must be a valid number.';
            case 'boolean':
                return 'Attribute value must be true/false, yes/no, 1/0, or on/off.';
            case 'url':
                return 'Attribute value must be a valid URL.';
            case 'email':
                return 'Attribute value must be a valid email address.';
            case 'json':
                return 'Attribute value must be valid JSON format.';
            default:
                return 'Attribute value is not valid for the selected type.';
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

    /**
     * Custom field processing for JSON values
     */
    public function processJsonValue($value)
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES);
        }
        
        if (is_string($value)) {
            // Validate JSON
            json_decode($value);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $value;
            }
        }
        
        return '{}';
    }

    /**
     * Custom field processing for boolean values
     */
    public function processBooleanValue($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        $cleaned = strtolower(trim($value));
        return in_array($cleaned, ['1', 'true', 'yes', 'on'], true) ? 'true' : 'false';
    }

    /**
     * Custom field processing for number values
     */
    public function processNumberValue($value)
    {
        if (is_numeric($value)) {
            return (string)(float)$value;
        }
        
        return '0';
    }
}