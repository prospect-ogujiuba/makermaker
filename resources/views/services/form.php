<?php

/**
 * Enhanced Service Form - Complete Data Management
 * File: resources/views/admin/service/form.php
 * 
 * This form handles all Service relationships and properties organized into logical tabs
 * based on the actual database structure. Each tab corresponds to a related data structure
 * allowing complete control of all service aspects from this single interface.
 */

use MakerMaker\Models\Service;

/** @var \App\Elements\Form $form */
/** @var \MakerMaker\Models\Service $service */
/** @var string $button */

// Helper function to get relationship options
function getRelationshipOptions($modelClass)
{
    if (!class_exists($modelClass)) {
        return [];
    }
    return $modelClass::new()->findAll()->get()->pluck('title', 'id')->toArray() ?? [];
}

echo $form->open();

/**
 * Tab 1: Service Overview
 * Core service information based on wp_b2bcnc_services main table
 */
$overview = $form->fieldset(
    'Service Overview',
    'Core service information and metadata from main services table',
    [
        $form->row()
            ->withColumn(
                $form->text('Service Name')
                    ->setName('name')
                    ->setHelp('Service name (max 200 characters)')
                    ->setAttribute('maxlength', '200')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->text('URL Slug')
                    ->setName('slug')
                    ->setHelp('URL-friendly slug (max 200 characters)')
                    ->setAttribute('maxlength', '200')
            ),

        $form->row()
            ->withColumn(
                $form->select('Service Category')
                    ->setName('category_id')
                    // ->setOptions(getRelationshipOptions('\MakerMaker\Models\ServiceCategory'))
                    ->setHelp('Primary service category')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->select('Service Type')
                    ->setName('service_type')
                    ->setOptions([
                        'Installation' => 'installation',
                        'Maintenance'  => 'maintenance',
                        'Hosting'      => 'hosting',
                        'Consulting'   => 'consulting',
                        'Support'      => 'support',
                        'Hybrid'       => 'hybrid',
                    ])
                    ->setHelp('Type of service being offered')
                    ->markLabelRequired()
            ),

        $form->text('Short Description')
            ->setName('short_description')
            ->setHelp('Brief description for listings (max 500 characters)')
            ->setAttribute('maxlength', '500'),

        $form->textarea('Long Description')
            ->setName('long_description')
            ->setHelp('Detailed service description with formatting'),

        $form->row()
            ->withColumn(
                $form->select('Delivery Method')
                    ->setName('delivery_method')
                    ->setOptions([
                        'On-Site' => 'onsite',
                        'Remote' => 'remote',
                        'Hybrid' => 'hybrid',
                        'Hosted' => 'hosted'
                    ])
                    ->setHelp('How this service is delivered')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->select('Complexity Level')
                    ->setName('complexity_level')
                    ->setOptions([
                        'Basic' => 'basic',
                        'Intermediate' => 'intermediate',
                        'Advanced' => 'advanced',
                        'Expert' => 'expert'
                    ])
                    ->setDefault('basic')
            ),

        $form->row()
            ->withColumn(
                $form->toggle('Site Visit Required')
                    ->setName('requires_site_visit')
                    ->setText('This service requires an on-site visit')
            )
            ->withColumn(
                $form->toggle('Supports Remote Delivery')
                    ->setName('supports_remote_delivery')
                    ->setText('Service can be delivered remotely')
            ),

        $form->row()
            ->withColumn(
                $form->toggle('Active Service')
                    ->setName('is_active')
                    ->setText('Service is active and available')
                    ->setDefault(true)
            )
            ->withColumn(
                $form->toggle('Featured Service')
                    ->setName('is_featured')
                    ->setText('Mark as featured in listings')
            ),

        $form->row()
            ->withColumn(
                $form->toggle('Requires Assessment')
                    ->setName('requires_assessment')
                    ->setText('Service requires pre-assessment')
            )
            ->withColumn(
                $form->text('Min Notice Days')
                    ->setName('min_notice_days')
                    ->setType('number')
                    ->setAttribute('min', '0')
                    ->setHelp('Minimum days notice required')
            ),

        $form->text('Estimated Duration (Hours)')
            ->setName('estimated_duration_hours')
            ->setType('number')
            ->setAttribute('min', '0')
            ->setHelp('Estimated time to complete service'),

        $form->image('Featured Image')
            ->setName('featured_image')
            ->setHelp('Primary image for this service (max 500 chars path)')
    ]
);

