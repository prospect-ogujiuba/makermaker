<?php

/**
 * TypeRocket Form Helper Functions
 * 
 * Drop-in PHP helpers for generating TypeRocket admin forms via model reflection.
 * Returns configured form/builder instances without rendering - use existing TypeRocket render calls.
 */

/**
 * Primary entrypoint - configures and returns a TypeRocket form with tabs, fields, and validation
 * 
 * @param string $modelClass Fully qualified model class name
 * @param array $overrides Override configuration
 * @param mixed $resource Form resource (post, user, etc.)
 * @param string|null $action Form action (create, update)
 * @param int|null $itemId Item ID for updates
 * @return \TypeRocket\Elements\BaseForm|\App\Elements\Form
 * @throws Exception
 */
function tr_form_setup(string $modelClass, array $overrides = [], $resource = null, string $action = null, int $itemId = null) 
{
    if (!class_exists($modelClass)) {
        throw new Exception("Model class {$modelClass} does not exist");
    }

    // Get form class from config or use default
    $form_class = \TypeRocket\Core\Config::get('app.class.form', \TypeRocket\Elements\BaseForm::class);
    $form = new $form_class($resource, $action, $itemId, $modelClass);
    
    // Set up tabs if configured
    if (isset($overrides['tabs']) && $overrides['tabs'] !== false) {
        $tabs = tr_setup_form_tabs($modelClass, $overrides['tabs'] ?? [], $form);
        return $tabs; // Return tabs instead of form when tabs are used
    }
    
    // If no tabs, set up fields directly on form
    $fields = tr_infer_form_fields($modelClass, $overrides['fields'] ?? []);
    tr_add_fields_to_form($form, $fields);
    
    return $form;
}

/**
 * Sets up form tabs with reflected fields
 * 
 * @param string $modelClass
 * @param array $tabConfig
 * @param \TypeRocket\Elements\BaseForm $form
 * @return \TypeRocket\Elements\Tabs
 */
function tr_setup_form_tabs(string $modelClass, array $tabConfig, $form): \TypeRocket\Elements\Tabs 
{
    $tabs = \TypeRocket\Elements\Tabs::new();
    
    // Get default tab configuration
    $defaultTabs = tr_infer_form_tabs($modelClass);
    
    // Merge with overrides
    $finalTabs = array_merge($defaultTabs, $tabConfig);
    
    foreach ($finalTabs as $tabKey => $tabData) {
        $title = $tabData['title'] ?? ucfirst($tabKey);
        $icon = $tabData['icon'] ?? null;
        $fields = $tabData['fields'] ?? [];
        
        // Infer fields if not explicitly provided
        if (empty($fields)) {
            $fields = tr_infer_form_fields($modelClass, [], $tabKey);
        }
        
        // Create form elements for this tab
        $formElements = [];
        foreach ($fields as $fieldName => $fieldConfig) {
            $formElements[] = tr_create_form_field($form, $fieldName, $fieldConfig);
        }
        
        // Add tab with fields
        if ($icon) {
            $tabs->tab($title, $icon, $formElements);
        } else {
            $tabs->tab($title, $formElements);
        }
    }
    
    return $tabs;
}

/**
 * Infers form tabs from model reflection
 * 
 * @param string $modelClass
 * @return array
 */
function tr_infer_form_tabs(string $modelClass): array 
{
    $model = new $modelClass;
    $tabs = [];
    
    // Check for static tabs configuration
    if (property_exists($modelClass, 'formTabs')) {
        return $modelClass::$formTabs;
    }
    
    // Default tab structure based on field types and relationships
    $fillable = $model->getFillableFields();
    $relationships = tr_get_model_relationships($model);
    
    // Basic tab with core fields
    $basicFields = array_slice($fillable, 0, 8); // First 8 fillable fields
    if (!empty($basicFields)) {
        $tabs['basic'] = [
            'title' => 'Basic Information',
            'icon' => 'admin-generic',
            'fields' => array_fill_keys($basicFields, [])
        ];
    }
    
    // Meta/Advanced tab for remaining fields
    $metaFields = array_slice($fillable, 8);
    if (!empty($metaFields)) {
        $tabs['advanced'] = [
            'title' => 'Advanced',
            'icon' => 'admin-tools',
            'fields' => array_fill_keys($metaFields, [])
        ];
    }
    
    // Relationships tab
    if (!empty($relationships)) {
        $tabs['relationships'] = [
            'title' => 'Relationships',
            'icon' => 'admin-links',
            'fields' => array_fill_keys(array_keys($relationships), [])
        ];
    }
    
    return $tabs;
}

/**
 * Infers form fields from model reflection
 * 
 * @param string $modelClass
 * @param array $overrides
 * @param string|null $context Context for field filtering (tab name, etc.)
 * @return array
 */
