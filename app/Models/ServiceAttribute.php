<?php
namespace MakerMaker\Models;

use TypeRocket\Models\Model;

/**
 * ServiceAttribute Model
 * 
 * Represents custom attributes and metadata for services
 * Table: wp_b2bcnc_service_attributes
 */
class ServiceAttribute extends Model
{
    protected $resource = 'b2bcnc_service_attributes';
    
    protected $fillable = [
        'service_id',
        'attribute_name',
        'attribute_value',
        'attribute_type',
        'is_configurable',
        'display_order'
    ];

    protected $casts = [
        'service_id' => 'integer',
        'is_configurable' => 'boolean',
        'display_order' => 'integer'
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Relationship to the parent service
     * wp_b2bcnc_service_attributes.service_id -> wp_b2bcnc_services.id
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Get only configurable attributes
     */
    public function scopeConfigurable($query)
    {
        return $query->where('is_configurable', 1);
    }

    /**
     * Get non-configurable (display-only) attributes
     */
    public function scopeDisplayOnly($query)
    {
        return $query->where('is_configurable', 0);
    }

    /**
     * Get attributes by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('attribute_type', $type);
    }

    /**
     * Get attributes for a specific service
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Order by display order and name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'ASC')->orderBy('attribute_name', 'ASC');
    }

    /**
     * Search by attribute name
     */
    public function scopeByName($query, $name)
    {
        return $query->where('attribute_name', 'LIKE', '%' . $name . '%');
    }

    // ==========================================
    // UTILITY METHODS
    // ==========================================

    /**
     * Get attribute type display text
     */
    public function getAttributeTypeText()
    {
        $types = [
            'text' => 'Text',
            'number' => 'Number',
            'boolean' => 'Boolean (Yes/No)',
            'json' => 'JSON Data',
            'url' => 'URL',
            'email' => 'Email Address'
        ];
        
        return $types[$this->attribute_type] ?? ucfirst($this->attribute_type);
    }

    /**
     * Get formatted attribute value based on type
     */
    public function getFormattedValue()
    {
        if (empty($this->attribute_value)) {
            return '';
        }

        switch ($this->attribute_type) {
            case 'boolean':
                return $this->getBooleanValue() ? 'Yes' : 'No';
                
            case 'number':
                return is_numeric($this->attribute_value) ? number_format((float)$this->attribute_value, 2) : $this->attribute_value;
                
            case 'url':
                return '<a href="' . esc_url($this->attribute_value) . '" target="_blank" rel="noopener">' . 
                       esc_html($this->attribute_value) . '</a>';
                
            case 'email':
                return '<a href="mailto:' . esc_attr($this->attribute_value) . '">' . 
                       esc_html($this->attribute_value) . '</a>';
                
            case 'json':
                $decoded = json_decode($this->attribute_value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return '<pre>' . json_encode($decoded, JSON_PRETTY_PRINT) . '</pre>';
                }
                return esc_html($this->attribute_value);
                
            case 'text':
            default:
                return esc_html($this->attribute_value);
        }
    }

    /**
     * Get boolean value (for boolean type attributes)
     */
    public function getBooleanValue()
    {
        if ($this->attribute_type !== 'boolean') {
            return null;
        }
        
        $value = strtolower(trim($this->attribute_value));
        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    /**
     * Get numeric value (for number type attributes)
     */
    public function getNumericValue()
    {
        if ($this->attribute_type !== 'number') {
            return null;
        }
        
        return is_numeric($this->attribute_value) ? (float)$this->attribute_value : 0;
    }

    /**
     * Get JSON value (for json type attributes)
     */
    public function getJsonValue()
    {
        if ($this->attribute_type !== 'json') {
            return null;
        }
        
        $decoded = json_decode($this->attribute_value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    /**
     * Check if attribute value is valid for its type
     */
    public function isValidValue()
    {
        if (empty($this->attribute_value)) {
            return true; // Empty values are generally acceptable
        }

        switch ($this->attribute_type) {
            case 'number':
                return is_numeric($this->attribute_value);
                
            case 'boolean':
                $value = strtolower(trim($this->attribute_value));
                return in_array($value, ['0', '1', 'true', 'false', 'yes', 'no', 'on', 'off'], true);
                
            case 'url':
                return filter_var($this->attribute_value, FILTER_VALIDATE_URL) !== false;
                
            case 'email':
                return filter_var($this->attribute_value, FILTER_VALIDATE_EMAIL) !== false;
                
            case 'json':
                json_decode($this->attribute_value);
                return json_last_error() === JSON_ERROR_NONE;
                
            case 'text':
            default:
                return true; // Text values are always valid
        }
    }

    /**
     * Get display name with type info
     */
    public function getDisplayNameWithType()
    {
        return $this->attribute_name . ' (' . $this->getAttributeTypeText() . ')';
    }

    /**
     * Check if this attribute can be deleted safely
     */
    public function canBeDeleted()
    {
        // Add business logic if certain attributes should be protected
        // For example, system-required attributes
        return true;
    }

    /**
     * Get the next display order value for a service
     */
    public static function getNextDisplayOrder($serviceId)
    {
        $maxOrder = static::where('service_id', $serviceId)->max('display_order');
        return ($maxOrder ? $maxOrder + 1 : 1);
    }

    /**
     * Sanitize attribute value based on type
     */
    public function sanitizeValue($value)
    {
        if (empty($value)) {
            return null;
        }

        switch ($this->attribute_type) {
            case 'number':
                return is_numeric($value) ? (string)(float)$value : '0';
                
            case 'boolean':
                $cleaned = strtolower(trim($value));
                if (in_array($cleaned, ['1', 'true', 'yes', 'on'], true)) {
                    return 'true';
                } elseif (in_array($cleaned, ['0', 'false', 'no', 'off'], true)) {
                    return 'false';
                }
                return 'false';
                
            case 'url':
                $url = trim($value);
                if (!preg_match('/^https?:\/\//', $url) && filter_var('http://' . $url, FILTER_VALIDATE_URL)) {
                    return 'http://' . $url;
                }
                return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
                
            case 'email':
                return filter_var(trim($value), FILTER_VALIDATE_EMAIL) ? trim($value) : '';
                
            case 'json':
                if (is_array($value)) {
                    return json_encode($value);
                }
                // Validate JSON
                json_decode($value);
                return json_last_error() === JSON_ERROR_NONE ? $value : '{}';
                
            case 'text':
            default:
                return strip_tags(trim($value));
        }
    }

    /**
     * Set attribute value with automatic sanitization
     */
    public function setValue($value)
    {
        $this->attribute_value = $this->sanitizeValue($value);
        return $this;
    }

    /**
     * Auto-set display order before saving if not provided
     */
    protected function beforeSave()
    {
        if (empty($this->display_order) && $this->service_id) {
            $this->display_order = static::getNextDisplayOrder($this->service_id);
        }
        
        parent::beforeSave();
    }
}