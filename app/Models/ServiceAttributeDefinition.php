<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceAttributeDefinition extends Model
{
    protected $resource = 'srvc_attribute_definitions';

    protected $fillable = [
        'service_type_id',
        'code',
        'label',
        'data_type',
        'enum_options',
        'unit',
        'required',
        'created_by',
        'updated_by'
    ];

    protected $format = [
        'enum_options' => 'json_encode'
    ];
    protected $cast = [
        'enum_options' => 'array'
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /** Services using this attribute definition */
    public function services()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_attribute_values', 'attribute_definition_id', 'service_id');
    }

    /** ServiceAttributeDefinition belongs to a ServiceType */
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    /** ServiceAttributeDefinition has many ServiceAttributeValues */
    public function attributeValues()
    {
        return $this->hasMany(ServiceAttributeValue::class, 'attribute_definition_id');
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
