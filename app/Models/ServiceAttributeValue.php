<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceAttributeValue extends Model
{
    protected $resource = 'srvc_service_attribute_values';

    protected $fillable = [
        'service_id',
        'attribute_definition_id',
        'int_val',
        'bool_val',
        'text_val',
        'enum_val',
        'created_by',
        'updated_by',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /** ServiceAttributeValue belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceAttributeValue belongs to a ServiceAttributeDefinition */
    public function attributeDefinition()
    {
        return $this->belongsTo(ServiceAttributeDefinition::class, 'attribute_definition_id');
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
