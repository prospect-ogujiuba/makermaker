<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class Service extends Model
{
    protected $resource = 'b2bcnc_services';

    protected $fillable = [
        'code',
        'name',
        'description',
        'base_price',
        'icon',
        'active',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at', // System managed - don't allow mass assignment
    ];

    /**
     * Data type casting for model properties
     */
    protected $cast = [
        'id' => 'int',
        'base_price' => 'float',
        'active' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Format data before saving
     */
    protected $format = [
        'code' => 'sanitize_key', // WordPress function for sanitizing keys
        'base_price' => 'static::formatPrice',
    ];

    /**
     * Format price to ensure proper decimal places
     */
    public static function formatPrice($value)
    {
        return number_format((float)$value, 2, '.', '');
    }

    // ================================
    // SOFT DELETE METHODS
    // ================================

    /**
     * Soft delete the service
     * Sets deleted_at timestamp instead of removing from database
     */
    public function softDelete()
    {
        if ($this->isDeleted()) {
            return $this; // Already deleted
        }

        $this->deleted_at = $this->getDateTime();
        $this->save(['deleted_at' => $this->deleted_at]);
        $this->save(['active' => '0']);

        return $this;
    }

    /**
     * Restore a soft deleted service
     * Sets deleted_at to null
     */
    public function restore()
    {
        if (!$this->isDeleted()) {
            return $this; // Not deleted
        }

        $this->deleted_at = null;
        $this->save(['deleted_at' => null]);

        return $this;
    }

    /**
     * Permanently delete the service from database
     * This cannot be undone
     */
    public function forceDelete()
    {
        return parent::delete();
    }

    /**
     * Check if service is soft deleted
     */
    public function isDeleted(): bool
    {
        return !is_null($this->deleted_at);
    }

    /**
     * Override default delete to use soft delete
     */
    public function delete($ids = null)
    {
        // If called on instance, soft delete this record
        if (is_null($ids) && $this->hasProperties()) {
            return $this->softDelete();
        }

        // If called with IDs, soft delete those records
        if (!is_null($ids)) {
            if (is_array($ids)) {
                // Bulk soft delete
                $updated = 0;
                foreach ($ids as $id) {
                    $service = static::new()->findById($id);
                    if ($service && !$service->isDeleted()) {
                        $service->softDelete();
                        $updated++;
                    }
                }
                return $updated;
            } else {
                // Single soft delete by ID
                $service = static::new()->findById($ids);
                if ($service && !$service->isDeleted()) {
                    return $service->softDelete();
                }
            }
        }

        return $this;
    }

    // ================================
    // QUERY SCOPES
    // ================================

    /**
     * Scope to exclude soft deleted services (default behavior)
     */
    public function scopeWhereNotDeleted($query)
    {
        return $query->where('deleted_at', 'IS', null);
    }

    /**
     * Scope to include soft deleted services
     */
    public function scopeWithDeleted($query)
    {
        // Don't add any where clause - return all records
        return $query;
    }

    /**
     * Scope to get only soft deleted services
     */
    public function scopeOnlyDeleted($query)
    {
        return $query->where('deleted_at', 'IS NOT', null);
    }

    /**
     * Scope to get only active services
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1)->where('deleted_at', 'IS', null);
    }

    /**
     * Scope to get only inactive services
     */
    public function scopeInactive($query)
    {
        return $query->where('active', 0)->where('deleted_at', 'IS', null);
    }

    

    // ================================
    // HELPER METHODS
    // ================================

    /**
     * Get formatted price for display
     */
    public function getFormattedPriceProperty()
    {
        if ($this->base_price <= 0) {
            return 'Quote Required';
        }
        return '$' . number_format($this->base_price, 2);
    }

    /**
     * Get status display
     */
    public function getStatusProperty()
    {
        if ($this->isDeleted()) {
            return 'Deleted';
        }
        return $this->active ? 'Active' : 'Inactive';
    }

    /**
     * Check if service is available for new requests
     */
    public function isAvailable(): bool
    {
        return $this->active && !$this->isDeleted();
    }

    // ================================
    // STATIC FINDER METHODS
    // ================================

    /**
     * Find service by code (excluding deleted)
     */
    public static function findByCode(string $code)
    {
        return static::new()->where('code', $code)->where('deleted_at', 'IS', null)->first();
    }

    /**
     * Find service by code including deleted
     */
    public static function findByCodeWithDeleted(string $code)
    {
        return static::new()->where('code', $code)->first();
    }

    /**
     * Get all active services
     */
    public static function getActive()
    {
        return static::new()->where('active', 1)->where('deleted_at', 'IS', null)->get();
    }

    /**
     * Get all inactive services (but not deleted)
     */
    public static function getInactive()
    {
        return static::new()->where('active', 0)->where('deleted_at', 'IS', null)->get();
    }

    /**
     * Get all deleted services
     */
    public static function getDeleted()
    {
        return static::new()->where('deleted_at', 'IS NOT', null)->get();
    }
}