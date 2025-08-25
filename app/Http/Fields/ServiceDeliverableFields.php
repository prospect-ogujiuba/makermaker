<?php
namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;

class ServiceDeliverableFields extends Fields
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
            'deliverable_name' => 'required|string|max:200',
            'deliverable_description' => '?string',
            'deliverable_type' => 'required|in:equipment,software,documentation,training,access,support',
            'is_included' => '?boolean',
            'quantity' => '?integer|min:1',
            'unit_of_measure' => '?string|max:50',
            'additional_cost' => '?numeric|min:0',
            'delivery_timeframe' => '?string|max:100',
            'sort_order' => '?integer|min:0'
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
            
            // Deliverable name validation messages
            'deliverable_name.required' => 'Deliverable name is required.',
            'deliverable_name.string' => 'Deliverable name must be text.',
            'deliverable_name.max' => 'Deliverable name cannot exceed 200 characters.',
            
            // Deliverable type validation messages
            'deliverable_type.required' => 'Deliverable type is required.',
            'deliverable_type.in' => 'Invalid deliverable type selected.',
            
            // Quantity validation messages
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            
            // Unit of measure validation messages
            'unit_of_measure.max' => 'Unit of measure cannot exceed 50 characters.',
            
            // Additional cost validation messages
            'additional_cost.numeric' => 'Additional cost must be a valid number.',
            'additional_cost.min' => 'Additional cost cannot be negative.',
            
            // Delivery timeframe validation messages
            'delivery_timeframe.max' => 'Delivery timeframe cannot exceed 100 characters.',
            
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
     * Sanitize the deliverable_name field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeDeliverableName($value)
    {
        if (empty($value)) {
            return null;
        }
        
        return trim(strip_tags((string) $value));
    }

    /**
     * Sanitize the deliverable_description field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeDeliverableDescription($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Allow basic HTML tags in description
        $allowed_tags = '<p><br><strong><em><ul><ol><li><a>';
        return trim(strip_tags((string) $value, $allowed_tags));
    }

    /**
     * Process additional_cost field
     * 
     * @param mixed $value
     * @return float
     */
    public function sanitizeAdditionalCost($value)
    {
        if ($value === null || $value === '') {
            return 0.00;
        }
        
        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^0-9.]/', '', (string) $value);
        return $cleaned ? max(0, (float) $cleaned) : 0.00;
    }

    /**
     * Sanitize delivery_timeframe field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeDeliveryTimeframe($value)
    {
        if (empty($value)) {
            return null;
        }
        
        return trim(strip_tags((string) $value));
    }

    /**
     * Process is_included field
     * 
     * @param mixed $value
     * @return bool
     */
    public function sanitizeIsIncluded($value)
    {
        if ($value === null || $value === '') {
            return true; // Default to included
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
            return 0; // Will be auto-generated in model beforeSave
        }
        
        return max(0, (int) $value);
    }

    /**
     * Custom validation after standard validation
     */
    protected function afterValidation()
    {
        $isIncluded = $this->getProperty('is_included');
        $additionalCost = $this->getProperty('additional_cost');
        $deliverableType = $this->getProperty('deliverable_type');
        $quantity = $this->getProperty('quantity');
        
        // Business logic validation
        
        // If included, additional cost should be 0
        if ($isIncluded && $additionalCost > 0) {
            $this->addCustomError('additional_cost', 'Included deliverables should not have additional cost.');
        }
        
        // Validate reasonable quantities for certain types
        if ($deliverableType === 'training' && $quantity > 50) {
            $this->addCustomError('quantity', 'Training quantity seems unusually high. Please verify.');
        }
        
        if ($deliverableType === 'documentation' && $quantity > 20) {
            $this->addCustomError('quantity', 'Documentation quantity seems unusually high. Please verify.');
        }
        
        // Validate cost reasonableness
        if ($additionalCost > 50000) {
            $this->addCustomError('additional_cost', 'Additional cost seems unusually high. Please verify.');
        }
        
        // Check for duplicate deliverable names within the same service
        $serviceId = $this->getProperty('service_id');
        $deliverableName = $this->getProperty('deliverable_name');
        $modelId = $this->getModelId();
        
        if ($serviceId && $deliverableName) {
            $existingDeliverable = \MakerMaker\Models\ServiceDeliverable::new()
                ->where('service_id', $serviceId)
                ->where('deliverable_name', $deliverableName)
                ->where('id', '!=', $modelId ?: 0)
                ->first();
                
            if ($existingDeliverable) {
                $this->addCustomError('deliverable_name', 'This deliverable name already exists for this service.');
            }
        }
        
        // Validate timeframe format
        $timeframe = $this->getProperty('delivery_timeframe');
        if ($timeframe && !$this->isValidTimeframe($timeframe)) {
            $this->addCustomError('delivery_timeframe', 'Please use a clear timeframe (e.g., "2 weeks", "Upon completion", "Same day").');
        }
    }

    /**
     * Validate timeframe format
     */
    private function isValidTimeframe($timeframe)
    {
        $timeframe = strtolower(trim($timeframe));
        
        // Allow common patterns
        $validPatterns = [
            '/^\d+\s*(day|week|month)s?$/',           // "2 weeks", "1 day"
            '/^upon\s+(completion|delivery)/',         // "upon completion"
            '/^same\s+day$/',                         // "same day"
            '/^immediate(ly)?$/',                     // "immediately"
            '/^next\s+business\s+day$/',              // "next business day"
            '/^within\s+\d+\s*(day|week|month)s?$/',  // "within 5 days"
            '/^after\s+/',                            // "after installation"
            '/^during\s+/',                           // "during service"
            '/^at\s+/'                                // "at completion"
        ];
        
        foreach ($validPatterns as $pattern) {
            if (preg_match($pattern, $timeframe)) {
                return true;
            }
        }
        
        // If it's short and doesn't match patterns, it might still be valid
        return strlen($timeframe) <= 50;
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
     * Get suggested units of measure for deliverable types
     */
    public static function getSuggestedUnits($deliverableType)
    {
        $units = [
            'equipment' => ['unit', 'device', 'piece', 'set'],
            'software' => ['license', 'copy', 'seat', 'installation'],
            'documentation' => ['document', 'manual', 'guide', 'page'],
            'training' => ['session', 'hour', 'course', 'module'],
            'access' => ['account', 'credential', 'key', 'permission'],
            'support' => ['month', 'year', 'incident', 'hour']
        ];
        
        return $units[$deliverableType] ?? ['item'];
    }

    /**
     * Get suggested timeframes for deliverable types
     */
    public static function getSuggestedTimeframes($deliverableType)
    {
        $timeframes = [
            'equipment' => ['Upon installation', 'Same day', '1 week'],
            'software' => ['Upon completion', 'Same day', '2 days'],
            'documentation' => ['Upon completion', '1 week', '2 weeks'],
            'training' => ['After installation', '1 week', '2 weeks'],
            'access' => ['Same day', 'Upon completion', 'Immediately'],
            'support' => ['Immediately', 'Upon activation', 'Same day']
        ];
        
        return $timeframes[$deliverableType] ?? ['Upon completion'];
    }
} quantity field
     * 
     * @param mixed $value
     * @return int
     */
    public function sanitizeQuantity($value)
    {
        if ($value === null || $value === '') {
            return 1; // Default quantity
        }
        
        return max(1, (int) $value);
    }

    /**
     * Sanitize unit_of_measure field
     * 
     * @param mixed $value
     * @return string|null
     */
    public function sanitizeUnitOfMeasure($value)
    {
        if (empty($value)) {
            return 'item'; // Default unit
        }
        
        return trim(strtolower(strip_tags((string) $value)));
    }

    /**
     * Process