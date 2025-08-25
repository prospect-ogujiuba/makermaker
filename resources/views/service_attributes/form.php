<?php

/**
 * ServiceAttributes Form
 * File: resources/views/admin/service_attributes/form.php
 * 
 * Form for creating and editing service attributes
 */

use MakerMaker\Models\ServiceAttributes;
use MakerMaker\Models\Service;

/** @var \TypeRocket\Elements\Form $form */
/** @var \MakerMaker\Models\ServiceAttributes $serviceAttribute */
/** @var string $button */

echo $form->open();

/**
 * Basic Information Section
 */
$basicInfo = $form->fieldset(
    'Attribute Information',
    'Core attribute details and type configuration',
    [
        $form->row()
            ->withColumn(
                $form->select('Service')
                    ->setName('service_id')
                    ->setModelOptions(Service::class, 'name')
                    ->setHelp('Select the service this attribute belongs to')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->text('Attribute Name')
                    ->setName('attribute_name')
                    ->setHelp('Name of the attribute (e.g., "max_users", "warranty_months")')
                    ->setAttribute('maxlength', '100')
                    ->markLabelRequired()
            ),

        $form->row()
            ->withColumn(
                $form->select('Attribute Type')
                    ->setName('attribute_type')
                    ->setOptions([
                        'Text' => 'text',
                        'Number' => 'number',
                        'Boolean (Yes/No)' => 'boolean',
                        'JSON Data' => 'json',
                        'URL' => 'url',
                        'Email Address' => 'email'
                    ])
                    ->setHelp('Data type for this attribute')
                    ->markLabelRequired()
                    ->setAttribute('id', 'attribute_type_select')
            )
            ->withColumn(
                $form->text('Display Order')
                    ->setName('display_order')
                    ->setType('number')
                    ->setAttribute('min', '0')
                    ->setDefault(0)
                    ->setHelp('Display order (0 = auto-assign)')
            )
    ]
);

/**
 * Value and Configuration Section
 */
$valueConfig = $form->fieldset(
    'Value & Configuration',
    'Attribute value and client configuration settings',
    [
        $form->textarea('Attribute Value')
            ->setName('attribute_value')
            ->setHelp('The value for this attribute. Format depends on attribute type.')
            ->setAttribute('id', 'attribute_value_field'),

        $form->toggle('Configurable by Clients')
            ->setName('is_configurable')
            ->setText('Clients can configure this attribute when ordering')
            ->setHelp('If enabled, clients can modify this attribute value during service ordering'),

        // Dynamic help text based on type
        '<div id="type-help-text" class="type-help-container"></div>'
    ]
);

// Save button
$save = $form->save($button ?? 'Save Service Attribute');

// Create tabs layout
$tabs = \TypeRocket\Elements\Tabs::new()
    ->setFooter($save)
    ->layoutLeft();

// Add tabs
$tabs->tab('Attribute Details', 'admin-customizer', [$basicInfo])
    ->setDescription('Attribute name, type, and service');

$tabs->tab('Value & Settings', 'admin-settings', [$valueConfig])
    ->setDescription('Attribute value and configuration options');

// Render the tabbed interface
$tabs->render();

echo $form->close();
?>

