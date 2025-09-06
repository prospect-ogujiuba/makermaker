<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

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
        'enum_options' => 'json'
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
        return $this->belongsTo(\TypeRocket\Models\WPUser::class, 'created_by');
    }

    /** Updated by WP user */
    public function updatedBy()
    {
        return $this->belongsTo(\TypeRocket\Models\WPUser::class, 'updated_by');
    }
}
