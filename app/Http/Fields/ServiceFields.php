<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServiceFields extends Fields
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

        // Core Required Fields
        $rules['name'] = "unique:name:{GLOBAL_WPDB_PREFIX}srvc_services@id:{$id}required";
        $rules['slug'] = "unique:slug:{GLOBAL_WPDB_PREFIX}srvc_services@id:{$id}|required";
        $rules['category_id'] = 'required|numeric';
        $rules['service_type_id'] = 'required|numeric';
        $rules['complexity_id'] = 'required|numeric';

        // Optional Fields with Constraints
        $rules['sku'] = "unique:sku:{GLOBAL_WPDB_PREFIX}srvc_services@id:{$id}";
        $rules['short_desc'] = 'max:512';
        $rules['long_desc'] = '?required';
        $rules['default_unit'] = 'max:32';
        $rules['metadata'] = '?required'; // JSON validation could be added with custom validator

        // Boolean Fields
        $rules['is_active'] = '?numeric';
        $rules['is_addon'] = '?numeric';

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
