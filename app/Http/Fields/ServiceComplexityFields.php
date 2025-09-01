<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;

class ServiceComplexityFields extends Fields
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
        return [
            'name' => 'unique:name:wp_srvc_complexities|required',
            'level' => '?unique:level:wp_srvc_complexities|required|min:1',
            'price_multiplier' => '?required|numeric'
        ];
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
