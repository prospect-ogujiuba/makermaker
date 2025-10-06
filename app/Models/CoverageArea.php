<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class CoverageArea extends Model
{
    protected $resource = 'srvc_coverage_areas';

    protected $fillable = [
        'name',
        'code',
        'country_code',
        'region_type',
        'timezone',
        'postal_code_pattern'
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $with = [
        'serviceCoverages.service'
    ];


    public function serviceCoverages()
    {
        return $this->hasMany(ServiceCoverage::class, 'coverage_area_id');
    }

    // User who created this record
    public function createdBy()
    {
        return $this->belongsTo(WPUser::class, 'created_by');
    }

    // User who last updated this record
    public function updatedBy()
    {
        return $this->belongsTo(WPUser::class, 'updated_by');
    }

    /**
     * Get all active coverage areas
     */
    public function getActive()
    {
        return $this->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Find coverage area by code
     */
    public function findByCode($code)
    {
        return $this->where('code', $code)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Get coverage areas by country
     */
    public function getByCountry($countryCode)
    {
        return $this->where('country_code', $countryCode)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get coverage areas by region type
     */
    public function getByRegionType($regionType)
    {
        $validTypes = ['city', 'province', 'state', 'region', 'postal_code', 'country'];

        if (!in_array($regionType, $validTypes)) {
            throw new \InvalidArgumentException("Invalid region type: {$regionType}");
        }

        return $this->where('region_type', $regionType)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get coverage areas by timezone
     */
    public function getByTimezone($timezone)
    {
        return $this->where('timezone', $timezone)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Search coverage areas
     */
    public function search($keyword)
    {
        return $this->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('code', 'LIKE', "%{$keyword}%")
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Check if postal code matches pattern
     */
    public function matchesPostalCode($postalCode)
    {
        if (!$this->postal_code_pattern) {
            return false;
        }

        return preg_match('/' . $this->postal_code_pattern . '/i', $postalCode) === 1;
    }

    /**
     * Get services available in this coverage area
     */
    public function getAvailableServices($activeOnly = true)
    {
        $coverages = $this->serviceCoverages;
        $services = [];

        if (!$coverages) {
            return [];
        }

        foreach ($coverages as $coverage) {
            $service = $coverage->service;

            if ($service) {
                $matchesActive = !$activeOnly || ($service->is_active && $service->deleted_at === null);

                if ($matchesActive) {
                    $services[] = $service;
                }
            }
        }

        return $services;
    }

    /**
     * Get service count for this coverage area
     */
    public function getServiceCount($activeOnly = true)
    {
        $services = $this->getAvailableServices($activeOnly);
        return count($services);
    }

    /**
     * Check if service is available in this area
     */
    public function hasService($serviceId)
    {
        $coverages = $this->serviceCoverages;

        if (!$coverages) {
            return false;
        }

        foreach ($coverages as $coverage) {
            if ($coverage->service_id == $serviceId && $coverage->deleted_at === null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get coverage details for a specific service
     */
    public function getCoverageForService($serviceId)
    {
        return ServiceCoverage::new()
            ->where('coverage_area_id', $this->id)
            ->where('service_id', $serviceId)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Get all unique country codes
     */
    public static function getAllCountries()
    {
        $wpdb = static::new()->getQuery()->getWpdb();
        $table = static::new()->getTable();

        $sql = "SELECT DISTINCT country_code 
            FROM {$table} 
            WHERE deleted_at IS NULL 
            ORDER BY country_code";

        $results = $wpdb->get_results($sql);

        return array_column($results, 'country_code');
    }

    /**
     * Get all unique timezones
     */
    public static function getAllTimezones()
    {
        $wpdb = static::new()->getQuery()->getWpdb();
        $table = static::new()->getTable();

        $sql = "SELECT DISTINCT timezone 
            FROM {$table} 
            WHERE timezone IS NOT NULL 
            AND deleted_at IS NULL 
            ORDER BY timezone";

        $results = $wpdb->get_results($sql);

        return array_column($results, 'timezone');
    }

    /**
     * Get full display name with country
     */
    public function getFullName()
    {
        return "{$this->name}, {$this->country_code}";
    }

    /**
     * Validate coverage area data
     */
    public function validateCoverageArea()
    {
        $errors = [];

        if (!preg_match('/^[A-Z]{2}$/', $this->country_code)) {
            $errors[] = "Country code must be 2 uppercase letters (ISO 3166-1 alpha-2)";
        }

        // Check unique code
        $existing = static::new()
            ->where('code', $this->code)
            ->where('id', '!=', $this->id ?? 0)
            ->where('deleted_at', 'IS', null)
            ->first();

        if ($existing) {
            $errors[] = "Coverage area code '{$this->code}' is already in use";
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Check if coverage area is in use
     */
    public function isInUse()
    {
        return $this->getServiceCount(false) > 0;
    }

    /**
     * Soft delete (with validation)
     */
    public function softDelete()
    {
        if ($this->isInUse()) {
            throw new \RuntimeException(
                "Cannot delete coverage area '{$this->name}' because it is used by {$this->getServiceCount(false)} service(s)"
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
     * Get select options for dropdowns
     */
    public static function getSelectOptions($includeEmpty = true, $groupByCountry = false)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Coverage Area —';
        }

        $areas = static::new()->getActive()->get();

        if (!$areas) {
            return $options;
        }

        if ($groupByCountry) {
            $grouped = [];
            foreach ($areas as $area) {
                $country = $area->country_code;
                if (!isset($grouped[$country])) {
                    $grouped[$country] = [];
                }
                $grouped[$country][$area->id] = $area->name;
            }

            foreach ($grouped as $country => $items) {
                $options[$country] = $items;
            }
        } else {
            foreach ($areas as $area) {
                $options[$area->id] = $area->getFullName();
            }
        }

        return $options;
    }

    /**
     * Get coverage area statistics
     */
    public static function getCoverageStats()
    {
        $areas = static::new()->getActive()->get();

        if (!$areas) {
            return null;
        }

        $stats = [
            'total_areas' => count($areas),
            'country_count' => 0,
            'region_types' => [],
            'timezone_count' => 0,
            'areas_with_services' => 0
        ];

        foreach ($areas as $area) {
            if ($area->getServiceCount(true) > 0) {
                $stats['areas_with_services']++;
            }

            if ($area->region_type && !in_array($area->region_type, $stats['region_types'])) {
                $stats['region_types'][] = $area->region_type;
            }
        }

        $stats['country_count'] = count(static::getAllCountries());
        $stats['timezone_count'] = count(static::getAllTimezones());
        $stats['region_type_count'] = count($stats['region_types']);

        return $stats;
    }

    /**
     * Get region type display label
     */
    public function getRegionTypeLabel()
    {
        $labels = [
            'city' => 'City',
            'province' => 'Province',
            'state' => 'State',
            'region' => 'Region',
            'postal_code' => 'Postal Code Area',
            'country' => 'Country'
        ];

        return $labels[$this->region_type] ?? ucfirst($this->region_type);
    }
}
