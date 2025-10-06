<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;
use MakerMaker\Helpers\ServiceCatalogHelper;

class Equipment extends Model
{
    protected $resource = 'srvc_equipment';

    protected $fillable = [
        'sku',
        'name',
        'manufacturer',
        'model',
        'category',
        'unit_cost',
        'is_consumable',
        'specs'
    ];

    protected $format = [
        'specs' => 'json_encode'
    ];

    protected $cast = [
        'specs' => 'array',
        'is_consumable' => 'bool',
        'unit_cost' => 'float'
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
    ];

    protected $with = [
        'services',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_equipment', 'equipment_id', 'service_id');
    }

    /** Created by WP user */
    public function createdBy()
    {
        return $this->belongsTo(WPUser::class, 'created_by');
    }

    /** Updated by WP user */
    public function updatedBy()
    {
        return $this->belongsTo(WPUser::class, 'updated_by');
    }

    /**
     * Get all active equipment
     */
    public function getActive()
    {
        return $this->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Find equipment by SKU
     */
    public function findBySKU($sku)
    {
        return $this->where('sku', $sku)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Get equipment by manufacturer
     */
    public function getByManufacturer($manufacturer)
    {
        return $this->where('manufacturer', $manufacturer)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get equipment by category
     */
    public function getByCategory($category)
    {
        return $this->where('category', $category)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get all consumable equipment
     */
    public function getConsumable()
    {
        return $this->where('is_consumable', 1)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get all non-consumable equipment
     */
    public function getNonConsumable()
    {
        return $this->where('is_consumable', 0)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Search equipment by keyword (searches name, manufacturer, model)
     */
    public function search($keyword)
    {
        return $this->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('manufacturer', 'LIKE', "%{$keyword}%")
            ->orWhere('model', 'LIKE', "%{$keyword}%")
            ->orWhere('sku', 'LIKE', "%{$keyword}%")
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get equipment within cost range
     */
    public function getInCostRange($minCost, $maxCost)
    {
        return $this->where('unit_cost', '>=', $minCost)
            ->where('unit_cost', '<=', $maxCost)
            ->where('deleted_at', 'IS', null)
            ->orderBy('unit_cost', 'ASC')
            ->findAll();
    }

    /**
     * Get equipment above cost threshold
     */
    public function getAboveCost($cost)
    {
        return $this->where('unit_cost', '>', $cost)
            ->where('deleted_at', 'IS', null)
            ->orderBy('unit_cost', 'DESC')
            ->findAll();
    }

    /**
     * Check if equipment is consumable
     */
    public function isConsumable()
    {
        return (bool) $this->is_consumable;
    }

    /**
     * Get formatted unit cost
     */
    public function getFormattedCost($currency = 'CAD')
    {
        if ($this->unit_cost === null) {
            return 'Cost not available';
        }

        return ServiceCatalogHelper::formatCurrency($this->unit_cost, $currency);
    }

    /**
     * Get equipment full name (includes manufacturer and model)
     */
    public function getFullName()
    {
        $parts = [$this->name];

        if ($this->manufacturer) {
            $parts[] = "({$this->manufacturer}";

            if ($this->model) {
                $parts[] = $this->model . ")";
            } else {
                $parts[count($parts) - 1] .= ")";
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Get specification value by key
     */
    public function getSpecValue($key, $default = null)
    {
        $specs = $this->specs;

        if (!is_array($specs)) {
            return $default;
        }

        return $specs[$key] ?? $default;
    }

    /**
     * Set specification value
     */
    public function setSpecValue($key, $value)
    {
        $specs = $this->specs ?? [];
        $specs[$key] = $value;
        $this->specs = $specs;
        return $this;
    }

    /**
     * Get services using this equipment
     */
    public function getServicesUsingEquipment($activeOnly = true)
    {
        $services = $this->services;

        if (!$services || !$activeOnly) {
            return $services;
        }

        // Filter to active only
        $filtered = [];
        foreach ($services as $service) {
            if ($service->is_active && $service->deleted_at === null) {
                $filtered[] = $service;
            }
        }

        return $filtered;
    }

    /**
     * Get count of services using this equipment
     */
    public function getServiceCount($activeOnly = true)
    {
        $services = $this->getServicesUsingEquipment($activeOnly);
        return $services ? count($services) : 0;
    }

    /**
     * Get services that require this equipment
     */
    public function getServicesRequiringEquipment()
    {
        $serviceEquipment = ServiceEquipment::new()
            ->where('equipment_id', $this->id)
            ->where('required', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll()
            ->get();

        $services = [];
        if ($serviceEquipment) {
            foreach ($serviceEquipment as $se) {
                $service = $se->service;
                if ($service && $service->is_active && $service->deleted_at === null) {
                    $services[] = $service;
                }
            }
        }

        return $services;
    }

    /**
     * Get total quantity used across all services
     */
    public function getTotalQuantityUsed()
    {
        $serviceEquipment = ServiceEquipment::new()
            ->where('equipment_id', $this->id)
            ->where('deleted_at', 'IS', null)
            ->findAll()
            ->get();

        $total = 0;
        if ($serviceEquipment) {
            foreach ($serviceEquipment as $se) {
                $total += $se->quantity;
            }
        }

        return $total;
    }

    /**
     * Calculate total cost impact across all services
     */
    public function getTotalCostImpact()
    {
        if ($this->unit_cost === null) {
            return 0;
        }

        return $this->unit_cost * $this->getTotalQuantityUsed();
    }

    /**
     * Get all unique categories in use
     */
    public static function getAllCategories()
    {
        $wpdb = static::new()->getQuery()->getWpdb();
        $table = static::new()->getTable();

        $sql = "SELECT DISTINCT category 
            FROM {$table} 
            WHERE category IS NOT NULL 
            AND deleted_at IS NULL 
            ORDER BY category";

        $results = $wpdb->get_results($sql);

        return array_column($results, 'category');
    }

    /**
     * Get all unique manufacturers
     */
    public static function getAllManufacturers()
    {
        $wpdb = static::new()->getQuery()->getWpdb();
        $table = static::new()->getTable();

        $sql = "SELECT DISTINCT manufacturer 
            FROM {$table} 
            WHERE deleted_at IS NULL 
            ORDER BY manufacturer";

        $results = $wpdb->get_results($sql);

        return array_column($results, 'manufacturer');
    }

    /**
     * Get equipment statistics
     */
    public static function getEquipmentStats()
    {
        $equipment = static::new()->getActive()->get();

        if (!$equipment) {
            return null;
        }

        $stats = [
            'total_equipment' => count($equipment),
            'consumable_count' => 0,
            'non_consumable_count' => 0,
            'avg_unit_cost' => 0,
            'total_value' => 0,
            'category_count' => 0,
            'manufacturer_count' => 0
        ];

        $costs = [];

        foreach ($equipment as $item) {
            if ($item->is_consumable) {
                $stats['consumable_count']++;
            } else {
                $stats['non_consumable_count']++;
            }

            if ($item->unit_cost !== null) {
                $costs[] = $item->unit_cost;
                $stats['total_value'] += $item->unit_cost;
            }
        }

        if (!empty($costs)) {
            $stats['avg_unit_cost'] = round(array_sum($costs) / count($costs), 2);
            $stats['min_unit_cost'] = min($costs);
            $stats['max_unit_cost'] = max($costs);
        }

        $stats['category_count'] = count(static::getAllCategories());
        $stats['manufacturer_count'] = count(static::getAllManufacturers());

        return $stats;
    }

    /**
     * Check if equipment is in use
     */
    public function isInUse()
    {
        return $this->getServiceCount(false) > 0;
    }

    /**
     * Toggle consumable status
     */
    public function toggleConsumable()
    {
        $this->is_consumable = !$this->is_consumable;
        return $this->update(['is_consumable']);
    }

    /**
     * Update unit cost
     */
    public function updateCost($newCost)
    {
        if ($newCost < 0) {
            throw new \InvalidArgumentException("Unit cost must be non-negative");
        }

        $this->unit_cost = $newCost;
        return $this->update(['unit_cost']);
    }

    /**
     * Soft delete (with validation)
     */
    public function softDelete()
    {
        if ($this->isInUse()) {
            throw new \RuntimeException(
                "Cannot delete equipment '{$this->name}' because it is used by {$this->getServiceCount(false)} service(s)"
            );
        }

        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->update(['deleted_at']);
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
     * Get equipment display badge
     */
    public function getEquipmentBadge()
    {
        $type = $this->is_consumable ? 'consumable' : 'durable';
        $class = $this->is_consumable ? 'equipment-badge consumable' : 'equipment-badge durable';

        return sprintf(
            '<span class="%s">%s - %s</span>',
            $class,
            $this->name,
            ucfirst($type)
        );
    }

    /**
     * Get select options for dropdowns
     */
    public static function getSelectOptions($includeEmpty = true, $groupByCategory = false)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Equipment —';
        }

        $equipment = static::new()->getActive()->get();

        if (!$equipment) {
            return $options;
        }

        if ($groupByCategory) {
            $grouped = [];
            foreach ($equipment as $item) {
                $category = $item->category ?? 'Uncategorized';
                if (!isset($grouped[$category])) {
                    $grouped[$category] = [];
                }
                $grouped[$category][$item->id] = $item->getFullName();
            }

            foreach ($grouped as $category => $items) {
                $options[$category] = $items;
            }
        } else {
            foreach ($equipment as $item) {
                $options[$item->id] = $item->getFullName();
            }
        }

        return $options;
    }
}
