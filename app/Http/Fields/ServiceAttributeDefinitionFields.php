<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServiceAttributeDefinitionFields extends Fields
{
    /**
     * Run On Import
     *
     * Validate and then redirect on failure with errors, immediately
     * when imported by the application container resolver.
     *
     * @var bool
     */
    protected $run = true;

    /**
     * Model Fillable Property Override
     *
     * @return array
     */
    protected function fillable()
    {
        return [];
    }

    /**
     * Validation Rules
     *
     * @return array
     */
    protected function rules()
    {
        $request = Request::new();
        $route_args = $request->getDataGet('route_args');
        $id = $route_args[0] ?? null;
        $wpdb_prefix = GLOBAL_WPDB_PREFIX;
        
        $rules = [];

        $rules['service_type_id'] = "unique:service_type_id:{GLOBAL_WPDB_PREFIX}srvc_attribute_definitions@id:{$id}|numeric|required";
        $rules['code'] = "unique:code:{GLOBAL_WPDB_PREFIX}srvc_attribute_definitions@id:{$id}|required";
        $rules['label'] = "unique:label:{GLOBAL_WPDB_PREFIX}srvc_attribute_definitions@id:{$id}|required";
        $rules['data_type'] = "required";
        $rules['enum_options'] = "?required";
        $rules['unit'] = "?required";
        $rules['required'] = "?numeric";



        return $rules;
    }

    /**
     * Custom Error Messages
     *
     * @return array
     */
    protected function messages()
    {
        return [];
    }
}
