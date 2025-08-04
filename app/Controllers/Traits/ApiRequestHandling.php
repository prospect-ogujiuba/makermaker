<?php

namespace MakerMaker\Controllers\Traits;

use TypeRocket\Http\Request;

/**
 * ApiRequestHandling Trait
 * 
 * Provides request parsing and validation methods for API controllers.
 * Standardizes how request data is handled across endpoints.
 */
trait ApiRequestHandling
{
    /**
     * Parse request data from various HTTP methods
     * Handles JSON, form data, and query parameters
     * 
     * @param Request $request The request object
     * @return array Parsed request data
     */
    protected function parseRequestData(Request $request): array
    {
        // Try to get JSON data first
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // If not JSON, try form data
        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            parse_str($input, $data);
        }

        // Fallback to request fields
        if (empty($data)) {
            $data = $request->getFields();
        }

        return $data ?: [];
    }

    /**
     * Validate required fields in request data
     * 
     * @param array $data Request data
     * @param array $required Required field names
     * @return array Empty if valid, error messages if invalid
     */
    protected function validateRequiredFields(array $data, array $required): array
    {
        $errors = [];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $errors[$field] = "The {$field} field is required.";
            }
        }
        
        return $errors;
    }

    /**
     * Validate PATCH request data against allowed fields
     * 
     * @param array $data Request data
     * @param array $allowed Allowed field names
     * @return array Validation errors if any
     */
    protected function validatePatchData(array $data, array $allowed): array
    {
        $errors = [];
        
        foreach ($data as $field => $value) {
            if (!in_array($field, $allowed)) {
                continue; // Skip unknown fields
            }

            // Add field-specific validation here
            $fieldErrors = $this->validateField($field, $value);
            if (!empty($fieldErrors)) {
                $errors[$field] = $fieldErrors;
            }
        }

        return $errors;
    }

    /**
     * Validate individual field - override in controller for custom validation
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @return array Errors for this field
     */
    protected function validateField(string $field, $value): array
    {
        $errors = [];

        switch ($field) {
            case 'base_price':
            case 'price':
                if (!is_numeric($value) || $value < 0) {
                    $errors[] = "The {$field} must be a positive number.";
                }
                break;
                
            case 'active':
                if (!in_array($value, ['0', '1', 0, 1, true, false], true)) {
                    $errors[] = "The {$field} field must be true or false.";
                }
                break;
                
            case 'code':
                if (!preg_match('/^[a-z0-9_]+$/', $value)) {
                    $errors[] = "The {$field} may only contain lowercase letters, numbers, and underscores.";
                }
                break;
                
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "The {$field} must be a valid email address.";
                }
                break;
        }

        return $errors;
    }

    /**
     * Sanitize input data
     * 
     * @param array $data Input data
     * @return array Sanitized data
     */
    protected function sanitizeData(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            $sanitized[$key] = $this->sanitizeValue($key, $value);
        }
        
        return $sanitized;
    }

    /**
     * Sanitize individual value based on field type
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @return mixed Sanitized value
     */
    protected function sanitizeValue(string $field, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($field) {
            case 'code':
                return sanitize_key($value); // WordPress function
                
            case 'name':
            case 'title':
                return sanitize_text_field($value);
                
            case 'description':
                return sanitize_textarea_field($value);
                
            case 'email':
                return sanitize_email($value);
                
            case 'url':
                return esc_url_raw($value);
                
            case 'base_price':
            case 'price':
                return number_format((float)$value, 2, '.', '');
                
            case 'active':
                return (bool) $value;
                
            default:
                if (is_string($value)) {
                    return sanitize_text_field($value);
                }
                return $value;
        }
    }

    /**
     * Get allowed fields for updates - override in controller
     * 
     * @return array Allowed field names
     */
    protected function getAllowedFields(): array
    {
        return ['code', 'name', 'description', 'base_price', 'icon', 'active'];
    }

    /**
     * Get required fields for creation - override in controller
     * 
     * @return array Required field names
     */
    protected function getRequiredFields(): array
    {
        return ['name'];
    }

    /**
     * Filter data to only include allowed fields
     * 
     * @param array $data Input data
     * @param array $allowed Allowed field names
     * @return array Filtered data
     */
    protected function filterAllowedFields(array $data, array $allowed): array
    {
        return array_intersect_key($data, array_flip($allowed));
    }
}