function tr_infer_form_fields(string $modelClass, array $overrides = [], ?string $context = null): array 
{
    $model = new $modelClass;
    $fields = [];
    
    // Get model properties
    $fillable = $model->getFillableFields();
    $casts = property_exists($model, 'cast') ? $model->cast : [];
    $formats = property_exists($model, 'format') ? $model->format : [];
    $relationships = tr_get_model_relationships($model) ?? [];
    
    // Check for static form configuration
    if (property_exists($modelClass, 'formFields')) {
        $staticFields = $modelClass::$formFields;
        if ($context && isset($staticFields[$context])) {
            $staticFields = $staticFields[$context];
        }
        $fields = array_merge($fields, $staticFields);
    }
    
    // Process fillable fields
    foreach ($fillable as $fieldName) {
        if (isset($overrides[$fieldName])) {
            $fields[$fieldName] = $overrides[$fieldName];
            continue;
        }
        
        $fieldConfig = tr_infer_field_config($fieldName, $casts, $formats);
        $fields[$fieldName] = $fieldConfig;
    }
    
    // Add relationship fields
    foreach ($relationships as $relName => $relConfig) {
        if (isset($overrides[$relName])) {
            $fields[$relName] = $overrides[$relName];
            continue;
        }
        
        $fields[$relName] = tr_infer_relationship_field_config($relName, $relConfig);
    }
    
    // Apply overrides
    foreach ($overrides as $fieldName => $config) {
        $fields[$fieldName] = $config;
    }
    
    return $fields;
}

/**
 * Infers field configuration from name and casts
 * 
 * @param string $fieldName
 * @param array $casts
 * @param array $formats
 * @return array
 */
function tr_infer_field_config(string $fieldName, ?array $casts = [], ?array $formats = []): array 
{
    $config = [
        'type' => 'text',
        'label' => tr_humanize_field_name($fieldName),
        'attributes' => [],
        'settings' => []
    ];
    
    // Determine field type from name patterns
    $lowerName = strtolower($fieldName);
    
    // Email fields
    if (strpos($lowerName, 'email') !== false) {
        $config['type'] = 'email';
        $config['attributes']['type'] = 'email';
    }
    // URL fields
    elseif (strpos($lowerName, 'url') !== false || strpos($lowerName, 'link') !== false) {
        $config['type'] = 'url';
        $config['attributes']['type'] = 'url';
    }
    // Phone fields
    elseif (strpos($lowerName, 'phone') !== false || strpos($lowerName, 'tel') !== false) {
        $config['type'] = 'tel';
        $config['attributes']['type'] = 'tel';
    }
    // Password fields
    elseif (strpos($lowerName, 'password') !== false) {
        $config['type'] = 'password';
        $config['attributes']['type'] = 'password';
    }
    // Image/media fields
    elseif (strpos($lowerName, 'image') !== false || strpos($lowerName, 'photo') !== false || strpos($lowerName, 'avatar') !== false) {
        $config['type'] = 'image';
    }
    // Gallery fields
    elseif (strpos($lowerName, 'gallery') !== false) {
        $config['type'] = 'gallery';
    }
    // File fields
    elseif (strpos($lowerName, 'file') !== false || strpos($lowerName, 'attachment') !== false) {
        $config['type'] = 'file';
    }
    // Date fields
    elseif (strpos($lowerName, 'date') !== false && strpos($lowerName, 'update') === false) {
        $config['type'] = 'date';
    }
    // Datetime fields
    elseif (strpos($lowerName, 'datetime') !== false || strpos($lowerName, 'timestamp') !== false) {
        $config['type'] = 'datetime';
    }
    // Time fields
    elseif (strpos($lowerName, 'time') !== false && strpos($lowerName, 'datetime') === false) {
        $config['type'] = 'time';
    }
    // Color fields
    elseif (strpos($lowerName, 'color') !== false || strpos($lowerName, 'colour') !== false) {
        $config['type'] = 'color';
    }
    // Number fields
    elseif (strpos($lowerName, 'count') !== false || strpos($lowerName, 'number') !== false || strpos($lowerName, 'qty') !== false || strpos($lowerName, 'quantity') !== false) {
        $config['type'] = 'number';
        $config['attributes']['type'] = 'number';
    }
    // Large text fields
    elseif (strpos($lowerName, 'description') !== false || strpos($lowerName, 'content') !== false || strpos($lowerName, 'bio') !== false || strpos($lowerName, 'about') !== false) {
        $config['type'] = 'textarea';
    }
    // Rich text fields
    elseif (strpos($lowerName, 'body') !== false || strpos($lowerName, 'rich') !== false) {
        $config['type'] = 'editor';
    }
    // Toggle/Boolean fields
    elseif (strpos($lowerName, 'is_') === 0 || strpos($lowerName, 'has_') === 0 || strpos($lowerName, 'active') !== false || strpos($lowerName, 'enabled') !== false) {
        $config['type'] = 'toggle';
    }
    
    // Apply cast-based overrides
    if (isset($casts[$fieldName])) {
        $cast = $casts[$fieldName];
        switch ($cast) {
            case 'bool':
            case 'boolean':
                $config['type'] = 'toggle';
                break;
            case 'int':
            case 'integer':
                $config['type'] = 'number';
                $config['attributes']['type'] = 'number';
                break;
            case 'array':
            case 'json':
                $config['type'] = 'items';
                break;
            case 'date':
                $config['type'] = 'date';
                break;
            case 'datetime':
                $config['type'] = 'datetime';
                break;
        }
    }
    
    return $config;
}

