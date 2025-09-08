<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServiceAttributeValueFields extends Fields
{
    /**
     * Run On Import
     *
     * Validate and then redirect on failure with errors, immediately
     * when imported by the application container resolver.
     *
     * @var bool
     */
    protected $run = null;

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

        $rules['service_id'] = 'required';
        $rules['attribute_definition_id'] = 'required';
        $rules['int_val'] = 'numeric|?required';
        $rules['bool_val'] = '?required';
        $rules['text_val'] = '?required';
        $rules['enum_val'] = '?required';

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