/**
 * Tab 2: Pricing & Financial
 * Pricing models and financial details from main services table
 */
$pricing = $form->fieldset(
    'Pricing & Financial Details',
    'Pricing models and financial configuration',
    [
        $form->row()
            ->withColumn(
                $form->select('Pricing Model')
                    ->setName('pricing_model')
                    ->setOptions([
                        'Fixed Price' => 'fixed',
                        'Hourly Rate' => 'hourly',
                        'Monthly' => 'monthly',
                        'Project-based' => 'project',
                        'Tiered' => 'tiered',
                        'Custom Quote' => 'custom'
                    ])
                    ->setHelp('How this service is priced')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->text('Base Price')
                    ->setName('base_price')
                    ->setType('number')
                    ->setAttribute('step', '0.01')
                    ->setAttribute('min', '0')
                    ->setHelp('Base price for this service (max 10,2 decimal)')
            ),

        $form->text('Hourly Rate')
            ->setName('hourly_rate')
            ->setType('number')
            ->setAttribute('step', '0.01')
            ->setAttribute('min', '0')
            ->setHelp('Hourly rate if applicable (max 8,2 decimal)')
    ]
);

/**
 * Tab 3: Pricing Tiers
 * Manage service pricing tiers (wp_b2bcnc_service_pricing_tiers)
 */
$pricingTiers = $form->fieldset(
    'Pricing Tiers',
    'Tiered pricing structure for quantity-based discounts',
    [
        $form->repeater('Service Pricing Tiers')
            ->setName('pricing_tiers')
            ->setHelp('Define different pricing tiers based on quantity or service level')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->text('Tier Name')
                            ->setName('tier_name')
                            ->setAttribute('maxlength', '100')
                            ->setHelp('Name for this pricing tier')
                    )
                    ->withColumn(
                        $form->text('Price')
                            ->setName('price')
                            ->setType('number')
                            ->setAttribute('step', '0.01')
                            ->setAttribute('min', '0')
                            ->setHelp('Price for this tier')
                    ),

                $form->textarea('Tier Description')
                    ->setName('tier_description')
                    ->setAttribute('rows', '2')
                    ->setHelp('Description of what this tier includes'),

                $form->row()
                    ->withColumn(
                        $form->text('Min Quantity')
                            ->setName('min_quantity')
                            ->setType('number')
                            ->setAttribute('min', '1')
                            ->setDefault(1)
                            ->setHelp('Minimum quantity for this tier')
                    )
                    ->withColumn(
                        $form->text('Max Quantity')
                            ->setName('max_quantity')
                            ->setType('number')
                            ->setAttribute('min', '0')
                            ->setHelp('Maximum quantity (leave empty for unlimited)')
                    ),

                $form->row()
                    ->withColumn(
                        $form->text('Setup Fee')
                            ->setName('setup_fee')
                            ->setType('number')
                            ->setAttribute('step', '0.01')
                            ->setAttribute('min', '0')
                            ->setDefault(0)
                            ->setHelp('One-time setup fee for this tier')
                    )
                    ->withColumn(
                        $form->text('Sort Order')
                            ->setName('sort_order')
                            ->setType('number')
                            ->setAttribute('min', '0')
                            ->setDefault(0)
                            ->setHelp('Display order (lower numbers first)')
                    ),

                $form->toggle('Active Tier')
                    ->setName('is_active')
                    ->setText('This pricing tier is active')
                    ->setDefault(true)
            ])
    ]
);

/**
 * Tab 4: Service Addons
 * Manage service addons (wp_b2bcnc_service_addons)
 */
