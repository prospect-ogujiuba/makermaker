<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServiceCategoryFields extends Fields
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

        $rules['name'] = "unique:name:{$wpdb_prefix}srvc_categories@id:{$id}|required|max:64";
        $rules['slug'] = "unique:slug:{$wpdb_prefix}srvc_categories@id:{$id}|?required|max:64";
        $rules['parent_id'] = "callback:checkSelfReference:{$wpdb_prefix}srvc_categories:parent_id:id";
        $rules['icon'] = "max:32";
        $rules['description'] = "";
        $rules['sort_order'] = "?numeric";
        $rules['is_active'] = "?numeric";



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
