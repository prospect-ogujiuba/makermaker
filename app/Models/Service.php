<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class Service extends Model
{
    protected $resource = 'it_services';

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'base_price',
        'is_active',
        'requires_quote',
        'allows_file_upload'
    ];

    protected $rules = [
        'code' => 'required|string|max:50|unique',
        'name' => 'required|string|max:100',
        'description' => 'nullable|string',
        'category' => 'required|string|max:50',
        'base_price' => 'nullable|numeric|min:0',
        'is_active' => 'boolean',
        'requires_quote' => 'boolean',
        'allows_file_upload' => 'boolean'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_quote' => 'boolean',
        'allows_file_upload' => 'boolean'
    ];

    /**
     * Get only active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get services by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get services that require quotes
     */
    public function scopeRequiresQuote($query)
    {
        return $query->where('requires_quote', true);
    }

    /**
     * Get services that allow file uploads
     */
    public function scopeAllowsFileUpload($query)
    {
        return $query->where('allows_file_upload', true);
    }
}