$addons = $form->fieldset(
    'Service Addons',
    'Optional addons that can enhance this service',
    [
        $form->repeater('Service Addons')
            ->setName('service_addons')
            ->setHelp('Define optional addons for this service')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->text('Addon Name')
                            ->setName('addon_name')
                            ->setAttribute('maxlength', '200')
                            ->setHelp('Name of the addon')
                    )
                    ->withColumn(
                        $form->select('Addon Type')
                            ->setName('addon_type')
                            ->setOptions([
                                'upgrade' => 'Upgrade',
                                'additional' => 'Additional',
                                'extended_warranty' => 'Extended Warranty',
                                'training' => 'Training',
                                'support' => 'Support'
                            ])
                            ->setHelp('Type of addon')
                    ),

                $form->textarea('Addon Description')
                    ->setName('addon_description')
                    ->setAttribute('rows', '2')
                    ->setHelp('Description of what this addon provides'),

                $form->row()
                    ->withColumn(
                        $form->text('Price')
                            ->setName('price')
                            ->setType('number')
                            ->setAttribute('step', '0.01')
                            ->setAttribute('min', '0')
                            ->setHelp('Price for this addon')
                    )
                    ->withColumn(
                        $form->toggle('Recurring')
                            ->setName('is_recurring')
                            ->setText('This is a recurring addon')
                    ),

                $form->row()
                    ->withColumn(
                        $form->select('Billing Frequency')
                            ->setName('billing_frequency')
                            ->setOptions([
                                '' => 'Not Applicable',
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'annually' => 'Annually'
                            ])
                            ->setHelp('How often this addon is billed (if recurring)')
                    )
                    ->withColumn(
                        $form->text('Sort Order')
                            ->setName('sort_order')
                            ->setType('number')
                            ->setAttribute('min', '0')
                            ->setDefault(0)
                            ->setHelp('Display order')
                    ),

                $form->toggle('Active Addon')
                    ->setName('is_active')
                    ->setText('This addon is active and available')
                    ->setDefault(true)
            ])
    ]
);

/**
 * Tab 5: Equipment & Resources
 * Service equipment requirements (wp_b2bcnc_service_equipment)
 */
$equipment = $form->fieldset(
    'Equipment & Resources',
    'Equipment and resources required to deliver this service',
    [
        $form->repeater('Service Equipment')
            ->setName('service_equipment')
            ->setHelp('Define equipment needed for this service')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->text('Equipment Name')
                            ->setName('equipment_name')
                            ->setAttribute('maxlength', '200')
                            ->setHelp('Name of the equipment')
                    )
                    ->withColumn(
                        $form->select('Equipment Type')
                            ->setName('equipment_type')
                            ->setOptions([
                                'hardware' => 'Hardware',
                                'software' => 'Software',
                                'tool' => 'Tool',
                                'consumable' => 'Consumable'
                            ])
                            ->setHelp('Type of equipment')
                    ),

                $form->row()
                    ->withColumn(
                        $form->text('Manufacturer')
                            ->setName('manufacturer')
                            ->setAttribute('maxlength', '100')
                            ->setHelp('Equipment manufacturer')
                    )
                    ->withColumn(
                        $form->text('Model Number')
                            ->setName('model_number')
                            ->setAttribute('maxlength', '100')
                            ->setHelp('Model or part number')
                    ),

                $form->textarea('Specifications')
                    ->setName('specification')
                    ->setAttribute('rows', '2')
                    ->setHelp('Technical specifications or details'),

                $form->row()
                    ->withColumn(
                        $form->text('Quantity Required')
                            ->setName('quantity_required')
                            ->setType('number')
                            ->setAttribute('min', '1')
                            ->setDefault(1)
                            ->setHelp('Number of units required')
                    )
                    ->withColumn(
                        $form->text('Estimated Cost')
                            ->setName('estimated_cost')
                            ->setType('number')
                            ->setAttribute('step', '0.01')
                            ->setAttribute('min', '0')
                            ->setHelp('Estimated cost per unit')
                    ),

                $form->toggle('Customer Provided')
                    ->setName('is_customer_provided')
                    ->setText('Customer provides this equipment')
            ])
    ]
);

/**
 * Tab 6: Coverage Areas
 * Service coverage areas (wp_b2bcnc_service_coverage_areas)
 */
