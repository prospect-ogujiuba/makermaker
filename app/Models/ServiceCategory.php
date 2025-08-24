<?php
namespace MakerMaker\Models;

use TypeRocket\Models\Model;

/**
 * ServiceCategory Model
 * 
 * Represents service categories with hierarchical (parent/child) structure
 * Table: wp_b2bcnc_service_categories
 */
class ServiceCategory extends Model
{
    protected $resource = 'b2bcnc_service_categories';
    
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean'
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Services belonging to this category
     * wp_b2bcnc_services.category_id -> wp_b2bcnc_service_categories.id
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    /**
     * Parent category (hierarchical)
     * wp_b2bcnc_service_categories.parent_id -> wp_b2bcnc_service_categories.id
     */
    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    /**
     * Child categories (hierarchical)
     * wp_b2bcnc_service_categories.parent_id -> wp_b2bcnc_service_categories.id
     */
    public function children()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Get only active categories
     */
    public function active()
    {
        return $this->where('is_active', 1);
    }

    /**
     * Get only top-level categories (no parent)
     */
    public function topLevel()
    {
        return $this->whereNull('parent_id');
    }

    /**
     * Order by sort order and name
     */
    public function ordered()
    {
        return $this->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC');
    }

    // ==========================================
    // UTILITY METHODS
    // ==========================================

    /**
     * Get full hierarchical name (Parent > Child)
     */
    public function getFullName()
    {
        if ($this->parent) {
            return $this->parent->getFullName() . ' > ' . $this->name;
        }
        
        return $this->name;
    }

    /**
     * Check if category has children
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Check if category has services
     */
    public function hasServices()
    {
        return $this->services()->count() > 0;
    }

    /**
     * Get all descendant categories (children, grandchildren, etc.)
     */
    public function getDescendants()
    {
        $descendants = [];
        
        return $descendants;
    }

    /**
     * Get hierarchy level (0 = top level, 1 = child, 2 = grandchild, etc.)
     */
    public function getLevel()
    {
        $level = 0;
        $current = $this;
        
        while ($current->parent) {
            $level++;
            $current = $current->parent;
        }
        
        return $level;
    }

    /**
     * Generate unique slug based on name and parent
     */
    public function generateSlug()
    {
        $baseSlug = sanitize_title($this->name);
        $slug = $baseSlug;
        $counter = 1;

        // Check for existing slugs
        while (ServiceCategory::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    // ==========================================
    // MODEL EVENTS
    // ==========================================

    /**
     * Boot model - set up model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug before saving if not provided
        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = $category->generateSlug();
            }
        });

        // Prevent deletion if category has services or children
        static::deleting(function ($category) {
            if ($category->hasServices()) {
                throw new \Exception('Cannot delete category that contains services. Please reassign services first.');
            }
            
            if ($category->hasChildren()) {
                throw new \Exception('Cannot delete category that has subcategories. Please reassign or delete subcategories first.');
            }
        });
    }

    // ==========================================
    // STATIC HELPER METHODS
    // ==========================================

    /**
     * Get hierarchical list for select dropdowns
     * Returns array with full hierarchy names as labels
     */
    public static function getHierarchicalOptions()
    {
        $options = [];
        return $options;
    }

    /**
     * Build tree structure for nested display
     */
    // public static function buildTree($parentId = null)
    // {
    //     return static::where('parent_id', $parentId)
    //         ->active()
    //         ->ordered()
    //         ->with(['children' => function ($query) {
    //             $query->active()->ordered();
    //         }])
    //         ->get();
    // }

    /**
     * Get breadcrumb array for a category
     */
    public function getBreadcrumb()
    {
        $breadcrumb = [];
        $current = $this;
        
        while ($current) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug
            ]);
            $current = $current->parent;
        }
        
        return $breadcrumb;
    }
}