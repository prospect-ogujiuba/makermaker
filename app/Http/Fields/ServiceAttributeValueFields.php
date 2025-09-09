<?php

namespace MakerMaker\Http\Fields;

use MakerMaker\Models\ServiceAttributeDefinition;
use MakerMaker\Models\ServiceAttributeValue;
use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

// Define the standalone function OUTSIDE the class
function validateTypedValue($args)
{
    /**
     * @var $option
     * @var $option2
     * @var $option3
     * @var $name
     * @var $field_name
     * @var $value
     * @var $type
     * @var \TypeRocket\Utility\Validator $validator
     */
    extract($args);

    // Get all form data from validator
    $form_data = $validator->getFields();
    $attributeDefinitionId = $form_data['attribute_definition_id'] ?? null;

    if (!$attributeDefinitionId) {
        return 'Attribute definition is required';
    }

    // Create instance and find the definition
    $definitionModel = new ServiceAttributeDefinition();
    $definition = $definitionModel->findById($attributeDefinitionId);

    if (!$definition) {
        return 'Invalid attribute definition';
    }

    // Create temporary model instance for validation
    $tempModel = new ServiceAttributeValue();
    $tempModel->attribute_definition_id = $attributeDefinitionId;

    $validation = $tempModel->validateValue($value);

    return $validation['Valid'] ? true : $validation['Message'];
}

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

        $rules['service_id'] = 'required';
        $rules['attribute_definition_id'] = 'required';
        $rules['value'] = 'required';

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
