<?php
namespace MakerMaker\Models;

use TypeRocket\Models\Model;

/**
 * ServiceComplexity Model
 * 
 * Represents service complexity levels lookup table
 * Table: wp_b2bcnc_service_complexity
 */
class ServiceComplexity extends Model
{
    protected $resource = 'b2bcnc_service_complexity';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean'
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Services using this complexity level
     * wp_b2bcnc_services.complexity_level_id -> wp_b2bcnc_service_complexity.id
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'complexity_level_id');
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Get only active complexity levels
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Order by sort order and name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC');
    }

    /**
     * Get by slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // ==========================================
    // UTILITY METHODS
    // ==========================================

    /**
     * Check if complexity level is in use by any services
     */
    public function isInUse()
    {
        return $this->services()->count() > 0;
    }

    /**
     * Get services count using this complexity level
     */
    public function getServicesCount()
    {
        return $this->services()->count();
    }

    /**
     * Get active services count
     */
    public function getActiveServicesCount()
    {
        return $this->services()->active()->count();
    }

    /**
     * Get display name with services count
     */
    public function getDisplayNameWithCount()
    {
        $count = $this->getServicesCount();
        return $this->name . ' (' . $count . ' service' . ($count !== 1 ? 's' : '') . ')';
    }

    /**
     * Generate slug from name if not provided
     */
    public function generateSlug()
    {
        if (!empty($this->slug)) {
            return $this->slug;
        }

        $slug = strtolower(trim($this->name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }

    /**
     * Check if this complexity level can be deleted safely
     */
    public function canBeDeleted()
    {
        // Cannot delete if it's being used by services
        if ($this->isInUse()) {
            return false;
        }

        // Cannot delete if it's one of the core complexity levels
        $coreComplexities = ['basic', 'intermediate', 'advanced', 'expert'];
        if (in_array($this->slug, $coreComplexities)) {
            return false;
        }

        return true;
    }

    /**
     * Get the next sort order value
     */
    public static function getNextSortOrder()
    {
        $maxOrder = static::max('sort_order');
        return ($maxOrder ? $maxOrder + 1 : 1);
    }

    /**
     * Auto-generate slug before saving if not provided
     */
    protected function beforeSave()
    {
        if (empty($this->slug)) {
            $this->slug = $this->generateSlug();
        }
        
        if (empty($this->sort_order)) {
            $this->sort_order = static::getNextSortOrder();
        }
        
        parent::beforeSave();
    }
}