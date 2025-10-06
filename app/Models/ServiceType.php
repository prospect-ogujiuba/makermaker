<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceType extends Model
{
    protected $resource = 'srvc_service_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'requires_site_visit',
        'supports_remote',
        'estimated_duration_hours'
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
        'services'
    ];

    /** ServiceType has many Services */
    public function services()
    {
        return $this->hasMany(Service::class, 'service_type_id');
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
     * Get all active service types
     */
    public function getActive()
    {
        return $this->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Find service type by code
     */
    public function findByCode($code)
    {
        return $this->where('code', $code)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Get service types that require site visits
     */
    public function getRequiringSiteVisit()
    {
        return $this->where('requires_site_visit', 1)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get service types that support remote delivery
     */
    public function getSupportingRemote()
    {
        return $this->where('supports_remote', 1)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get service types by delivery capability
     */
    public function getByDeliveryCapability($requiresSiteVisit = null, $supportsRemote = null)
    {
        $query = $this->where('deleted_at', 'IS', null);

        if ($requiresSiteVisit !== null) {
            $query->where('requires_site_visit', $requiresSiteVisit ? 1 : 0);
        }

        if ($supportsRemote !== null) {
            $query->where('supports_remote', $supportsRemote ? 1 : 0);
        }

        return $query->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Check if service type can be delivered remotely
     */
    public function canDeliverRemotely()
    {
        return (bool) $this->supports_remote;
    }

    /**
     * Check if service type requires on-site visit
     */
    public function requiresOnSite()
    {
        return (bool) $this->requires_site_visit;
    }

    /**
     * Get service type delivery mode as string
     */
    public function getDeliveryMode()
    {
        if ($this->requires_site_visit && $this->supports_remote) {
            return 'hybrid';
        } elseif ($this->requires_site_visit) {
            return 'on-site';
        } elseif ($this->supports_remote) {
            return 'remote';
        }

        return 'unknown';
    }

    /**
     * Get estimated duration in hours
     */
    public function getEstimatedDuration()
    {
        return $this->estimated_duration_hours ?? 0;
    }

    /**
     * Format estimated duration as human-readable string
     */
    public function getFormattedDuration()
    {
        $hours = $this->estimated_duration_hours;

        if ($hours === null) {
            return 'Not specified';
        }

        if ($hours < 1) {
            $minutes = round($hours * 60);
            return "{$minutes} minutes";
        }

        if ($hours == 1) {
            return '1 hour';
        }

        if ($hours < 8) {
            return number_format($hours, 1) . ' hours';
        }

        $days = floor($hours / 8);
        $remainingHours = $hours % 8;

        $result = "{$days} day" . ($days > 1 ? 's' : '');

        if ($remainingHours > 0) {
            $result .= " and " . number_format($remainingHours, 1) . " hours";
        }

        return $result;
    }

    /**
     * Count services using this service type
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
     * Get services for this type
     */
    public function getServices($activeOnly = true)
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
     * Check if service type has any services
     */
    public function hasServices()
    {
        return $this->getServiceCount(false) > 0;
    }

    /**
     * Get average pricing for services of this type
     */
    public function getAveragePricing($currency = 'CAD')
    {
        $services = $this->getServices(true);

        if (!$services) {
            return null;
        }

        $totalAmount = 0;
        $count = 0;

        foreach ($services as $service) {
            $prices = $service->prices;
            if ($prices) {
                foreach ($prices as $price) {
                    if (
                        $price->currency === $currency &&
                        $price->is_current == 1 &&
                        $price->approval_status === 'approved'
                    ) {
                        $totalAmount += $price->amount;
                        $count++;
                        break;
                    }
                }
            }
        }

        return $count > 0 ? round($totalAmount / $count, 2) : null;
    }

    /**
     * Soft delete
     */
    public function softDelete()
    {
        // Check if there are active services using this type
        if ($this->getServiceCount(true) > 0) {
            throw new \RuntimeException(
                "Cannot delete service type '{$this->name}' because it has active services"
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
     * Search service types by name or description
     */
    public static function search($keyword)
    {
        return static::new()
            ->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('description', 'LIKE', "%{$keyword}%")
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Update delivery capabilities
     */
    public function updateDeliveryCapabilities($requiresSiteVisit, $supportsRemote)
    {
        $this->requires_site_visit = $requiresSiteVisit ? 1 : 0;
        $this->supports_remote = $supportsRemote ? 1 : 0;

        return $this->update(['requires_site_visit', 'supports_remote']);
    }

    /**
     * Update estimated duration
     */
    public function updateEstimatedDuration($hours)
    {
        if ($hours < 0) {
            throw new \InvalidArgumentException("Duration must be positive or zero");
        }

        $this->estimated_duration_hours = $hours;
        return $this->update(['estimated_duration_hours']);
    }
}
