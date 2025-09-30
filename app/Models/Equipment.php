<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

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

    public function services()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_equipment_assignments', 'equipment_id', 'service_id');
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
}
