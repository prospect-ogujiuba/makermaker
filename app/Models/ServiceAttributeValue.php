<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceAttributeValue extends Model
{
    protected $resource = 'srvc_attribute_values';

    protected $fillable = [
        'service_id',
        'attribute_definition_id',
        'value',
        'created_by',
        'updated_by',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /** ServiceAttributeValue belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceAttributeValue belongs to a ServiceAttributeDefinition */
    public function attributeDefinition()
    {
        return $this->belongsTo(ServiceAttributeDefinition::class, 'attribute_definition_id');
    }

    /** Created by WP user */
    public function createdBy()
    {
        return $this->belongsTo(\TypeRocket\Models\WPUser::class, 'created_by');
    }

    /** Updated by WP user */
    public function updatedBy()
    {
        return $this->belongsTo(\TypeRocket\Models\WPUser::class, 'updated_by');
    }

    /**
     * Automatically serialize when storing
     */
    public function setValueAttribute($value)
    {
        // If it's already serialized, don't double-serialize
        if (is_string($value) && @unserialize($value) !== false) {
            $this->attributes['value'] = $value;
        } else {
            $this->attributes['value'] = serialize($value);
        }
    }

    /**
     * Automatically unserialize when retrieving
     */
    public function getValueAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Get the properly typed/formatted value based on attribute definition
     */
    public function getTypedValue()
    {
        $definition = $this->attributeDefinition;
        if (!$definition) {
            return $this->value;
        }

        return $this->formatValueByType($this->value, $definition->data_type);
    }

    /**
     * Set value with automatic type conversion and storage
     */
    public function setTypedValue($rawValue)
    {
        $definition = $this->attributeDefinition;
        if (!$definition) {
            throw new \Exception('Attribute definition required to set typed value');
        }

        $convertedValue = $this->convertValueByType($rawValue, $definition->data_type);
        $this->value = $convertedValue; // This will trigger setValueAttribute()
        
        return $this;
    }

    /**
     * Convert raw input to appropriate type for storage
     */
    private function convertValueByType($value, $dataType)
    {
        switch ($dataType) {
            case 'int':
                return (int) $value;
            case 'decimal':
                return (float) $value;
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
            case 'date':
                return date('Y-m-d', strtotime($value));
            case 'datetime':
                return date('Y-m-d H:i:s', strtotime($value));
            case 'json':
                return is_string($value) ? json_decode($value, true) : $value;
            case 'enum':
            case 'text':
            case 'url':
            case 'email':
            default:
                return (string) $value;
        }
    }

    /**
     * Format stored value for display/usage
     */
    private function formatValueByType($value, $dataType)
    {
        switch ($dataType) {
            case 'int':
                return (int) $value;
            case 'decimal':
                return (float) $value;
            case 'bool':
                return (bool) $value;
            case 'date':
                return new \DateTime($value);
            case 'datetime':
                return new \DateTime($value);
            case 'json':
                return $value; // Already unserialized as array/object
            default:
                return $value;
        }
    }

    /**
     * Get display-ready formatted value
     */
    public function getFormattedValue()
    {
        $definition = $this->attributeDefinition;
        if (!$definition) {
            return $this->value;
        }

        $typedValue = $this->getTypedValue();

        switch ($definition->data_type) {
            case 'bool':
                return $typedValue ? 'Yes' : 'No';
            case 'date':
                return $typedValue->format('M j, Y');
            case 'datetime':
                return $typedValue->format('M j, Y g:i A');
            case 'decimal':
                $unit = $definition->unit ? ' ' . $definition->unit : '';
                return number_format($typedValue, 2) . $unit;
            case 'int':
                $unit = $definition->unit ? ' ' . $definition->unit : '';
                return number_format($typedValue) . $unit;
            case 'json':
                return json_encode($typedValue, JSON_PRETTY_PRINT);
            default:
                return (string) $typedValue;
        }
    }

    /**
     * Validate value against attribute definition
     */
    public function validateValue($value = null)
    {
        $definition = $this->attributeDefinition;
        if (!$definition) {
            return ['Valid' => false, 'Message' => 'Attribute definition not found'];
        }

        $checkValue = $value ?? $this->value;

        // Required check
        if ($definition->required && (is_null($checkValue) || $checkValue === '')) {
            return ['Valid' => false, 'Message' => $definition->label . ' is required'];
        }

        // Type-specific validation
        switch ($definition->data_type) {
            case 'enum':
                $enumOptions = json_decode($definition->enum_options, true);
                if (!in_array($checkValue, $enumOptions)) {
                    return ['Valid' => false, 'Message' => 'Value must be one of: ' . implode(', ', $enumOptions)];
                }
                break;
            case 'email':
                if (!filter_var($checkValue, FILTER_VALIDATE_EMAIL)) {
                    return ['Valid' => false, 'Message' => 'Must be a valid email address'];
                }
                break;
            case 'url':
                if (!filter_var($checkValue, FILTER_VALIDATE_URL)) {
                    return ['Valid' => false, 'Message' => 'Must be a valid URL'];
                }
                break;
        }

        return ['Valid' => true, 'Message' => 'Valid'];
    }
}