<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class DeliverableFields extends Fields
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

        $rules['name'] = "unique:name:{$wpdb_prefix}srvc_deliverables@id:{$id}|required|max:128";
        $rules['description'] = "required";
        $rules['deliverable_type'] = "";
        $rules['template_path'] = "max:255|?required";
        $rules['estimated_effort_hours'] = "?numeric|min:0.01|?required";
        $rules['requires_approval'] = "?numeric|callback:checkIntRange:0:1";

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