/**
 * Infers relationship field configuration
 * 
 * @param string $relationshipName
 * @param array $relationshipConfig
 * @return array
 */
function tr_infer_relationship_field_config(string $relationshipName, array $relationshipConfig): array 
{
    $config = [
        'type' => 'search',
        'label' => tr_humanize_field_name($relationshipName),
        'attributes' => [],
        'settings' => []
    ];
    
    $relationType = $relationshipConfig['type'] ?? 'belongsTo';
    
    switch ($relationType) {
        case 'hasMany':
        case 'belongsToMany':
            $config['settings']['multiple'] = true;
            break;
        case 'belongsTo':
        case 'hasOne':
        default:
            $config['settings']['multiple'] = false;
            break;
    }
    
    // Set post type options if it's a post relationship
    if (isset($relationshipConfig['model']) && strpos($relationshipConfig['model'], 'WPPost') !== false) {
        $config['settings']['post_type'] = $relationshipConfig['post_type'] ?? 'any';
    }
    
    return $config;
}

/**
 * Gets model relationships via reflection
 * 
 * @param \TypeRocket\Models\Model $model
 * @return array
 */
function tr_get_model_relationships($model): ?array 
{
    $relationships = [];
    
    // Check for static relationships property
    if (property_exists($model, 'relationships')) {
        return $model->relationships;
    }
    
    // Try to detect relationships through method reflection
    $reflection = new \ReflectionClass($model);
    $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
    
    foreach ($methods as $method) {
        $methodName = $method->getName();
        
        // Skip getters, setters, and other non-relationship methods
        if (strpos($methodName, 'get') === 0 || strpos($methodName, 'set') === 0 || 
            in_array($methodName, ['save', 'delete', 'create', 'update', 'find', 'where'])) {
            continue;
        }
        
        // Check if method returns a relationship
        try {
            $docComment = $method->getDocComment();
            if ($docComment && (strpos($docComment, '@return') !== false)) {
                // Simple heuristic - if it looks like a relationship method
                if (preg_match('/belongsTo|hasMany|hasOne|belongsToMany/', $methodName)) {
                    $relationships[$methodName] = [
                        'type' => 'belongsTo', // Default
                        'method' => $methodName
                    ];
                }
            }
        } catch (Exception $e) {
            // Skip if we can't analyze the method
            continue;
        }
    }
    
    return $relationships;
}

/**
 * Creates a form field element
 * 
 * @param \TypeRocket\Elements\BaseForm $form
 * @param string $fieldName
 * @param array $fieldConfig
 * @return \TypeRocket\Elements\Fields\Field
 */
function tr_create_form_field($form, string $fieldName, array $fieldConfig) 
{
    $type = $fieldConfig['type'] ?? 'text';
    $label = $fieldConfig['label'] ?? tr_humanize_field_name($fieldName);
    $attributes = $fieldConfig['attributes'] ?? [];
    $settings = $fieldConfig['settings'] ?? [];
    
    // Create field based on type
    switch ($type) {
        case 'text':
            $field = $form->text($label, $attributes);
            break;
        case 'email':
            $field = $form->email($label, $attributes);
            break;
        case 'url':
            $field = $form->url($label, $attributes);
            break;
        case 'tel':
            $field = $form->text($label, array_merge(['type' => 'tel'], $attributes));
            break;
        case 'password':
            $field = $form->password($label, $attributes);
            break;
        case 'number':
            $field = $form->number($label, $attributes);
            break;
        case 'textarea':
            $field = $form->textarea($label, $attributes);
            break;
        case 'editor':
            $field = $form->editor($label, $attributes);
            break;
        case 'toggle':
            $field = $form->toggle($label, $attributes);
            break;
        case 'image':
            $field = $form->image($label, $attributes);
            break;
        case 'gallery':
            $field = $form->gallery($label, $attributes);
            break;
        case 'file':
            $field = $form->file($label, $attributes);
            break;
        case 'date':
            $field = $form->date($label, $attributes);
            break;
        case 'datetime':
            $field = $form->datetime($label, $attributes);
            break;
        case 'time':
            $field = $form->time($label, $attributes);
            break;
        case 'color':
            $field = $form->color($label, $attributes);
            break;
        case 'search':
            $field = $form->search($label, $attributes);
            if (isset($settings['post_type'])) {
                $field->setPostTypeOptions($settings['post_type']);
            }
            if (isset($settings['multiple']) && $settings['multiple']) {
                $field->setMultiple();
            }
            break;
        case 'items':
            $field = $form->items($label, $attributes);
            break;
        default:
            $field = $form->text($label, $attributes);
            break;
    }
    
    // Apply additional settings
    foreach ($settings as $setting => $value) {
        if (method_exists($field, $setting)) {
            $field->$setting($value);
        } elseif (method_exists($field, 'set' . ucfirst($setting))) {
            $method = 'set' . ucfirst($setting);
            $field->$method($value);
        }
    }
    
    // Set field name (dot notation support)
    $field->setName($fieldName);
    
    return $field;
}

