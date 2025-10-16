<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;
use MakerMaker\Helpers\ServiceCatalogHelper;

class ServiceCoverage extends Model
{
    protected $resource = 'srvc_service_coverage';

    protected $fillable = [
        'service_id',
        'coverage_area_id',
        'delivery_surcharge',
        'lead_time_adjustment_days'
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
        'service',
        'coverageArea',
    ];

    /** ServiceCoverage belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceCoverage belongs to a CoverageArea */
    public function coverageArea()
    {
        return $this->belongsTo(CoverageArea::class, 'coverage_area_id');
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
 * Get all active service coverages
 */
public function getActive()
{
    return $this->where('deleted_at', 'IS', null)
        ->findAll();
}

/**
 * Get coverage for a specific service
 */
public static function getForService($serviceId)
{
    return static::new()
        ->where('service_id', $serviceId)
        ->where('deleted_at', 'IS', null)
        ->findAll();
}

/**
 * Get services available in a coverage area
 */
public static function getServicesInArea($coverageAreaId, $activeOnly = true)
{
    $query = static::new()
        ->where('coverage_area_id', $coverageAreaId)
        ->where('deleted_at', 'IS', null);
    
    $coverages = $query->findAll()->get();
    $services = [];
    
    if ($coverages) {
        foreach ($coverages as $coverage) {
            $service = $coverage->service;
            if ($service) {
                $matchesActive = !$activeOnly || ($service->is_active && $service->deleted_at === null);
                if ($matchesActive) {
                    $services[] = $service;
                }
            }
        }
    }
    
    return $services;
}

/**
 * Check if service is available in area
 */
public static function isServiceAvailableInArea($serviceId, $coverageAreaId)
{
    $coverage = static::new()
        ->where('service_id', $serviceId)
        ->where('coverage_area_id', $coverageAreaId)
        ->where('deleted_at', 'IS', null)
        ->first();
    
    return $coverage !== null;
}

/**
 * Get coverage with surcharge
 */
public static function getCoverageWithSurcharge($minSurcharge)
{
    return static::new()
        ->where('delivery_surcharge', '>=', $minSurcharge)
        ->where('deleted_at', 'IS', null)
        ->orderBy('delivery_surcharge', 'DESC')
        ->findAll();
}

/**
 * Get coverage with lead time adjustments
 */
public static function getCoverageWithLeadTimeAdjustment()
{
    return static::new()
        ->where('lead_time_adjustment_days', '!=', 0)
        ->where('deleted_at', 'IS', null)
        ->orderBy('lead_time_adjustment_days', 'DESC')
        ->findAll();
}

/**
 * Check if has delivery surcharge
 */
public function hasSurcharge()
{
    return $this->delivery_surcharge > 0;
}

/**
 * Check if has lead time adjustment
 */
public function hasLeadTimeAdjustment()
{
    return $this->lead_time_adjustment_days != 0;
}

/**
 * Get formatted surcharge
 */
public function getFormattedSurcharge($currency = 'CAD')
{
    if ($this->delivery_surcharge == 0) {
        return 'No surcharge';
    }
    
    return ServiceCatalogHelper::formatCurrency($this->delivery_surcharge, $currency);
}

/**
 * Get lead time impact description
 */
public function getLeadTimeImpact()
{
    if ($this->lead_time_adjustment_days == 0) {
        return 'Standard lead time';
    } elseif ($this->lead_time_adjustment_days > 0) {
        return "+{$this->lead_time_adjustment_days} day" . ($this->lead_time_adjustment_days != 1 ? 's' : '');
    } else {
        return "{$this->lead_time_adjustment_days} day" . ($this->lead_time_adjustment_days != -1 ? 's' : '');
    }
}

/**
 * Calculate adjusted lead time
 */
public function calculateAdjustedLeadTime($baseLeadTimeDays)
{
    return $baseLeadTimeDays + $this->lead_time_adjustment_days;
}

/**
 * Calculate total delivery cost
 */
public function calculateDeliveryCost($basePrice)
{
    return $basePrice + $this->delivery_surcharge;
}

/**
 * Get service coverage summary
 */
public function getCoverageSummary($currency = 'CAD')
{
    $service = $this->service;
    $area = $this->coverageArea;
    
    return [
        'service_name' => $service->name ?? 'Unknown',
        'coverage_area' => $area ? $area->getFullName() : 'Unknown',
        'surcharge' => $this->delivery_surcharge,
        'formatted_surcharge' => $this->getFormattedSurcharge($currency),
        'lead_time_adjustment' => $this->lead_time_adjustment_days,
        'lead_time_impact' => $this->getLeadTimeImpact(),
        'has_surcharge' => $this->hasSurcharge(),
        'has_lead_adjustment' => $this->hasLeadTimeAdjustment()
    ];
}

/**
 * Update surcharge
 */
public function updateSurcharge($newSurcharge)
{
    if ($newSurcharge < 0) {
        throw new \InvalidArgumentException("Surcharge must be non-negative");
    }
    
    $this->delivery_surcharge = $newSurcharge;
    return $this->update(['delivery_surcharge']);
}

/**
 * Update lead time adjustment
 */
public function updateLeadTimeAdjustment($days)
{
    $this->lead_time_adjustment_days = $days;
    return $this->update(['lead_time_adjustment_days']);
}

/**
 * Check if association already exists
 */
public static function exists($serviceId, $coverageAreaId)
{
    $existing = static::new()
        ->where('service_id', $serviceId)
        ->where('coverage_area_id', $coverageAreaId)
        ->where('deleted_at', 'IS', null)
        ->first();
    
    return $existing !== null;
}

/**
 * Get coverage statistics for a service
 */
public static function getCoverageStatsForService($serviceId)
{
    $coverages = static::getForService($serviceId)->get();
    
    if (!$coverages) {
        return null;
    }
    
    $stats = [
        'total_areas' => count($coverages),
        'areas_with_surcharge' => 0,
        'areas_with_lead_adjustment' => 0,
        'avg_surcharge' => 0,
        'max_surcharge' => 0,
        'avg_lead_adjustment' => 0,
        'max_lead_adjustment' => 0
    ];
    
    $surcharges = [];
    $leadAdjustments = [];
    
    foreach ($coverages as $coverage) {
        if ($coverage->delivery_surcharge > 0) {
            $stats['areas_with_surcharge']++;
            $surcharges[] = $coverage->delivery_surcharge;
        }
        
        if ($coverage->lead_time_adjustment_days != 0) {
            $stats['areas_with_lead_adjustment']++;
            $leadAdjustments[] = $coverage->lead_time_adjustment_days;
        }
    }
    
    if (!empty($surcharges)) {
        $stats['avg_surcharge'] = round(array_sum($surcharges) / count($surcharges), 2);
        $stats['max_surcharge'] = max($surcharges);
    }
    
    if (!empty($leadAdjustments)) {
        $stats['avg_lead_adjustment'] = round(array_sum($leadAdjustments) / count($leadAdjustments), 1);
        $stats['max_lead_adjustment'] = max($leadAdjustments);
    }
    
    return $stats;
}

/**
 * Get coverage statistics for an area
 */
public static function getCoverageStatsForArea($coverageAreaId)
{
    $coverages = static::new()
        ->where('coverage_area_id', $coverageAreaId)
        ->where('deleted_at', 'IS', null)
        ->findAll()
        ->get();
    
    if (!$coverages) {
        return null;
    }
    
    $stats = [
        'total_services' => count($coverages),
        'services_with_surcharge' => 0,
        'services_with_lead_adjustment' => 0,
        'avg_surcharge' => 0,
        'total_surcharge_revenue' => 0
    ];
    
    $surcharges = [];
    
    foreach ($coverages as $coverage) {
        if ($coverage->delivery_surcharge > 0) {
            $stats['services_with_surcharge']++;
            $surcharges[] = $coverage->delivery_surcharge;
            $stats['total_surcharge_revenue'] += $coverage->delivery_surcharge;
        }
        
        if ($coverage->lead_time_adjustment_days != 0) {
            $stats['services_with_lead_adjustment']++;
        }
    }
    
    if (!empty($surcharges)) {
        $stats['avg_surcharge'] = round(array_sum($surcharges) / count($surcharges), 2);
    }
    
    return $stats;
}

/**
 * Soft delete
 */
public function softDelete()
{
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
 * Get surcharge badge
 */
public function getSurchargeBadge()
{
    if ($this->delivery_surcharge == 0) {
        return '<span class="surcharge-badge none">No Surcharge</span>';
    }
    
    $formatted = $this->getFormattedSurcharge();
    return "<span class=\"surcharge-badge active\">{$formatted}</span>";
}

/**
 * Get lead time badge
 */
public function getLeadTimeBadge()
{
    if ($this->lead_time_adjustment_days == 0) {
        return '<span class="leadtime-badge standard">Standard</span>';
    } elseif ($this->lead_time_adjustment_days > 0) {
        return "<span class=\"leadtime-badge delayed\">{$this->getLeadTimeImpact()}</span>";
    } else {
        return "<span class=\"leadtime-badge expedited\">{$this->getLeadTimeImpact()}</span>";
    }
}
}
