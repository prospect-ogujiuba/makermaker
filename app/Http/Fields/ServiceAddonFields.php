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
            'service_id' => 'required|numeric',
            'addon_service_id' => 'required|numeric',
            'required' => 'numeric',
            'min_qty' => '?numeric',
            'max_qty' => '?numeric',
            'default_qty' => '?numeric',
            'sort_order' => 'numeric'
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