<style>
/* Attribute Type Badges */
.type-text { background-color: #007cba; color: white; }
.type-number { background-color: #00a32a; color: white; }
.type-boolean { background-color: #dba617; color: white; }
.type-json { background-color: #8c5d2b; color: white; }
.type-url { background-color: #d63638; color: white; }
.type-email { background-color: #6c757d; color: white; }
.type-default { background-color: #6c757d; color: white; }

/* Value Display Styles */
.boolean-value.bool-true { color: #00a32a; font-weight: bold; }
.boolean-value.bool-false { color: #d63638; font-weight: bold; }
.number-value { font-family: monospace; color: #007cba; }
.url-value { color: #d63638; }
.email-value { color: #8c5d2b; }
.json-value { font-family: monospace; color: #6c757d; }
.text-value { color: #333; }

/* Status Badges */
.badge { padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
.badge-success { background-color: #00a32a; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.badge-danger { background-color: #d63638; color: white; }
.text-muted { color: #6c757d !important; }

/* Form Enhancement */
.type-help-container {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin: 10px 0;
    display: none;
}

.type-help-container.show {
    display: block;
}

.value-preview {
    background: #f0f8ff;
    border: 1px solid #b3d9ff;
    border-radius: 4px;
    padding: 10px;
    margin: 10px 0;
}

.validation-status {
    display: inline-block;
    margin-left: 10px;
    font-size: 12px;
}

.validation-status.valid { color: #00a32a; }
.validation-status.invalid { color: #d63638; }

.display-order { font-weight: bold; }
</style>

<script>
jQuery(document).ready(function($) {
    const typeSelect = $('#attribute_type_select');
    const valueField = $('#attribute_value_field');
    const helpContainer = $('#type-help-text');
    
    // Type-specific help and examples
    const typeHelp = {
        'text': {
            help: 'Plain text value. Can contain letters, numbers, and special characters.',
            example: 'Premium Package',
            placeholder: 'Enter text value...'
        },
        'number': {
            help: 'Numeric value. Can be integer or decimal.',
            example: '150.50',
            placeholder: 'Enter numeric value...'
        },
        'boolean': {
            help: 'True/false value. Use: true/false, yes/no, 1/0, or on/off.',
            example: 'true',
            placeholder: 'true, false, yes, no, 1, or 0'
        },
        'json': {
            help: 'JSON formatted data. Must be valid JSON syntax.',
            example: '{"max_users": 100, "features": ["ssl", "backup"]}',
            placeholder: 'Enter valid JSON...'
        },
        'url': {
            help: 'Valid URL address. Should start with http:// or https://.',
            example: 'https://example.com/documentation',
            placeholder: 'https://example.com'
        },
        'email': {
            help: 'Valid email address.',
            example: 'support@example.com',
            placeholder: 'user@example.com'
        }
    };
    
    // Update help text and field placeholder based on type
    function updateTypeHelp() {
        const selectedType = typeSelect.val();
        
        if (selectedType && typeHelp[selectedType]) {
            const help = typeHelp[selectedType];
            helpContainer.html(
                '<strong>Type: ' + selectedType.charAt(0).toUpperCase() + selectedType.slice(1) + '</strong><br>' +
                help.help + '<br>' +
                '<strong>Example:</strong> <code>' + help.example + '</code>'
            ).addClass('show');
            
            valueField.attr('placeholder', help.placeholder);
            
            // Special handling for different types
            if (selectedType === 'json') {
                valueField.attr('rows', 6);
            } else if (selectedType === 'boolean') {
                valueField.attr('rows', 2);
            } else {
                valueField.attr('rows', 4);
            }
        } else {
            helpContainer.removeClass('show');
            valueField.attr('placeholder', 'Enter attribute value...');
        }
    }
    
    // Initial help text update
    updateTypeHelp();
    
    // Update help when type changes
    typeSelect.on('change', updateTypeHelp);
    
    // Real-time validation
    let validationTimeout;
    valueField.on('input', function() {
        const value = $(this).val();
        const type = typeSelect.val();
        
        // Clear previous timeout
        clearTimeout(validationTimeout);
        
        // Remove existing validation status
        $('.validation-status').remove();
        
        if (!value || !type) return;
        
        // Add validation status after a delay
        validationTimeout = setTimeout(function() {
            validateValue(type, value);
        }, 500);
    });
    
    // Validate attribute value
    function validateValue(type, value) {
        if (!value) return;
        
        let isValid = true;
        let message = '';
        
        switch(type) {
            case 'number':
                isValid = !isNaN(parseFloat(value)) && isFinite(value);
                message = isValid ? 'Valid number' : 'Invalid number format';
                break;
                
            case 'boolean':
                const boolValues = ['true', 'false', 'yes', 'no', '1', '0', 'on', 'off'];
                isValid = boolValues.includes(value.toLowerCase().trim());
                message = isValid ? 'Valid boolean' : 'Use: true/false, yes/no, 1/0, on/off';
                break;
                
            case 'json':
                try {
                    JSON.parse(value);
                    isValid = true;
                    message = 'Valid JSON';
                } catch(e) {
                    isValid = false;
                    message = 'Invalid JSON format';
                }
                break;
                
            case 'url':
                const urlPattern = /^https?:\/\/.+/i;
                isValid = urlPattern.test(value);
                message = isValid ? 'Valid URL' : 'URL should start with http:// or https://';
                break;
                
            case 'email':
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                isValid = emailPattern.test(value);
                message = isValid ? 'Valid email' : 'Invalid email format';
                break;
                
            case 'text':
            default:
                isValid = true;
                message = 'Valid text';
                break;
        }
        
        // Show validation status
        const statusClass = isValid ? 'valid' : 'invalid';
        const statusIcon = isValid ? '✓' : '✗';
        valueField.after(
            '<span class="validation-status ' + statusClass + '">' + 
            statusIcon + ' ' + message + '</span>'
        );
    }
    
    // Form validation
    $('form').on('submit', function(e) {
        const serviceId = $('select[name="service_id"]').val();
        const attributeName = $('input[name="attribute_name"]').val().trim();
        const attributeType = typeSelect.val();
        const attributeValue = valueField.val().trim();
        
        if (!serviceId) {
            e.preventDefault();
            alert('Please select a service.');
            $('select[name="service_id"]').focus();
            return false;
        }
        
        if (!attributeName) {
            e.preventDefault();
            alert('Please enter an attribute name.');
            $('input[name="attribute_name"]').focus();
            return false;
        }
        
        if (!attributeType) {
            e.preventDefault();
            alert('Please select an attribute type.');
            typeSelect.focus();
            return false;
        }
        
        // Validate value if provided
        if (attributeValue) {
            let isValid = true;
            
            switch(attributeType) {
                case 'number':
                    if (isNaN(parseFloat(attributeValue)) || !isFinite(attributeValue)) {
                        isValid = false;
                        alert('Please enter a valid number.');
                    }
                    break;
                    
                case 'json':
                    try {
                        JSON.parse(attributeValue);
                    } catch(e) {
                        isValid = false;
                        alert('Please enter valid JSON format.');
                    }
                    break;
                    
                case 'url':
                    if (!/^https?:\/\/.+/i.test(attributeValue)) {
                        isValid = false;
                        alert('Please enter a valid URL starting with http:// or https://');
                    }
                    break;
                    
                case 'email':
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(attributeValue)) {
                        isValid = false;
                        alert('Please enter a valid email address.');
                    }
                    break;
            }
            
            if (!isValid) {
                e.preventDefault();
                valueField.focus();
                return false;
            }
        }
        
        // Validate display order
        const displayOrder = parseInt($('input[name="display_order"]').val());
        if (isNaN(displayOrder) || displayOrder < 0) {
            $('input[name="display_order"]').val(0);
        }
    });
    
    // Clean attribute name - allow only valid characters
    $('input[name="attribute_name"]').on('input', function() {
        let value = $(this).val();
        // Allow letters, numbers, underscores, spaces, hyphens
        value = value.replace(/[^a-zA-Z0-9_\s-]/g, '');
        $(this).val(value);
    });
});
</script>