$coverageAreas = $form->fieldset(
    'Coverage Areas',
    'Geographic areas where this service is available',
    [
        $form->repeater('Coverage Areas')
            ->setName('service_coverage_areas')
            ->setHelp('Define geographic areas where this service is available')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->select('Area Type')
                            ->setName('area_type')
                            ->setOptions([
                                'city' => 'City',
                                'region' => 'Region',
                                'postal_code' => 'Postal Code',
                                'radius' => 'Radius'
                            ])
                            ->setHelp('Type of geographic area')
                    )
                    ->withColumn(
                        $form->text('Area Value')
                            ->setName('area_value')
                            ->setAttribute('maxlength', '100')
                            ->setHelp('City name, postal code, radius distance, etc.')
                    ),

                $form->row()
                    ->withColumn(
                        $form->text('Additional Fee')
                            ->setName('additional_fee')
                            ->setType('number')
                            ->setAttribute('step', '0.01')
                            ->setAttribute('min', '0')
                            ->setDefault(0)
                            ->setHelp('Additional fee for this area')
                    )
                    ->withColumn(
                        $form->text('Travel Time (Minutes)')
                            ->setName('travel_time_minutes')
                            ->setType('number')
                            ->setAttribute('min', '0')
                            ->setHelp('Estimated travel time to this area')
                    ),

                $form->toggle('Active Coverage')
                    ->setName('is_active')
                    ->setText('This coverage area is active')
                    ->setDefault(true)
            ])
    ]
);

/**
 * Tab 7: Service Dependencies
 * Service dependencies (wp_b2bcnc_service_dependencies)
 */
$dependencies = $form->fieldset(
    'Service Dependencies',
    'Other services that this service depends on',
    [
        $form->repeater('Dependencies')
            ->setName('service_dependencies')
            ->setHelp('Define services that must be completed before this service')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->select('Dependent Service')
                            ->setName('dependent_service_id')
                            // ->setOptions(getRelationshipOptions('\MakerMaker\Models\Service'))
                            ->setHelp('Service that this service depends on')
                    )
                    ->withColumn(
                        $form->select('Dependency Type')
                            ->setName('dependency_type')
                            ->setOptions([
                                'required' => 'Required',
                                'recommended' => 'Recommended',
                                'optional' => 'Optional',
                                'alternative' => 'Alternative'
                            ])
                            ->setDefault('required')
                            ->setHelp('Type of dependency')
                    ),

                $form->textarea('Notes')
                    ->setName('notes')
                    ->setAttribute('rows', '2')
                    ->setHelp('Additional notes about this dependency')
            ])
    ]
);

/**
 * Tab 8: Prerequisites
 * Service prerequisites (wp_b2bcnc_service_prerequisites)
 */
$prerequisites = $form->fieldset(
    'Prerequisites',
    'Requirements that must be met before service delivery',
    [
        $form->repeater('Service Prerequisites')
            ->setName('service_prerequisites')
            ->setHelp('Define prerequisites required before this service can be delivered')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->select('Prerequisite Type')
                            ->setName('prerequisite_type')
                            ->setOptions([
                                'service' => 'Service',
                                'equipment' => 'Equipment',
                                'access' => 'Access',
                                'documentation' => 'Documentation',
                                'other' => 'Other'
                            ])
                            ->setHelp('Type of prerequisite')
                    )
                    ->withColumn(
                        $form->select('Prerequisite Service')
                            ->setName('prerequisite_service_id')
                            // ->setOptions(getRelationshipOptions('\MakerMaker\Models\Service'))
                            ->setHelp('Required service (if prerequisite type is service)')
                    ),

                $form->text('Prerequisite Description')
                    ->setName('prerequisite_description')
                    ->setAttribute('maxlength', '500')
                    ->setHelp('Description of the prerequisite requirement'),

                $form->toggle('Required')
                    ->setName('is_required')
                    ->setText('This prerequisite is mandatory')
                    ->setDefault(true)
            ])
    ]
);

/**
 * Tab 9: Deliverables
 * Service deliverables (wp_b2bcnc_service_deliverables)
 */
