<?php

/**
 * ServiceBundles Form
 * File: resources/views/admin/service_bundles/form.php
 * 
 * Form for creating and editing service bundles
 */

use MakerMaker\Models\ServiceBundles;
use MakerMaker\Models\Service;

/** @var \TypeRocket\Elements\Form $form */
/** @var \MakerMaker\Models\ServiceBundles $serviceBundle */
/** @var string $button */

echo $form->open();

/**
 * Basic Information Section
 */
$basicInfo = $form->fieldset(
    'Bundle Information',
    'Core bundle details and type configuration',
    [
        $form->row()
            ->withColumn(
                $form->text('Bundle Name')
                    ->setName('name')
                    ->setHelp('Name of the service bundle')
                    ->setAttribute('maxlength', '200')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->text('URL Slug')
                    ->setName('slug')
                    ->setHelp('URL-friendly slug (auto-generated if empty)')
                    ->setAttribute('maxlength', '200')
                    ->markLabelRequired()
            ),

        $form->row()
            ->withColumn(
                $form->select('Bundle Type')
                    ->setName('bundle_type')
                    ->setOptions([
                        'Service Package' => 'package',
                        'Addon Group' => 'addon_group',
                        'Maintenance Plan' => 'maintenance_plan',
                        'Enterprise Solution' => 'enterprise'
                    ])
                    ->setHelp('Type of bundle this represents')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->toggle('Active Status')
                    ->setName('is_active')
                    ->setText('Bundle is active and available')
                    ->setHelp('Inactive bundles are hidden from selection')
                    ->setDefault(true)
            ),

        $form->textarea('Description')
            ->setName('description')
            ->setHelp('Detailed description of what this bundle includes and provides')
    ]
);

/**
 * Pricing Section
 */
$pricing = $form->fieldset(
    'Pricing Configuration',
    'Bundle pricing, discounts, and commitment terms',
    [
        $form->row()
            ->withColumn(
                $form->text('Base Price')
                    ->setName('base_price')
                    ->setType('number')
                    ->setAttribute('step', '0.01')
                    ->setAttribute('min', '0')
                    ->setHelp('Base price for this bundle (leave empty for custom pricing)')
            )
            ->withColumn(
                $form->text('Discount Percentage')
                    ->setName('discount_percentage')
                    ->setType('number')
                    ->setAttribute('step', '0.01')
                    ->setAttribute('min', '0')
                    ->setAttribute('max', '100')
                    ->setDefault(0)
                    ->setHelp('Discount percentage offered in this bundle')
            ),

        $form->text('Minimum Commitment (Months)')
            ->setName('min_commitment_months')
            ->setType('number')
            ->setAttribute('min', '0')
            ->setHelp('Minimum commitment period in months (0 = no commitment)')
    ]
);

/**
 * Bundle Services Section
 */
$bundleServices = $form->fieldset(
    'Bundle Services',
    'Services included in this bundle with quantities and options',
    [
        $form->repeater('Included Services')
            ->setName('bundle_services')
            ->setHelp('Define which services are included in this bundle')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->select('Service')
                            ->setName('service_id')
                            ->setModelOptions(Service::class, 'name')
                            ->setHelp('Select a service to include in this bundle')
                            ->markLabelRequired()
                    )
                    ->withColumn(
                        $form->text('Quantity')
                            ->setName('quantity')
                            ->setType('number')
                            ->setAttribute('min', '1')
                            ->setDefault(1)
                            ->setHelp('Number of units of this service')
                    ),

                $form->row()
                    ->withColumn(
                        $form->toggle('Optional Service')
                            ->setName('is_optional')
                            ->setText('This service is optional in the bundle')
                            ->setHelp('Optional services can be removed by clients')
                    )
                    ->withColumn(
                        $form->text('Sort Order')
                            ->setName('sort_order')
                            ->setType('number')
                            ->setAttribute('min', '0')
                            ->setDefault(0)
                            ->setHelp('Display order in bundle listing')
                    )
            ])
    ]
);

// Save button
$save = $form->save($button ?? 'Save Service Bundle');

// Create tabs layout
$tabs = \TypeRocket\Elements\Tabs::new()
    ->setFooter($save)
    ->layoutLeft();

// Add tabs
$tabs->tab('Bundle Information', 'admin-post', [$basicInfo])
    ->setDescription('Bundle name, type, and description');

$tabs->tab('Pricing & Terms', 'money-alt', [$pricing])
    ->setDescription('Pricing, discounts, and commitment terms');

$tabs->tab('Included Services', 'admin-tools', [$bundleServices])
    ->setDescription('Services included in this bundle');

// Render the tabbed interface
$tabs->render();

echo $form->close();
?>