/**
 * Adds multiple fields to a form
 * 
 * @param \TypeRocket\Elements\BaseForm $form
 * @param array $fields
 * @return \TypeRocket\Elements\BaseForm
 */
function tr_add_fields_to_form($form, array $fields) 
{
    foreach ($fields as $fieldName => $fieldConfig) {
        $field = tr_create_form_field($form, $fieldName, $fieldConfig);
        // Fields are automatically added to form when created with form context
    }
    
    return $form;
}

/**
 * Humanizes field names for labels
 * 
 * @param string $fieldName
 * @return string
 */
function tr_humanize_field_name(string $fieldName): string 
{
    // Remove underscores and capitalize words
    $humanized = str_replace(['_', '-'], ' ', $fieldName);
    $humanized = ucwords($humanized);
    
    // Handle common abbreviations
    $humanized = str_replace([' Id', ' Url', ' Api ', ' Seo'], [' ID', ' URL', ' API ', ' SEO'], $humanized);
    
    return $humanized;
}

/**
 * Sets up form validation rules based on model
 * 
 * @param string $modelClass
 * @param array $overrides
 * @return array
 */
function tr_infer_validation_rules(string $modelClass, array $overrides = []): array 
{
    $model = new $modelClass;
    $rules = [];
    
    // Check for static validation rules
    if (property_exists($model, 'rules')) {
        $rules = $model->rules;
    }
    
    // Infer basic validation from field names and types
    $fillable = $model->getFillableFields();
    $casts = property_exists($model, 'cast') ? $model->cast : [];
    
    foreach ($fillable as $fieldName) {
        if (isset($overrides[$fieldName])) {
            $rules[$fieldName] = $overrides[$fieldName];
            continue;
        }
        
        $fieldRules = [];
        $lowerName = strtolower($fieldName);
        
        // Email validation
        if (strpos($lowerName, 'email') !== false) {
            $fieldRules[] = 'email';
        }
        
        // URL validation
        if (strpos($lowerName, 'url') !== false) {
            $fieldRules[] = 'url';
        }
        
        // Required fields (common patterns)
        if (in_array($lowerName, ['name', 'title', 'email']) || strpos($lowerName, 'required') !== false) {
            $fieldRules[] = 'required';
        }
        
        // Cast-based validation
        if (isset($casts[$fieldName])) {
            $cast = $casts[$fieldName];
            switch ($cast) {
                case 'int':
                case 'integer':
                    $fieldRules[] = 'numeric';
                    break;
                case 'bool':
                case 'boolean':
                    $fieldRules[] = 'boolean';
                    break;
            }
        }
        
        if (!empty($fieldRules)) {
            $rules[$fieldName] = implode('|', $fieldRules);
        }
    }
    
    return array_merge($rules, $overrides);
}

/**
 * Quick helper for basic form setup without tabs
 * 
 * @param string $modelClass
 * @param array $fieldOverrides
 * @param mixed $resource
 * @param string|null $action
 * @param int|null $itemId
 * @return \TypeRocket\Elements\BaseForm
 */
function tr_form_basic(string $modelClass, array $fieldOverrides = [], $resource = null, string $action = null, int $itemId = null) 
{
    return tr_form_setup($modelClass, [
        'tabs' => false,
        'fields' => $fieldOverrides
    ], $resource, $action, $itemId);
}

/**
 * Quick helper for tabbed form setup
 * 
 * @param string $modelClass
 * @param array $tabOverrides
 * @param mixed $resource
 * @param string|null $action
 * @param int|null $itemId
 * @return \TypeRocket\Elements\Tabs
 */
function tr_form_tabs(string $modelClass, array $tabOverrides = [], $resource = null, string $action = null, int $itemId = null) 
{
    return tr_form_setup($modelClass, [
        'tabs' => $tabOverrides
    ], $resource, $action, $itemId);
}