$deliverables = $form->fieldset(
    'Service Deliverables',
    'What clients receive upon service completion',
    [
        $form->repeater('Deliverables')
            ->setName('service_deliverables')
            ->setHelp('Define what the client receives when this service is completed')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->text('Deliverable Name')
                            ->setName('deliverable_name')
                            ->setAttribute('maxlength', '200')
                            ->setHelp('Name of the deliverable')
                    )
                    ->withColumn(
                        $form->select('Deliverable Type')
                            ->setName('deliverable_type')
                            ->setOptions([
                                'equipment' => 'Equipment',
                                'software' => 'Software',
                                'documentation' => 'Documentation',
                                'training' => 'Training',
                                'access' => 'Access',
                                'support' => 'Support'
                            ])
                            ->setHelp('Type of deliverable')
                    ),

                $form->textarea('Description')
                    ->setName('deliverable_description')
                    ->setAttribute('rows', '2')
                    ->setHelp('Description of the deliverable'),

                $form->row()
                    ->withColumn(
                        $form->text('Quantity')
                            ->setName('quantity')
                            ->setType('number')
                            ->setAttribute('min', '1')
                            ->setDefault(1)
                            ->setHelp('Quantity delivered')
                    )
                    ->withColumn(
                        $form->text('Unit of Measure')
                            ->setName('unit_of_measure')
                            ->setAttribute('maxlength', '50')
                            ->setHelp('Unit (hours, items, licenses, etc.)')
                    ),

                $form->row()
                    ->withColumn(
                        $form->text('Additional Cost')
                            ->setName('additional_cost')
                            ->setType('number')
                            ->setAttribute('step', '0.01')
                            ->setAttribute('min', '0')
                            ->setDefault(0)
                            ->setHelp('Additional cost for this deliverable')
                    )
                    ->withColumn(
                        $form->text('Delivery Timeframe')
                            ->setName('delivery_timeframe')
                            ->setAttribute('maxlength', '100')
                            ->setHelp('When this deliverable is provided')
                    ),

                $form->row()
                    ->withColumn(
                        $form->toggle('Included')
                            ->setName('is_included')
                            ->setText('Included in base service price')
                            ->setDefault(true)
                    )
                    ->withColumn(
                        $form->text('Sort Order')
                            ->setName('sort_order')
                            ->setType('number')
                            ->setAttribute('min', '0')
                            ->setDefault(0)
                            ->setHelp('Display order')
                    )
            ])
    ]
);

/**
 * Tab 10: Service Attributes
 * Custom attributes for services (wp_b2bcnc_service_attributes)
 */
$attributes = $form->fieldset(
    'Service Attributes',
    'Custom attributes and metadata for this service',
    [
        $form->repeater('Custom Attributes')
            ->setName('service_attributes')
            ->setHelp('Define custom attributes for this service')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->text('Attribute Name')
                            ->setName('attribute_name')
                            ->setAttribute('maxlength', '100')
                            ->setHelp('Name of the attribute')
                    )
                    ->withColumn(
                        $form->select('Attribute Type')
                            ->setName('attribute_type')
                            ->setOptions([
                                'text' => 'Text',
                                'number' => 'Number',
                                'boolean' => 'Boolean',
                                'json' => 'JSON',
                                'url' => 'URL',
                                'email' => 'Email'
                            ])
                            ->setDefault('text')
                            ->setHelp('Data type for this attribute')
                    ),

                $form->textarea('Attribute Value')
                    ->setName('attribute_value')
                    ->setAttribute('rows', '2')
                    ->setHelp('Value for this attribute'),

                $form->row()
                    ->withColumn(
                        $form->toggle('Configurable')
                            ->setName('is_configurable')
                            ->setText('This attribute can be configured by clients')
                    )
                    ->withColumn(
                        $form->text('Display Order')
                            ->setName('display_order')
                            ->setType('number')
                            ->setAttribute('min', '0')
                            ->setDefault(0)
                            ->setHelp('Order for displaying attributes')
                    )
            ])
    ]
);

/**
 * Tab 11: Bundles & Relationships
 * Service bundles and relationships (wp_b2bcnc_service_bundles, wp_b2bcnc_bundle_services)
 */
