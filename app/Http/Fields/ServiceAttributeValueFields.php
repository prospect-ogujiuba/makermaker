<?php

namespace MakerMaker\Http\Fields;

use MakerMaker\Models\ServiceAttributeDefinition;
use MakerMaker\Models\ServiceAttributeValue;
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

        // Basic required fields
        $rules['service_id'] = 'required';
        $rules['attribute_definition_id'] = 'required';

        // Get the attribute definition to determine data type and validation
        $attribute_definition_id = $this->get('attribute_definition_id');
        $definition = null;

        if ($attribute_definition_id) {
            $definition = ServiceAttributeDefinition::new()->find($attribute_definition_id);
        }

        // If we have a definition, apply type-specific validation
        if ($definition) {
            // tr_dd($definition->data_type); // This should now work

            $rules['value'] = $this->getValueValidationRule($definition);
        } else {
            // Default validation if no definition found
            $rules['value'] = 'required';
        }

        return $rules;
    }

    /**
     * Get validation rule based on attribute definition
     */
    private function getValueValidationRule(ServiceAttributeDefinition $definition)
    {
        $rule = $definition->required ? 'required' : '?required';

        switch ($definition->data_type) {
            case 'int':
                $rule .= '|numeric';
                break;
            case 'decimal':
                $rule .= '|numeric';
                break;
            case 'bool':
                $rule .= '|numeric|max:1';
                break;
            case 'date':
                $rule .= '|date';
                break;
            case 'datetime':
                $rule .= '|date';
                break;
            case 'email':
                $rule .= '|email';
                break;
            case 'url':
                $rule .= '|url';
                break;
            case 'json':
                $rule .= '|json';
                break;
            case 'enum':
                if ($definition->enum_options) {
                    $options = json_decode($definition->enum_options, true);
                    if (is_array($options) && !empty($options)) {
                        $rule .= '|in:' . implode(',', $options);
                    }
                }
                break;
        }

        return $rule;
    }

    /**
     * Custom Error Messages
     *
     * @return array
     */
    protected function messages()
    {
        return [
            'value.required' => 'This attribute value is required.',
            'value.numeric' => 'This attribute must be a number.',
            'value.boolean' => 'This attribute must be true or false.',
            'value.date' => 'This attribute must be a valid date.',
            'value.email' => 'This attribute must be a valid email address.',
            'value.url' => 'This attribute must be a valid URL.',
            'value.json' => 'This attribute must be valid JSON.',
            'value.in' => 'This attribute must be one of the allowed values.',
        ];
    }
}
