<?php

namespace MakerMaker\Models\Traits;

/**
 * HasCodeGeneration Trait
 * 
 * Provides automatic code generation functionality for models
 * that need unique string identifiers based on name fields.
 */
trait HasCodeGeneration
{
    /**
     * Generate a unique code from the name
     * Converts name to lowercase, underscore-separated format
     * Ensures uniqueness by appending numbers if needed
     * 
     * @param string $name The name to convert to code
     * @param int|null $excludeId ID to exclude from uniqueness check (for updates)
     * @return string Generated unique code
     */
    public static function generateCodeFromName(string $name, ?int $excludeId = null): string
    {
        // Clean and convert name to code format
        $baseCode = static::nameToCode($name);
        
        // Check if code already exists
        $code = $baseCode;
        $counter = 1;
        
        while (static::codeExists($code, $excludeId)) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }
        
        return $code;
    }

    /**
     * Convert a name string to code format
     * Example: "Web Development Services" -> "web_development_services"
     * 
     * @param string $name The name to convert
     * @return string Formatted code
     */
    public static function nameToCode(string $name): string
    {
        // Remove special characters and convert to lowercase
        $code = strtolower($name);
        
        // Replace spaces and common separators with underscores
        $code = preg_replace('/[\s\-\.]+/', '_', $code);
        
        // Remove any characters that aren't letters, numbers, or underscores
        $code = preg_replace('/[^a-z0-9_]/', '', $code);
        
        // Clean up multiple underscores
        $code = preg_replace('/_+/', '_', $code);
        
        // Remove leading/trailing underscores
        $code = trim($code, '_');
        
        // Ensure we have something (fallback)
        if (empty($code)) {
            $code = strtolower(class_basename(static::class)) . '_' . time();
        }
        
        return $code;
    }

    /**
     * Check if a code already exists (excluding soft deleted by default)
     * 
     * @param string $code The code to check
     * @param int|null $excludeId ID to exclude from check (for updates)
     * @return bool True if code exists, false otherwise
     */
    public static function codeExists(string $code, ?int $excludeId = null): bool
    {
        $query = static::new()->where('code', $code);
        
        // Exclude soft deleted if the trait is being used
        if (method_exists(static::class, 'isDeleted')) {
            $query = $query->where('deleted_at', 'IS', null);
        }
        
        if ($excludeId) {
            $query = $query->where('id', '!=', $excludeId);
        }
        
        return $query->first() !== null;
    }

    /**
     * Find model by code
     * 
     * @param string $code The code to search for
     * @return static|null The model instance or null if not found
     */
    public static function findByCode(string $code)
    {
        $query = static::new()->where('code', $code);
        
        // Exclude soft deleted if the trait is being used
        if (method_exists(static::class, 'isDeleted')) {
            $query = $query->where('deleted_at', 'IS', null);
        }
        
        return $query->first();
    }

    /**
     * Auto-generate code before saving if not provided
     * Override the save method to handle auto code generation
     */
    public function save($fillable = null)
    {
        // Only auto-generate code if it's empty and we have a name
        if (empty($this->code) && !empty($this->name)) {
            $excludeId = $this->id ?? null; // Exclude current record if updating
            $this->code = static::generateCodeFromName($this->name, $excludeId);
        }
        
        return parent::save($fillable);
    }
}