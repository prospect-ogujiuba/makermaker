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
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $id = $this['id'] ?? '';

        $rules = [];

        // Correct format: unique:field:table@id_column:id_value
        $rules['name'] = "unique:name:{$table_prefix}srvc_complexities@id:{$id}|required";
        $rules['level'] = "unique:level:{$table_prefix}srvc_complexities@id:{$id}|numeric|required";
        $rules['price_multiplier'] = "numeric|?required";

        die(var_dump($id));

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
