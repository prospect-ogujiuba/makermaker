<?php

namespace MakerMaker\Helpers;

/**
 * API Helper Functions
 * 
 * Collection of utility functions for API operations.
 * These are generic helpers not tied to specific models.
 */
class ApiHelpers
{
    /**
     * Generate pagination metadata
     * 
     * @param int $currentPage Current page number
     * @param int $perPage Items per page
     * @param int $total Total number of items
     * @return array Pagination metadata
     */
    public static function generatePaginationMeta(int $currentPage, int $perPage, int $total): array
    {
        $totalPages = (int) ceil($total / $perPage);
        
        return [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_more' => $currentPage < $totalPages,
            'has_previous' => $currentPage > 1,
            'from' => (($currentPage - 1) * $perPage) + 1,
            'to' => min($currentPage * $perPage, $total)
        ];
    }

    /**
     * Validate pagination parameters
     * 
     * @param mixed $page Page number
     * @param mixed $limit Items per page
     * @param int $maxLimit Maximum allowed limit
     * @return array Validated [page, limit]
     */
    public static function validatePagination($page, $limit, int $maxLimit = 100): array
    {
        $page = max(1, (int) ($page ?? 1));
        $limit = min($maxLimit, max(1, (int) ($limit ?? 20)));
        
        return [$page, $limit];
    }

    /**
     * Parse boolean values from request
     * 
     * @param mixed $value Value to parse
     * @return bool|null
     */
    public static function parseBoolean($value): ?bool
    {
        if ($value === null) {
            return null;
        }
        
        if (is_bool($value)) {
            return $value;
        }
        
        $value = strtolower((string) $value);
        
        if (in_array($value, ['true', '1', 'yes', 'on'])) {
            return true;
        }
        
        if (in_array($value, ['false', '0', 'no', 'off'])) {
            return false;
        }
        
        return null;
    }

    /**
     * Format price for consistent display
     * 
     * @param mixed $price Price value
     * @param int $decimals Number of decimal places
     * @return string Formatted price
     */
    public static function formatPrice($price, int $decimals = 2): string
    {
        return number_format((float) $price, $decimals, '.', '');
    }

    /**
     * Generate unique code from string
     * 
     * @param string $input Input string
     * @param string $prefix Optional prefix
     * @return string Generated code
     */
    public static function generateCode(string $input, string $prefix = ''): string
    {
        // Remove special characters and convert to lowercase
        $code = strtolower($input);
        
        // Replace spaces and common separators with underscores
        $code = preg_replace('/[\s\-\.]+/', '_', $code);
        
        // Remove any characters that aren't letters, numbers, or underscores
        $code = preg_replace('/[^a-z0-9_]/', '', $code);
        
        // Clean up multiple underscores
        $code = preg_replace('/_+/', '_', $code);
        
        // Remove leading/trailing underscores
        $code = trim($code, '_');
        
        // Add prefix if provided
        if ($prefix && !empty($code)) {
            $code = $prefix . '_' . $code;
        }
        
        // Ensure we have something (fallback)
        if (empty($code)) {
            $code = ($prefix ?: 'item') . '_' . time();
        }
        
        return $code;
    }

    /**
     * Sanitize array of data
     * 
     * @param array $data Input data
     * @param array $fieldTypes Field type mappings
     * @return array Sanitized data
     */
    public static function sanitizeArray(array $data, array $fieldTypes = []): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            $type = $fieldTypes[$key] ?? 'text';
            $sanitized[$key] = self::sanitizeByType($value, $type);
        }
        
        return $sanitized;
    }

    /**
     * Sanitize value by type
     * 
     * @param mixed $value Value to sanitize
     * @param string $type Type of sanitization
     * @return mixed Sanitized value
     */
    public static function sanitizeByType($value, string $type)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($type) {
            case 'key':
                return sanitize_key($value);
                
            case 'text':
                return sanitize_text_field($value);
                
            case 'textarea':
                return sanitize_textarea_field($value);
                
            case 'email':
                return sanitize_email($value);
                
            case 'url':
                return esc_url_raw($value);
                
            case 'int':
                return (int) $value;
                
            case 'float':
                return (float) $value;
                
            case 'bool':
                return (bool) $value;
                
            case 'price':
                return self::formatPrice($value);
                
            default:
                return is_string($value) ? sanitize_text_field($value) : $value;
        }
    }

    /**
     * Check if string is valid JSON
     * 
     * @param string $string String to check
     * @return bool True if valid JSON
     */
    public static function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Parse JSON or return original data
     * 
     * @param mixed $data Data to parse
     * @return mixed Parsed data or original if not JSON
     */
    public static function parseJsonOrOriginal($data)
    {
        if (!is_string($data)) {
            return $data;
        }
        
        if (self::isJson($data)) {
            return json_decode($data, true);
        }
        
        return $data;
    }

    /**
     * Build search query for multiple fields
     * 
     * @param mixed $query Query builder instance
     * @param string $searchTerm Search term
     * @param array $fields Fields to search in
     * @param string $operator SQL operator (LIKE, =, etc.)
     * @return mixed Query builder instance
     */
    public static function buildSearchQuery($query, string $searchTerm, array $fields, string $operator = 'LIKE')
    {
        if (empty($fields)) {
            return $query;
        }
        
        $searchValue = $operator === 'LIKE' ? "%{$searchTerm}%" : $searchTerm;
        $isFirst = true;
        
        foreach ($fields as $field) {
            if ($isFirst) {
                $query = $query->where($field, $operator, $searchValue);
                $isFirst = false;
            } else {
                $query = $query->orWhere($field, $operator, $searchValue);
            }
        }
        
        return $query;
    }

    /**
     * Extract and validate bulk operation data
     * 
     * @param array $data Request data
     * @return array [action, ids, errors]
     */
    public static function parseBulkOperation(array $data): array
    {
        $errors = [];
        $action = $data['action'] ?? null;
        $ids = $data['ids'] ?? [];
        
        if (!$action) {
            $errors[] = 'Action is required for bulk operations';
        }
        
        if (!is_array($ids) || empty($ids)) {
            $errors[] = 'IDs array is required for bulk operations';
        }
        
        if (!in_array($action, ['activate', 'deactivate', 'delete', 'restore'])) {
            $errors[] = 'Invalid bulk action. Allowed: activate, deactivate, delete, restore';
        }
        
        // Ensure IDs are integers
        $ids = array_map('intval', array_filter($ids, 'is_numeric'));
        
        if (empty($ids)) {
            $errors[] = 'No valid IDs provided';
        }
        
        return [$action, $ids, $errors];
    }

    /**
     * Generate timestamp for soft deletes
     * 
     * @return string Current timestamp
     */
    public static function getCurrentTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Validate file upload
     * 
     * @param array $file $_FILES array element
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Maximum file size in bytes
     * @return array [isValid, error]
     */
    public static function validateFileUpload(array $file, array $allowedTypes = [], int $maxSize = 2097152): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [false, 'File upload error: ' . $file['error']];
        }
        
        if (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
            return [false, 'File type not allowed'];
        }
        
        if ($file['size'] > $maxSize) {
            return [false, 'File size exceeds maximum allowed size'];
        }
        
        return [true, null];
    }
}