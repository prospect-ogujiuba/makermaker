<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServiceDeliveryFields extends Fields
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

    $rules = [];

    $rules['service_id'] = "numeric|required";
    $rules['delivery_method_id'] = "numeric|required";
    $rules['lead_time_days'] = "?numeric|?required|min:0";
    $rules['sla_hours'] = "?numeric|min:0";
    $rules['surcharge'] = "?numeric|?required|min:0";
    $rules['is_default'] = "?numeric|callback:checkIntRange:0:1";

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
