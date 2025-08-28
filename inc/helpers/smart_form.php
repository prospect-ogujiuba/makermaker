<?php

/**
 * TypeRocket Smart Form System
 * 
 * A comprehensive form building system that provides zero-config form generation
 * with fine-grained configuration options through model reflection.
 * 
 * Main entry point: tr_smart_form($modelClass, $config)
 */

/**
 * Main Smart Form Entry Point
 * 
 * Creates a complete form with zero configuration by default, with opt-in overrides
 * 
 * @param string $modelClass Fully qualified model class name
 * @param array $config Configuration overrides
 * @param mixed $resource Form resource (post, user, etc.)
 * @param string|null $action Form action (create, update)
 * @param int|null $itemId Item ID for updates
 * @return \TypeRocket\Elements\BaseForm|\TypeRocket\Elements\Tabs
 */
function tr_smart_form(string $modelClass, array $config = [], $resource = null, string $action = null, int $itemId = null)
{
    if (!class_exists($modelClass)) {
        throw new Exception("Model class {$modelClass} does not exist");
    }

    // Build complete form configuration
    $formConfig = tr_form_build($modelClass, $config);
    
    // Get form class from config or use default
    $formClass = \TypeRocket\Core\Config::get('app.class.form', \TypeRocket\Elements\BaseForm::class);
    $form = new $formClass($resource, $action, $itemId, $modelClass);
    
    // Apply hooks
    if (!empty($formConfig['hooks']['before_render'])) {
        $context = compact('modelClass', 'config', 'resource', 'action', 'itemId');
        $context = call_user_func($formConfig['hooks']['before_render'], $context);
        extract($context);
    }
    
    // Setup sections/tabs or basic form
    if (!empty($formConfig['sections'])) {
        $tabs = tr_form_sections($formConfig['sections'], $form, $formConfig);
        
        // Apply custom actions as footer
        if (!empty($formConfig['actions'])) {
            $tabs->setFooter(tr_form_render_actions($formConfig['actions'], $form));
        }
        
        return $tabs;
    }
    
    // Single form without sections
    tr_form_add_fields($form, $formConfig['fields'], $formConfig);
    
    // Apply after_render hook
    if (!empty($formConfig['hooks']['after_render'])) {
        $context = compact('form', 'formConfig');
        call_user_func($formConfig['hooks']['after_render'], $context);
    }
    
    return $form;
}

/**
 * Build Complete Form Configuration
 * 
 * Merges zero-config inference with user overrides
 */
function tr_form_build(string $modelClass, array $config = []): array
{
    $model = new $modelClass;
    $modelInfo = tr_model_info($model);
    
    // Start with inferred base configuration
    $baseConfig = [
        'fields' => tr_form_infer_fields($modelClass, $modelInfo),
        'sections' => tr_form_infer_sections($modelClass, $modelInfo),
        'validation' => tr_form_infer_validation($modelClass, $modelInfo),
        'layout' => tr_form_infer_layout($modelClass, $modelInfo),
        'relations' => tr_form_infer_relations($modelClass, $modelInfo),
        'conditionals' => [],
        'widgets' => [],
        'hooks' => [],
        'actions' => tr_form_infer_actions($modelClass, $modelInfo),
    ];
    
    // Merge with user configuration
    return array_merge_recursive($baseConfig, $config);
}

/**
 * Setup Form Sections/Tabs
 */
function tr_form_sections(array $sections, $form, array $formConfig): \TypeRocket\Elements\Tabs
{
    $tabs = \TypeRocket\Elements\Tabs::new()->layoutLeft();
    
    foreach ($sections as $section) {
        $sectionFields = [];
        
        // Get fields for this section
        foreach ($section['fields'] as $fieldName) {
            if (isset($formConfig['fields'][$fieldName])) {
                $sectionFields[$fieldName] = $formConfig['fields'][$fieldName];
            }
        }
        
        // Create fieldset
        $fieldset = $form->fieldset(
            $section['label'], 
            $section['description'] ?? '', 
            tr_form_create_fields($form, $sectionFields, $formConfig)
        );
        
        // Add tab
        $tabs->tab($section['label'], $section['icon'] ?? 'admin-generic', [$fieldset])
             ->setDescription($section['description'] ?? '');
    }
    
    return $tabs;
}

/**
 * Create Form Fields from Configuration
 */
