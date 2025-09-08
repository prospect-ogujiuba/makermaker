<?php
namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServiceAddonFields extends Fields
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
    protected function fillable() {
        return [];
    }

    /**
     * Validation Rules
     *
     * @return array
     */
    protected function rules() {
         $request = Request::new();
        $route_args = $request->getDataGet('route_args');
        $id = $route_args[0] ?? null;

        $rules = [];

        $rules['service_id'] = "required";
        $rules['addon_service_id'] = "required";
        $rules['required'] = '?required|numeric';
        $rules['min_qty'] = 'required|numeric';
        $rules['max_qty'] = 'required|numeric';
        $rules['price_delta'] = 'required|numeric';
        $rules['multiplier'] = 'required|numeric';

        return $rules;
    }

    /**
     * Custom Error Messages
     *
     * @return array
     */
    protected function messages() {
        return [];
    }
}