$bundles = $form->fieldset(
    'Service Bundles',
    'Bundle relationships and package configurations',
    [
        $form->checkboxes('Included in Bundles')
            ->setName('bundle_memberships')
            // ->setOptions(getRelationshipOptions('\MakerMaker\Models\ServiceBundle'))
            ->setHelp('Select bundles that include this service'),


        $form->text('Bundle Configuration')
            ->setName('bundle_notes')
            ->setHelp('Notes about bundle configurations and relationships'),

        $form->repeater('Bundle Services (if this is a bundle)')
            ->setName('bundle_services')
            ->setHelp('If this service is itself a bundle, define the included services')
            ->setFields([
                $form->row()
                    ->withColumn(
                        $form->select('Included Service')
                            ->setName('service_id')
                            // ->setOptions(getRelationshipOptions('\MakerMaker\Models\Service'))
                            ->setHelp('Service included in this bundle')
                    )
                    ->withColumn(
                        $form->text('Quantity')
                            ->setName('quantity')
                            ->setType('number')
                            ->setAttribute('min', '1')
                            ->setDefault(1)
                            ->setHelp('Quantity of this service in the bundle')
                    ),

                $form->row()
                    ->withColumn(
                        $form->toggle('Optional')
                            ->setName('is_optional')
                            ->setText('This service is optional in the bundle')
                    )
                    ->withColumn(
                        $form->text('Sort Order')
                            ->setName('sort_order')
                            ->setType('number')
                            ->setAttribute('min', '0')
                            ->setDefault(0)
                            ->setHelp('Order in bundle listing')
                    )
            ])
    ]
);

/**
 * Tab 12: SEO & Meta
 * SEO and metadata from main services table
 */
$seo = $form->fieldset(
    'SEO & Meta Data',
    'Search engine optimization and metadata',
    [
        $form->text('Meta Title')
            ->setName('meta_title')
            ->setAttribute('maxlength', '200')
            ->setHelp('SEO title for search engines (max 200 characters)'),

        $form->textarea('Meta Description')
            ->setName('meta_description')
            ->setAttribute('rows', '3')
            ->setAttribute('maxlength', '500')
            ->setHelp('SEO description for search engines (max 500 characters)'),

        $form->row()
            ->withColumn(
                $form->select('Search Index Status')
                    ->setName('index_status')
                    ->setOptions([
                        'index' => 'Index (Allow in search)',
                        'noindex' => 'No Index (Block from search)'
                    ])
                    ->setDefault('index')
            )
            ->withColumn(
                $form->select('Schema Type')
                    ->setName('schema_type')
                    ->setOptions([
                        'Service' => 'Service',
                        'Product' => 'Product',
                        'Offer' => 'Offer'
                    ])
                    ->setDefault('Service')
            ),

        $form->toggle('Include in Sitemap')
            ->setName('sitemap_include')
            ->setText('Include this service in XML sitemap')
            ->setDefault(true)
    ]
);

// Save button
$save = $form->save($button ?? 'Save Service');

// Create tabs layout using TypeRocket's Tabs element
$tabs = \TypeRocket\Elements\Tabs::new()
    ->setFooter($save)
    ->layoutLeft();

// Add all tabs with appropriate dashicons and descriptions
$tabs->tab('Overview', 'admin-post', [$overview])
    ->setDescription('Core service information');

$tabs->tab('Pricing', 'money-alt', [$pricing])
    ->setDescription('Base pricing configuration');

$tabs->tab('Pricing Tiers', 'chart-bar', [$pricingTiers])
    ->setDescription('Tiered pricing structure');

$tabs->tab('Addons', 'plus-alt', [$addons])
    ->setDescription('Service addons and upgrades');

$tabs->tab('Equipment', 'admin-tools', [$equipment])
    ->setDescription('Required equipment and resources');

$tabs->tab('Coverage Areas', 'location', [$coverageAreas])
    ->setDescription('Geographic service coverage');

$tabs->tab('Dependencies', 'networking', [$dependencies])
    ->setDescription('Service dependencies');

$tabs->tab('Prerequisites', 'list-view', [$prerequisites])
    ->setDescription('Client requirements');

$tabs->tab('Deliverables', 'portfolio', [$deliverables])
    ->setDescription('Service outputs and deliverables');

$tabs->tab('Attributes', 'admin-customizer', [$attributes])
    ->setDescription('Custom attributes and metadata');

$tabs->tab('Bundles', 'category', [$bundles])
    ->setDescription('Bundle relationships');

$tabs->tab('SEO & Meta', 'search', [$seo])
    ->setDescription('SEO and metadata settings');

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