function tr_form_create_fields($form, array $fields, array $formConfig): array
{
    $elements = [];
    $layout = $formConfig['layout'] ?? [];
    
    // Group fields by layout if specified
    if (!empty($layout['rows'])) {
        foreach ($layout['rows'] as $row) {
            $rowFields = [];
            foreach ($row['columns'] as $column) {
                foreach ($column['fields'] as $fieldName) {
                    if (isset($fields[$fieldName])) {
                        $rowFields[] = tr_form_create_single_field($form, $fieldName, $fields[$fieldName], $formConfig);
                    }
                }
            }
            
            if (!empty($rowFields)) {
                $elements[] = count($rowFields) > 1 ? $form->row(...$rowFields) : $rowFields[0];
            }
        }
    } else {
        // Default layout - create fields in order
        foreach ($fields as $fieldName => $fieldConfig) {
            $elements[] = tr_form_create_single_field($form, $fieldName, $fieldConfig, $formConfig);
        }
    }
    
    return $elements;
}

/**
 * Create Single Form Field
 */
function tr_form_create_single_field($form, string $fieldName, array $fieldConfig, array $formConfig)
{
    // Skip hidden fields
    if ($fieldConfig['hidden'] ?? false) {
        return null;
    }
    
    $widget = $fieldConfig['widget'] ?? 'text';
    $label = $fieldConfig['label'] ?? tr_humanize_field_name($fieldName);
    $attrs = $fieldConfig['attrs'] ?? [];
    
    // Check for custom widget renderer
    if (isset($formConfig['widgets'][$widget])) {
        $renderer = $formConfig['widgets'][$widget];
        return call_user_func($renderer, $fieldName, $fieldConfig['value'] ?? null, $fieldConfig);
    }
    
    // Create field based on widget type
    $field = tr_form_create_widget($form, $widget, $label, $attrs);
    
    // Set field name
    $field->setName($fieldName);
    
    // Apply field configuration
    if (isset($fieldConfig['placeholder'])) {
        $field->setAttribute('placeholder', $fieldConfig['placeholder']);
    }
    
    if (isset($fieldConfig['help'])) {
        $field->setHelp($fieldConfig['help']);
    }
    
    if (isset($fieldConfig['required']) && $fieldConfig['required']) {
        $field->markLabelRequired();
    }
    
    if (isset($fieldConfig['default'])) {
        $field->setDefault($fieldConfig['default']);
    }
    
    // Handle options for select/radio/checkbox fields
    if (isset($fieldConfig['options'])) {
        $options = is_callable($fieldConfig['options']) 
            ? call_user_func($fieldConfig['options']) 
            : $fieldConfig['options'];
        $field->setOptions($options);
    }
    
    // Apply formatter if specified
    if (isset($fieldConfig['formatter']) && is_callable($fieldConfig['formatter'])) {
        // Note: TypeRocket doesn't directly support formatters on form fields
        // This would need to be applied during display/processing
    }
    
    return $field;
}

/**
 * Create Widget Field
 */
function tr_form_create_widget($form, string $widget, string $label, array $attrs)
{
    switch ($widget) {
        case 'text':
            return $form->text($label, $attrs);
        case 'email':
            return $form->email($label, $attrs);
        case 'password':
            return $form->password($label, $attrs);
        case 'textarea':
            return $form->textarea($label, $attrs);
        case 'editor':
            return $form->editor($label, $attrs);
        case 'select':
            return $form->select($label, $attrs);
        case 'radio':
            return $form->radio($label, $attrs);
        case 'checkbox':
            return $form->checkbox($label, $attrs);
        case 'toggle':
            return $form->toggle($label, $attrs);
        case 'number':
            return $form->number($label, $attrs);
        case 'date':
            return $form->date($label, $attrs);
        case 'datetime':
            return $form->datetime($label, $attrs);
        case 'time':
            return $form->time($label, $attrs);
        case 'color':
            return $form->color($label, $attrs);
        case 'file':
            return $form->file($label, $attrs);
        case 'image':
            return $form->image($label, $attrs);
        case 'gallery':
            return $form->gallery($label, $attrs);
        case 'search':
            return $form->search($label, $attrs);
        case 'repeater':
            return $form->repeater($label, $attrs);
        case 'matrix':
            return $form->matrix($label, $attrs);
        case 'items':
            return $form->items($label, $attrs);
        case 'money':
            // Custom money field - extend text with currency formatting
            $field = $form->text($label, array_merge($attrs, ['type' => 'number', 'step' => '0.01']));
            $field->setAttribute('data-money', true);
            return $field;
        case 'tags':
            // Tags widget for belongsToMany relations
            return $form->items($label, $attrs);
        case 'code':
            // Code editor widget
            $field = $form->textarea($label, $attrs);
            $field->setAttribute('data-code-editor', true);
            return $field;
        default:
            return $form->text($label, $attrs);
    }
}

/**
 * Add Fields to Form (for non-sectioned forms)
 */
function tr_form_add_fields($form, array $fields, array $formConfig): void
{
    $elements = tr_form_create_fields($form, $fields, $formConfig);
    
    // Note: TypeRocket automatically adds fields to form when created
    // No explicit adding needed for basic form
}

