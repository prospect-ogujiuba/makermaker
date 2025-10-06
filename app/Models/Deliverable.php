<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class Deliverable extends Model
{
    protected $resource = 'srvc_deliverables';

    protected $fillable = [
        'name',
        'description',
        'deliverable_type',
        'template_path',
        'estimated_effort_hours',
        'requires_approval'
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
        'services'
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_deliverables', 'deliverable_id', 'service_id');
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
     * Get all active deliverables
     * 
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getActive()
    {
        return static::new()->where('status', 'active')->get();
    }

    /**
     * Find deliverable by slug
     * 
     * @param string $slug
     * @return static|null
     */
    public static function findBySlug($slug)
    {
        if (empty($slug)) {
            return null;
        }

        return static::new()->where('slug', $slug)->first();
    }

    /**
     * Find deliverables by type
     * 
     * @param string $type
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function findByType($type)
    {
        if (empty($type)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()->where('type', $type)->get();
    }

    /**
     * Check if deliverable is digital/downloadable
     * 
     * @return bool
     */
    public function isDigital()
    {
        return !empty($this->type) && in_array(strtolower($this->type), ['digital', 'download', 'file']);
    }

    /**
     * Check if deliverable is physical
     * 
     * @return bool
     */
    public function isPhysical()
    {
        return !empty($this->type) && in_array(strtolower($this->type), ['physical', 'product', 'tangible']);
    }

    /**
     * Get formatted deliverable name with type
     * 
     * @return string
     */
    public function getFormattedName()
    {
        $name = $this->name ?? 'Unnamed Deliverable';
        $type = !empty($this->type) ? ' (' . ucfirst($this->type) . ')' : '';

        return $name . $type;
    }

    /**
     * Get all services that include this deliverable
     * 
     * @return \TypeRocket\Models\Results|Service[]
     */
    public function getServices()
    {
        $serviceDeliverables = ServiceDeliverable::new()
            ->where('deliverable_id', $this->getID())
            ->get();

        if ($serviceDeliverables->isEmpty()) {
            return Service::new()->where('1', '0')->get();
        }

        $serviceIds = array_filter(array_column($serviceDeliverables->toArray(), 'service_id'));

        if (empty($serviceIds)) {
            return Service::new()->where('1', '0')->get();
        }

        return Service::new()->where('id', 'in', $serviceIds)->get();
    }

    /**
     * Check if deliverable is required (based on default settings)
     * 
     * @return bool
     */
    public function isRequired()
    {
        return !empty($this->is_required) && (bool)$this->is_required;
    }

    /**
     * Get deliverable quantity/unit label
     * 
     * @return string
     */
    public function getUnitLabel()
    {
        return !empty($this->unit) ? $this->unit : 'item';
    }

    /**
     * Validate deliverable data
     * 
     * @return array Array of error messages (empty if valid)
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = 'Deliverable name is required';
        }

        if (empty($this->type)) {
            $errors[] = 'Deliverable type is required';
        }

        if (empty($this->slug)) {
            $errors[] = 'Deliverable slug is required';
        } elseif ($this->slug !== sanitize_title($this->slug)) {
            $errors[] = 'Deliverable slug contains invalid characters';
        }

        return $errors;
    }
}
