<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceCategory extends Model
{
    protected $resource = 'srvc_categories';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
        'is_active',
    ];


    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $format = [
        'parent_id' => 'convertEmptyToNull'
    ];

    protected $with = [
        'services',
        'parentCategory',
        'childCategories',
    ];

    /** ServiceCategory has many Services */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    /** ServiceCategory belongs to parent ServiceCategory */
    public function parentCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    /** ServiceCategory has many child ServiceCategories */
    public function childCategories()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
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
     * Get all active categories
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Get all root categories (no parent)
     */
    public function getRootCategories()
    {
        return $this->where('parent_id', 'IS', null)
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Get subcategories of a parent category
     */
    public function getSubcategories($parentId = null)
    {
        if ($parentId === null && $this->id) {
            $parentId = $this->id;
        }

        return static::new()
            ->where('parent_id', $parentId)
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Find category by slug
     */
    public function findBySlug($slug)
    {
        return $this->where('slug', $slug)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Check if this category has children
     */
    public function hasChildren()
    {
        $children = $this->childCategories;
        return $children && count($children) > 0;
    }

    /**
     * Check if this category is a root category
     */
    public function isRoot()
    {
        return $this->parent_id === null;
    }

    /**
     * Get category depth level (0 = root, 1 = first level child, etc.)
     */
    public function getDepth()
    {
        $depth = 0;
        $current = $this;

        while ($current->parent_id !== null) {
            $depth++;
            $current = $current->parentCategory;

            if (!$current || $depth > 10) { // Safety limit
                break;
            }
        }

        return $depth;
    }

    /**
     * Get full category path (e.g., "Networking > Cabling > Fiber")
     */
    public function getCategoryPath($separator = ' > ')
    {
        $path = [$this->name];
        $current = $this;

        while ($current->parent_id !== null) {
            $current = $current->parentCategory;
            if (!$current) {
                break;
            }
            array_unshift($path, $current->name);
        }

        return implode($separator, $path);
    }

    /**
     * Get all ancestor categories
     */
    public function getAncestors()
    {
        $ancestors = [];
        $current = $this;

        while ($current->parent_id !== null) {
            $current = $current->parentCategory;
            if (!$current) {
                break;
            }
            array_unshift($ancestors, $current);
        }

        return $ancestors;
    }

    /**
     * Get all descendant categories (recursive)
     */
    public function getDescendants()
    {
        $descendants = [];
        $children = $this->getSubcategories();

        if ($children) {
            foreach ($children->get() as $child) {
                $descendants[] = $child;
                $childDescendants = $child->getDescendants();
                if ($childDescendants) {
                    $descendants = array_merge($descendants, $childDescendants);
                }
            }
        }

        return $descendants;
    }

    /**
     * Get category tree as hierarchical array
     */
    public function getCategoryTree()
    {
        $tree = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'description' => $this->description,
            'sort_order' => $this->sort_order,
            'children' => []
        ];

        $children = $this->getSubcategories();
        if ($children) {
            foreach ($children->get() as $child) {
                $tree['children'][] = $child->getCategoryTree();
            }
        }

        return $tree;
    }

    /**
     * Get all categories as flat tree structure (for select dropdowns)
     */
    public static function getFlatTree($prefix = '— ', $parentId = null, $depth = 0)
    {
        $categories = static::new()
            ->where('parent_id', $parentId === null ? 'IS' : '=', $parentId)
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll()
            ->get();

        $result = [];

        if ($categories) {
            foreach ($categories as $category) {
                $result[] = [
                    'id' => $category->id,
                    'name' => str_repeat($prefix, $depth) . $category->name,
                    'slug' => $category->slug,
                    'depth' => $depth
                ];

                // Recursively get children
                $children = static::getFlatTree($prefix, $category->id, $depth + 1);
                if ($children) {
                    $result = array_merge($result, $children);
                }
            }
        }

        return $result;
    }

    /**
     * Count services in this category (direct children only)
     */
    public function getServiceCount($activeOnly = true)
    {
        $services = $this->services;

        if (!$services) {
            return 0;
        }

        if ($activeOnly) {
            $count = 0;
            foreach ($services as $service) {
                if ($service->is_active && $service->deleted_at === null) {
                    $count++;
                }
            }
            return $count;
        }

        return count($services);
    }

    /**
     * Count services in this category and all descendants
     */
    public function getTotalServiceCount($activeOnly = true)
    {
        $count = $this->getServiceCount($activeOnly);

        $descendants = $this->getDescendants();
        foreach ($descendants as $descendant) {
            $count += $descendant->getServiceCount($activeOnly);
        }

        return $count;
    }

    /**
     * Move category to new parent
     */
    public function moveTo($newParentId)
    {
        // Prevent moving to self or descendant
        if ($newParentId == $this->id) {
            throw new \InvalidArgumentException("Cannot move category to itself");
        }

        if ($newParentId !== null) {
            $descendants = $this->getDescendants();
            foreach ($descendants as $descendant) {
                if ($descendant->id == $newParentId) {
                    throw new \InvalidArgumentException("Cannot move category to its own descendant");
                }
            }
        }

        $this->parent_id = $newParentId;
        return $this->update(['parent_id']);
    }

    /**
     * Update sort order
     */
    public function updateSortOrder($newOrder)
    {
        $this->sort_order = $newOrder;
        return $this->update(['sort_order']);
    }

    /**
     * Reorder siblings (categories with same parent)
     */
    public static function reorderSiblings($categoryIds, $parentId = null)
    {
        $order = 0;
        foreach ($categoryIds as $categoryId) {
            $category = static::new()->findById($categoryId);
            if ($category && $category->parent_id == $parentId) {
                $category->updateSortOrder($order);
                $order++;
            }
        }

        return true;
    }

    /**
     * Toggle active status
     */
    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this->update(['is_active']);
    }

    /**
     * Soft delete (and optionally cascade to children)
     */
    public function softDelete($cascade = false)
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        $result = $this->update(['deleted_at']);

        if ($cascade) {
            $children = static::new()
                ->where('parent_id', $this->id)
                ->findAll()
                ->get();

            if ($children) {
                foreach ($children as $child) {
                    $child->softDelete(true);
                }
            }
        }

        return $result;
    }

    /**
     * Restore from soft delete
     */
    public function restore()
    {
        $this->deleted_at = null;
        return $this->update(['deleted_at']);
    }

    /**
     * Get icon HTML (for dashicons or custom icons)
     */
    public function getIconHtml($attributes = '')
    {
        if (!$this->icon) {
            return '';
        }

        // Check if it's a dashicon
        if (strpos($this->icon, 'dashicons-') === 0) {
            return sprintf(
                '<span class="dashicons %s" %s></span>',
                esc_attr($this->icon),
                $attributes
            );
        }

        // Otherwise treat as custom HTML/icon
        return $this->icon;
    }

    /**
     * Search categories by name or description
     */
    public static function search($keyword)
    {
        return static::new()
            ->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('description', 'LIKE', "%{$keyword}%")
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }
}