/**
 * Render Custom Actions
 */
function tr_form_render_actions(array $actions, $form): string
{
    $html = '<div class="tr-form-actions">';
    
    foreach ($actions as $action) {
        $key = $action['key'] ?? 'action';
        $label = $action['label'] ?? 'Action';
        $isPrimary = $action['primary'] ?? false;
        $href = $action['href'] ?? null;
        
        if ($href) {
            $class = $isPrimary ? 'button button-primary' : 'button';
            $html .= sprintf('<a href="%s" class="%s">%s</a>', esc_url($href), esc_attr($class), esc_html($label));
        } else {
            $class = $isPrimary ? 'button button-primary' : 'button';
            $html .= sprintf('<input type="submit" name="action[%s]" value="%s" class="%s" />', 
                esc_attr($key), esc_attr($label), esc_attr($class));
        }
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Field Inference from Model
 */
function tr_form_infer_fields(string $modelClass, array $modelInfo): array
{
    $fields = [];
    $fillable = $modelInfo['fillable']['fillable'] ?? [];
    $casts = $modelInfo['fillable']['cast'] ?? [];
    $relationships = $modelInfo['relationships'] ?? [];
    
    // Process fillable fields
    foreach ($fillable as $fieldName) {
        $fields[$fieldName] = tr_form_infer_field_config($fieldName, $casts, $modelInfo);
    }
    
    // Process relationships
    foreach ($relationships as $relName => $relConfig) {
        $fields[$relName] = tr_form_infer_relation_field_config($relName, $relConfig);
    }
    
    return $fields;
}

/**
 * Infer Single Field Configuration
 */
function tr_form_infer_field_config(string $fieldName, array $casts, array $modelInfo): array
{
    $config = [
        'widget' => 'text',
        'label' => tr_humanize_field_name($fieldName),
        'attrs' => [],
    ];
    
    $lowerName = strtolower($fieldName);
    
    // Infer widget from field name patterns
    if (str_contains($lowerName, 'email')) {
        $config['widget'] = 'email';
    } elseif (str_contains($lowerName, 'password')) {
        $config['widget'] = 'password';
    } elseif (str_contains($lowerName, 'description') || str_contains($lowerName, 'content') || str_contains($lowerName, 'text')) {
        $config['widget'] = 'textarea';
    } elseif (str_contains($lowerName, 'price') || str_contains($lowerName, 'cost') || str_contains($lowerName, 'amount')) {
        $config['widget'] = 'money';
    } elseif (str_ends_with($lowerName, '_id') || str_contains($lowerName, 'category') || str_contains($lowerName, 'type')) {
        $config['widget'] = 'select';
    } elseif (str_contains($lowerName, 'active') || str_contains($lowerName, 'enabled') || str_contains($lowerName, 'is_')) {
        $config['widget'] = 'toggle';
    } elseif (str_contains($lowerName, 'date') || str_contains($lowerName, 'time')) {
        $config['widget'] = str_contains($lowerName, 'time') && !str_contains($lowerName, 'date') ? 'time' : 'date';
    } elseif (str_contains($lowerName, 'image') || str_contains($lowerName, 'photo') || str_contains($lowerName, 'picture')) {
        $config['widget'] = 'image';
    }
    
    // Override based on cast types
    if (isset($casts[$fieldName])) {
        switch ($casts[$fieldName]) {
            case 'boolean':
            case 'bool':
                $config['widget'] = 'toggle';
                break;
            case 'integer':
            case 'int':
                $config['widget'] = 'number';
                break;
            case 'date':
                $config['widget'] = 'date';
                break;
            case 'datetime':
                $config['widget'] = 'datetime';
                break;
            case 'array':
            case 'json':
                $config['widget'] = 'code';
                break;
        }
    }
    
    return $config;
}

/**
 * Infer Relationship Field Configuration
 */
function tr_form_infer_relation_field_config(string $relName, array $relConfig): array
{
    $relType = $relConfig['type'] ?? 'belongsTo';
    
    $config = [
        'widget' => 'select',
        'label' => tr_humanize_field_name($relName),
        'attrs' => [],
    ];
    
    switch ($relType) {
        case 'hasMany':
            $config['widget'] = 'repeater';
            break;
        case 'belongsToMany':
            $config['widget'] = 'tags';
            break;
        case 'belongsTo':
        case 'hasOne':
        default:
            $config['widget'] = 'select';
            break;
    }
    
    return $config;
}

/**
 * Infer Form Sections from Model
 */
function tr_form_infer_sections(string $modelClass, array $modelInfo): array
{
    $fillable = $modelInfo['fillable']['fillable'] ?? [];
    $relationships = $modelInfo['relationships'] ?? [];
    
    // Check for static sections configuration on model
    if (property_exists($modelClass, 'formSections')) {
        return $modelClass::$formSections;
    }
    
    $sections = [];
    
    // Main section with core fields (first 8)
    $mainFields = array_slice($fillable, 0, 8);
    if (!empty($mainFields)) {
        $sections[] = [
            'key' => 'main',
            'label' => 'Details',
            'icon' => 'admin-generic',
            'description' => 'Core information',
            'fields' => $mainFields,
            'layout' => ['cols' => 2],
        ];
    }
    
    // Advanced section with remaining fields
    $advancedFields = array_slice($fillable, 8);
    if (!empty($advancedFields)) {
        $sections[] = [
            'key' => 'advanced',
            'label' => 'Advanced',
            'icon' => 'admin-tools',
            'description' => 'Additional settings',
            'fields' => $advancedFields,
            'collapsed' => true,
        ];
    }
    
    // Relationships section
    if (!empty($relationships)) {
        $sections[] = [
            'key' => 'relationships',
            'label' => 'Relationships',
            'icon' => 'admin-links',
            'description' => 'Related data',
            'fields' => array_keys($relationships),
        ];
    }
    
    return $sections;
}

/**
 * Infer Validation Rules
 */
function tr_form_infer_validation(string $modelClass, array $modelInfo): array
{
    // Check for static validation rules
    if (property_exists($modelClass, 'rules')) {
        return ['rules' => $modelClass::$rules];
    }
    
    $fillable = $modelInfo['fillable']['fillable'] ?? [];
    $casts = $modelInfo['fillable']['cast'] ?? [];
    $rules = [];
    
    foreach ($fillable as $fieldName) {
        $fieldRules = [];
        $lowerName = strtolower($fieldName);
        
        // Common required fields
        if (in_array($lowerName, ['name', 'title', 'email'])) {
            $fieldRules[] = 'required';
        }
        
        // Email validation
        if (str_contains($lowerName, 'email')) {
            $fieldRules[] = 'email';
        }
        
        // Cast-based validation
        if (isset($casts[$fieldName])) {
            switch ($casts[$fieldName]) {
                case 'integer':
                case 'int':
                    $fieldRules[] = 'integer';
                    break;
                case 'boolean':
                case 'bool':
                    $fieldRules[] = 'boolean';
                    break;
            }
        }
        
        if (!empty($fieldRules)) {
            $rules[$fieldName] = $fieldRules;
        }
    }
    
    return ['rules' => $rules];
}

/**
 * Infer Layout Configuration
 */
function tr_form_infer_layout(string $modelClass, array $modelInfo): array
{
    // Default 2-column layout for forms
    return [
        'cols' => 2,
        'responsive' => true,
    ];
}

/**
 * Infer Relations Configuration
 */
function tr_form_infer_relations(string $modelClass, array $modelInfo): array
{
    $relationships = $modelInfo['relationships'] ?? [];
    $relations = [];
    
    foreach ($relationships as $relName => $relConfig) {
        $relations[$relName] = [
            'type' => $relConfig['type'] ?? 'belongsTo',
            'widget' => $relConfig['type'] === 'hasMany' ? 'repeater' : 'select',
        ];
    }
    
    return $relations;
}

/**
 * Infer Form Actions
 */
function tr_form_infer_actions(string $modelClass, array $modelInfo): array
{
    return [
        ['key' => 'save', 'label' => 'Save', 'primary' => true],
        ['key' => 'save_continue', 'label' => 'Save & Continue'],
    ];
}

/**
 * Humanize Field Names
 */
// function tr_humanize_field_name(string $fieldName): string
// {
//     // Handle relationship notation
//     if (str_contains($fieldName, '.')) {
//         $parts = explode('.', $fieldName);
//         $fieldName = end($parts);
//     }
    
//     // Convert to human readable
//     $humanized = str_replace(['_', '-'], ' ', $fieldName);
//     $humanized = ucwords($humanized);
    
//     // Handle common abbreviations
//     $replacements = [
//         ' Id' => ' ID',
//         ' Url' => ' URL', 
//         ' Api' => ' API',
//         ' Seo' => ' SEO',
//         ' Html' => ' HTML',
//         ' Css' => ' CSS',
//         ' Json' => ' JSON',
//         ' Xml' => ' XML',
//     ];
    
//     return str_replace(array_keys($replacements), array_values($replacements), $humanized);
// }

/**
 * TypeRocket Smart Form Helpers
 * 
 * Composable helper functions for building smart forms with fine-grained control
 */

/**
 * Form Fields Configuration Helper
 * 
 * Define or override specific field configurations
 */
function tr_form_fields(array $fields): array
{
    $processed = [];
    
    foreach ($fields as $fieldName => $config) {
        if ($config === false) {
            // Hide field
            $processed[$fieldName] = ['hidden' => true];
        } elseif (is_array($config)) {
            // Full configuration
            $processed[$fieldName] = $config;
        } else {
            // Just a widget name
            $processed[$fieldName] = ['widget' => $config];
        }
    }
    
    return $processed;
}

/**
 * Form Layout Configuration Helper
 * 
 * Define responsive grid layouts for forms
 */
function tr_form_layout(array $layout): array
{
    // Normalize layout configuration
    $config = [
        'rows' => [],
        'cols' => $layout['cols'] ?? 2,
        'responsive' => $layout['responsive'] ?? true,
    ];
    
    // Convert simple rows to full structure
    if (isset($layout['rows'])) {
        foreach ($layout['rows'] as $row) {
            if (isset($row['columns'])) {
                // Full row definition
                $config['rows'][] = $row;
            } else {
                // Simple field list
                $columns = [];
                $spanPerField = 12 / count($row);
                
                foreach ($row as $fieldName) {
                    $columns[] = [
                        'span' => $spanPerField,
                        'fields' => [$fieldName],
                    ];
                }
                
                $config['rows'][] = ['columns' => $columns];
            }
        }
    }
    
    return $config;
}

/**
 * Form Validation Configuration Helper
 */
function tr_form_validation(array $rules, array $messages = [], array $scenes = []): array
{
    return [
        'rules' => $rules,
        'messages' => $messages,
        'scenes' => $scenes,
    ];
}

/**
 * Form Relations Configuration Helper
 * 
 * Configure relationship handling in forms
 */
function tr_form_relations(array $relations): array
{
    $processed = [];
    
    foreach ($relations as $relName => $config) {
        $processed[$relName] = array_merge([
            'type' => 'belongsTo',
            'widget' => 'select',
            'multiple' => false,
            'options' => null,
        ], $config);
        
        // Auto-configure based on relation type
        if ($config['type'] === 'hasMany') {
            $processed[$relName]['widget'] = $config['widget'] ?? 'repeater';
            $processed[$relName]['multiple'] = true;
        } elseif ($config['type'] === 'belongsToMany') {
            $processed[$relName]['widget'] = $config['widget'] ?? 'tags';
            $processed[$relName]['multiple'] = true;
        }
    }
    
    return $processed;
}

/**
 * Form Conditionals Configuration Helper
 * 
 * Define show/hide logic based on field values
 */
function tr_form_conditionals(array $conditionals): array
{
    $processed = [];
    
    foreach ($conditionals as $conditional) {
        if (isset($conditional['if'], $conditional['then'])) {
            $processed[] = [
                'condition' => $conditional['if'],
                'action' => $conditional['then'],
                'else' => $conditional['else'] ?? null,
            ];
        }
    }
    
    return $processed;
}

/**
 * Form Repeaters Configuration Helper
 * 
 * Configure dynamic field groups
 */
function tr_form_repeaters(array $repeaters): array
{
    $processed = [];
    
    foreach ($repeaters as $name => $config) {
        $processed[$name] = array_merge([
            'min' => 0,
            'max' => null,
            'fields' => [],
            'sortable' => true,
            'addButtonText' => 'Add Item',
            'removeButtonText' => 'Remove',
        ], $config);
    }
    
    return $processed;
}

/**
 * Form Hooks Configuration Helper
 * 
 * Define lifecycle hooks for form processing
 */
function tr_form_hooks(array $hooks): array
{
    $allowedHooks = [
        'before_render',
        'after_render', 
        'before_save',
        'after_save',
        'before_validate',
        'after_validate',
    ];
    
    $processed = [];
    
    foreach ($hooks as $hook => $callback) {
        if (in_array($hook, $allowedHooks) && is_callable($callback)) {
            $processed[$hook] = $callback;
        }
    }
    
    return $processed;
}

/**
 * Form Actions Configuration Helper
 * 
 * Define custom submit buttons and actions
 */
function tr_form_actions(array $actions): array
{
    $processed = [];
    
    foreach ($actions as $action) {
        $processed[] = array_merge([
            'key' => 'action',
            'label' => 'Action',
            'primary' => false,
            'href' => null,
            'confirm' => false,
            'class' => '',
        ], $action);
    }
    
    return $processed;
}

/**
 * Form Widgets Configuration Helper
 * 
 * Register custom field renderers
 */
function tr_form_widgets(array $widgets): array
{
    $processed = [];
    
    foreach ($widgets as $name => $renderer) {
        if (is_callable($renderer)) {
            $processed[$name] = $renderer;
        }
    }
    
    return $processed;
}

/**
 * Quick Form Builder Helpers
 * 
 * Convenience functions for common form patterns
 */

/**
 * Create a basic form with minimal configuration
 */
function tr_quick_form(string $modelClass, array $fields = []): array
{
    return [
        'fields' => tr_form_fields($fields),
        'sections' => false, // Disable sections for simple form
        'layout' => tr_form_layout(['cols' => 1]),
    ];
}

/**
 * Create a two-column form
 */
function tr_two_column_form(string $modelClass, array $fields = []): array
{
    return [
        'fields' => tr_form_fields($fields),
        'sections' => false,
        'layout' => tr_form_layout(['cols' => 2]),
    ];
}

/**
 * Create a tabbed form with automatic sectioning
 */
function tr_tabbed_form(string $modelClass, array $sections = []): array
{
    return [
        'sections' => $sections ?: null, // Use auto-inferred sections if empty
        'layout' => tr_form_layout(['cols' => 2]),
    ];
}

/**
 * Create a form with repeaters for related data
 */
function tr_relational_form(string $modelClass, array $relations = []): array
{
    return [
        'relations' => tr_form_relations($relations),
        'layout' => tr_form_layout(['cols' => 1]), // Single column for repeaters
    ];
}

/**
 * Widget-specific Helper Functions
 */

/**
 * Money field configuration helper
 */
function tr_money_field(string $label = null, array $config = []): array
{
    return array_merge([
        'widget' => 'money',
        'label' => $label,
        'attrs' => [
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
        ],
        'formatter' => function($value) {
            return number_format((float)$value, 2);
        },
    ], $config);
}

/**
 * Rich text editor field configuration
 */
function tr_editor_field(string $label = null, array $config = []): array
{
    return array_merge([
        'widget' => 'editor',
        'label' => $label,
        'attrs' => [
            'rows' => 10,
        ],
    ], $config);
}

/**
 * Select field with model options
 */
function tr_model_select(string $modelClass, string $titleField = 'title', string $label = null, array $config = []): array
{
    return array_merge([
        'widget' => 'select',
        'label' => $label,
        'options' => function() use ($modelClass, $titleField) {
            if (!class_exists($modelClass)) {
                return [];
            }
            return $modelClass::new()->findAll()->get($titleField, 'id')->toArray() ?? [];
        },
    ], $config);
}

/**
 * Tags field for many-to-many relationships
 */
function tr_tags_field(string $modelClass, string $titleField = 'name', string $label = null, array $config = []): array
{
    return array_merge([
        'widget' => 'tags',
        'label' => $label,
        'options' => function() use ($modelClass, $titleField) {
            if (!class_exists($modelClass)) {
                return [];
            }
            return $modelClass::new()->findAll()->get()->pluck($titleField, 'id')->toArray() ?? [];
        },
    ], $config);
}

/**
 * Toggle field with custom text
 */
function tr_toggle_field(string $label = null, string $text = null, array $config = []): array
{
    return array_merge([
        'widget' => 'toggle',
        'label' => $label,
        'text' => $text ?: $label,
        'default' => false,
    ], $config);
}

/**
 * Code editor field
 */
function tr_code_field(string $language = 'json', string $label = null, array $config = []): array
{
    return array_merge([
        'widget' => 'code',
        'label' => $label,
        'attrs' => [
            'data-language' => $language,
            'rows' => 10,
        ],
    ], $config);
}

/**
 * Repeater field configuration
 */
function tr_repeater_field(array $fields, string $label = null, array $config = []): array
{
    return array_merge([
        'widget' => 'repeater',
        'label' => $label,
        'fields' => $fields,
        'min' => 0,
        'max' => null,
        'sortable' => true,
    ], $config);
}

/**
 * File upload field
 */
function tr_file_field(array $allowedTypes = [], string $label = null, array $config = []): array
{
    return array_merge([
        'widget' => 'file',
        'label' => $label,
        'attrs' => [
            'accept' => implode(',', $allowedTypes),
        ],
    ], $config);
}

/**
 * Image upload field
 */
function tr_image_field(string $label = null, array $config = []): array
{
    return array_merge([
        'widget' => 'image',
        'label' => $label,
        'attrs' => [
            'accept' => 'image/*',
        ],
    ], $config);
}

/**
 * Date range field helper
 */
function tr_date_range_fields(string $startField = 'start_date', string $endField = 'end_date'): array
{
    return [
        $startField => [
            'widget' => 'date',
            'label' => 'Start Date',
            'attrs' => ['data-date-range' => 'start'],
        ],
        $endField => [
            'widget' => 'date', 
            'label' => 'End Date',
            'attrs' => ['data-date-range' => 'end'],
        ],
    ];
}

/**
 * Address field group helper
 */
function tr_address_fields(string $prefix = 'address'): array
{
    return [
        "{$prefix}_line1" => [
            'widget' => 'text',
            'label' => 'Address Line 1',
            'required' => true,
        ],
        "{$prefix}_line2" => [
            'widget' => 'text',
            'label' => 'Address Line 2',
        ],
        "{$prefix}_city" => [
            'widget' => 'text',
            'label' => 'City',
            'required' => true,
        ],
        "{$prefix}_state" => [
            'widget' => 'text',
            'label' => 'State/Province',
            'required' => true,
        ],
        "{$prefix}_postal_code" => [
            'widget' => 'text',
            'label' => 'Postal Code',
            'required' => true,
        ],
        "{$prefix}_country" => [
            'widget' => 'select',
            'label' => 'Country',
            'options' => [
                'US' => 'United States',
                'CA' => 'Canada',
                'GB' => 'United Kingdom',
                'AU' => 'Australia',
                // Add more countries as needed
            ],
            'default' => 'US',
        ],
    ];
}

/**
 * Contact information field group
 */
function tr_contact_fields(string $prefix = 'contact'): array
{
    return [
        "{$prefix}_email" => [
            'widget' => 'email',
            'label' => 'Email Address',
            'required' => true,
        ],
        "{$prefix}_phone" => [
            'widget' => 'text',
            'label' => 'Phone Number',
            'attrs' => ['type' => 'tel'],
        ],
        "{$prefix}_mobile" => [
            'widget' => 'text',
            'label' => 'Mobile Number',
            'attrs' => ['type' => 'tel'],
        ],
        "{$prefix}_website" => [
            'widget' => 'text',
            'label' => 'Website',
            'attrs' => ['type' => 'url'],
        ],
    ];
}

/**
 * SEO field group helper
 */
function tr_seo_fields(string $prefix = 'seo'): array
{
    return [
        "{$prefix}_title" => [
            'widget' => 'text',
            'label' => 'SEO Title',
            'help' => 'Recommended length: 50-60 characters',
            'attrs' => ['maxlength' => 60],
        ],
        "{$prefix}_description" => [
            'widget' => 'textarea',
            'label' => 'SEO Description',
            'help' => 'Recommended length: 150-160 characters',
            'attrs' => ['maxlength' => 160, 'rows' => 3],
        ],
        "{$prefix}_keywords" => [
            'widget' => 'text',
            'label' => 'SEO Keywords',
            'help' => 'Comma-separated keywords',
        ],
        "{$prefix}_canonical_url" => [
            'widget' => 'text',
            'label' => 'Canonical URL',
            'attrs' => ['type' => 'url'],
        ],
    ];
}

/**
 * Social media field group helper
 */
function tr_social_fields(string $prefix = 'social'): array
{
    return [
        "{$prefix}_facebook" => [
            'widget' => 'text',
            'label' => 'Facebook URL',
            'attrs' => ['type' => 'url'],
        ],
        "{$prefix}_twitter" => [
            'widget' => 'text',
            'label' => 'Twitter URL',
            'attrs' => ['type' => 'url'],
        ],
        "{$prefix}_linkedin" => [
            'widget' => 'text',
            'label' => 'LinkedIn URL',
            'attrs' => ['type' => 'url'],
        ],
        "{$prefix}_instagram" => [
            'widget' => 'text',
            'label' => 'Instagram URL',
            'attrs' => ['type' => 'url'],
        ],
        "{$prefix}_youtube" => [
            'widget' => 'text',
            'label' => 'YouTube URL',
            'attrs' => ['type' => 'url'],
        ],
    ];
}

/**
 * Form Configuration Presets
 */

/**
 * Service form preset matching the sample form
 */
function tr_service_form_preset(): array
{
    return [
        'sections' => [
            [
                'key' => 'overview',
                'label' => 'Overview',
                'icon' => 'admin-post',
                'description' => 'Core service information',
                'fields' => ['name', 'slug', 'category_id', 'service_type', 'short_description', 'long_description', 'delivery_method'],
            ],
            [
                'key' => 'pricing',
                'label' => 'Pricing',
                'icon' => 'money-alt',
                'description' => 'Base pricing configuration',
                'fields' => ['pricing_model', 'base_price', 'hourly_rate'],
            ],
            [
                'key' => 'advanced',
                'label' => 'Advanced',
                'icon' => 'admin-tools',
                'description' => 'Additional settings',
                'fields' => ['complexity_level', 'estimated_hours', 'is_active', 'is_featured'],
            ],
        ],
        'fields' => [
            'name' => [
                'widget' => 'text',
                'label' => 'Service Name',
                'placeholder' => 'e.g., Pro VOIP Bundle',
                'help' => 'Clear, customer-facing name.',
                'attrs' => ['maxlength' => 200],
                'required' => true,
            ],
            'slug' => [
                'widget' => 'text',
                'label' => 'URL Slug',
                'help' => 'URL-friendly slug',
                'attrs' => ['maxlength' => 200],
            ],
            'category_id' => tr_model_select('\\MakerMaker\\Models\\ServiceCategory', 'name', 'Service Category'),
            'service_type' => [
                'widget' => 'select',
                'label' => 'Service Type',
                'options' => [
                    'installation' => 'Installation',
                    'maintenance' => 'Maintenance',
                    'hosting' => 'Hosting',
                    'consulting' => 'Consulting',
                    'support' => 'Support',
                    'hybrid' => 'Hybrid',
                ],
                'required' => true,
            ],
            'short_description' => [
                'widget' => 'textarea',
                'label' => 'Short Description',
                'help' => 'Brief description for listings',
                'attrs' => ['maxlength' => 500, 'rows' => 3],
            ],
            'long_description' => [
                'widget' => 'editor',
                'label' => 'Long Description',
                'help' => 'Detailed service description',
            ],
            'delivery_method' => [
                'widget' => 'select',
                'label' => 'Delivery Method',
                'options' => [
                    'onsite' => 'On-Site',
                    'remote' => 'Remote',
                    'hybrid' => 'Hybrid',
                ],
            ],
            'pricing_model' => [
                'widget' => 'select',
                'label' => 'Pricing Model',
                'options' => [
                    'fixed' => 'Fixed Price',
                    'hourly' => 'Hourly Rate',
                    'monthly' => 'Monthly',
                    'project' => 'Project-based',
                    'tiered' => 'Tiered',
                    'custom' => 'Custom Quote',
                ],
                'required' => true,
            ],
            'base_price' => tr_money_field('Base Price', [
                'help' => 'Base price for this service',
            ]),
            'hourly_rate' => tr_money_field('Hourly Rate', [
                'help' => 'Hourly rate if applicable',
            ]),
            'complexity_level' => [
                'widget' => 'select',
                'label' => 'Complexity Level',
                'options' => [
                    'basic' => 'Basic',
                    'intermediate' => 'Intermediate',
                    'advanced' => 'Advanced',
                    'expert' => 'Expert',
                ],
            ],
            'estimated_hours' => [
                'widget' => 'number',
                'label' => 'Estimated Hours',
                'attrs' => ['min' => 0, 'step' => 0.5],
            ],
            'is_active' => tr_toggle_field('Active', 'Service is active and available'),
            'is_featured' => tr_toggle_field('Featured', 'Featured service'),
        ],
        'relations' => [
            'pricing_tiers' => [
                'type' => 'hasMany',
                'widget' => 'repeater',
                'fields' => [
                    'tier_name' => ['widget' => 'text', 'label' => 'Tier Name'],
                    'price' => tr_money_field('Price'),
                    'min_quantity' => ['widget' => 'number', 'label' => 'Min Quantity', 'attrs' => ['min' => 1]],
                    'max_quantity' => ['widget' => 'number', 'label' => 'Max Quantity'],
                    'is_active' => tr_toggle_field('Active'),
                ],
            ],
            'addons' => [
                'type' => 'hasMany',
                'widget' => 'repeater',
                'fields' => [
                    'addon_name' => ['widget' => 'text', 'label' => 'Addon Name'],
                    'addon_type' => [
                        'widget' => 'select',
                        'label' => 'Addon Type',
                        'options' => [
                            'upgrade' => 'Upgrade',
                            'additional' => 'Additional',
                            'extended_warranty' => 'Extended Warranty',
                            'training' => 'Training',
                            'support' => 'Support',
                        ],
                    ],
                    'price' => tr_money_field('Price'),
                    'is_recurring' => tr_toggle_field('Recurring'),
                ],
            ],
        ],
        'validation' => [
            'rules' => [
                'name' => ['required', 'string', 'max:200'],
                'service_type' => ['required'],
                'pricing_model' => ['required'],
                'base_price' => ['nullable', 'numeric', 'min:0'],
                'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            ],
            'messages' => [
                'name.required' => 'Please provide a service name.',
                'service_type.required' => 'Please select a service type.',
                'pricing_model.required' => 'Please select a pricing model.',
            ],
        ],
        'hooks' => [
            'before_save' => function($payload, $model) {
                // Auto-generate slug if not provided
                if (empty($payload['slug']) && !empty($payload['name'])) {
                    $payload['slug'] = \Illuminate\Support\Str::slug($payload['name']);
                }
                return $payload;
            },
        ],
        'actions' => [
            ['key' => 'save', 'label' => 'Save Service', 'primary' => true],
            ['key' => 'save_continue', 'label' => 'Save & Continue'],
            ['key' => 'cancel', 'label' => 'Cancel', 'href' => admin_url('admin.php?page=services')],
        ],
    ];
}