<style>
/* Bundle Type Badges */
.bundle-type-package { background-color: #007cba; color: white; }
.bundle-type-addon { background-color: #00a32a; color: white; }
.bundle-type-maintenance { background-color: #dba617; color: white; }
.bundle-type-enterprise { background-color: #d63638; color: white; }
.bundle-type-default { background-color: #6c757d; color: white; }

/* Status and Info Styles */
.badge { padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
.badge-success { background-color: #00a32a; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.badge-warning { background-color: #dba617; color: white; }
.text-muted { color: #6c757d !important; }
.text-success { color: #00a32a !important; }

/* Pricing Info */
.pricing-info { text-align: left; }
.discount-info { color: #d63638; font-weight: bold; }
.services-count { display: inline-block; }
.commitment-info { font-size: 12px; }
.bundle-description { font-style: italic; max-width: 200px; display: inline-block; }

/* Form Enhancement */
.pricing-calculator {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin: 10px 0;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Auto-generate slug from name if slug is empty
    $('input[name="name"]').on('input', function() {
        const slugField = $('input[name="slug"]');
        
        // Only auto-generate if slug field is empty
        if (!slugField.val()) {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .trim()
                .replace(/[^a-z0-9-]/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
            
            slugField.val(slug);
        }
    });

    // Bundle type specific behavior
    $('select[name="bundle_type"]').on('change', function() {
        const bundleType = $(this).val();
        const commitmentField = $('input[name="min_commitment_months"]');
        const basePriceField = $('input[name="base_price"]');
        
        // Auto-suggest values based on bundle type
        switch(bundleType) {
            case 'maintenance_plan':
                if (!commitmentField.val() || commitmentField.val() == '0') {
                    commitmentField.val('12');
                }
                break;
            case 'enterprise':
                // Enterprise bundles might need custom handling
                break;
            case 'addon_group':
                commitmentField.val('0'); // Addons usually don't have commitment
                break;
        }
    });

    // Pricing calculator
    function calculatePricing() {
        const basePrice = parseFloat($('input[name="base_price"]').val()) || 0;
        const discountPercentage = parseFloat($('input[name="discount_percentage"]').val()) || 0;
        
        if (basePrice > 0 && discountPercentage > 0) {
            const discountAmount = (basePrice * discountPercentage) / 100;
            const finalPrice = basePrice - discountAmount;
            
            // Show pricing calculation (if we have a display area)
            if (!$('.pricing-calculator').length) {
                $('input[name="discount_percentage"]').after(
                    '<div class="pricing-calculator">' +
                    '<strong>Pricing Preview:</strong><br>' +
                    'Base Price: $<span id="calc-base">0.00</span><br>' +
                    'Discount: -$<span id="calc-discount">0.00</span> (<span id="calc-percent">0</span>%)<br>' +
                    'Final Price: $<span id="calc-final">0.00</span>' +
                    '</div>'
                );
            }
            
            $('#calc-base').text(basePrice.toFixed(2));
            $('#calc-discount').text(discountAmount.toFixed(2));
            $('#calc-percent').text(discountPercentage.toFixed(1));
            $('#calc-final').text(finalPrice.toFixed(2));
        } else {
            $('.pricing-calculator').remove();
        }
    }
    
    // Update pricing calculation on change
    $('input[name="base_price"], input[name="discount_percentage"]').on('input', calculatePricing);
    
    // Initial calculation
    calculatePricing();
    
    // Form validation
    $('form').on('submit', function(e) {
        const name = $('input[name="name"]').val().trim();
        const slug = $('input[name="slug"]').val().trim();
        const bundleType = $('select[name="bundle_type"]').val();
        
        if (!name) {
            e.preventDefault();
            alert('Please enter a bundle name.');
            $('input[name="name"]').focus();
            return false;
        }
        
        if (!slug) {
            e.preventDefault();
            alert('Please enter a slug for this bundle.');
            $('input[name="slug"]').focus();
            return false;
        }
        
        if (!bundleType) {
            e.preventDefault();
            alert('Please select a bundle type.');
            $('select[name="bundle_type"]').focus();
            return false;
        }
        
        // Validate discount percentage
        const discount = parseFloat($('input[name="discount_percentage"]').val());
        if (discount < 0 || discount > 100) {
            e.preventDefault();
            alert('Discount percentage must be between 0 and 100.');
            $('input[name="discount_percentage"]').focus();
            return false;
        }
        
        // Validate commitment months
        const commitment = parseInt($('input[name="min_commitment_months"]').val());
        if (isNaN(commitment) || commitment < 0) {
            $('input[name="min_commitment_months"]').val(0);
        }
    });

    // Service selection validation in repeater
    $(document).on('change', 'select[name*="[service_id]"]', function() {
        const selectedValues = [];
        const currentValue = $(this).val();
        
        // Check for duplicates
        $('select[name*="[service_id]"]').each(function() {
            const value = $(this).val();
            if (value && selectedValues.includes(value) && value === currentValue) {
                alert('This service is already selected in another row.');
                $(this).val('').trigger('change');
                return false;
            }
            if (value) {
                selectedValues.push(value);
            }
        });
    });
});
</script>