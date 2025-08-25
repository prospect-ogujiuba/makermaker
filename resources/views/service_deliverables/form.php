<?php

/**
 * ServiceDeliverables Form
 * File: resources/views/admin/service_deliverables/form.php
 * 
 * Form for creating and editing service deliverables
 */

use MakerMaker\Models\ServiceDeliverables;
use MakerMaker\Models\Service;

/** @var \TypeRocket\Elements\Form $form */
/** @var \MakerMaker\Models\ServiceDeliverables $serviceDeliverable */
/** @var string $button */

echo $form->open();

/**
 * Basic Information Section
 */
$basicInfo = $form->fieldset(
    'Deliverable Information',
    'Core deliverable details and type configuration',
    [
        $form->row()
            ->withColumn(
                $form->select('Service')
                    ->setName('service_id')
                    ->setModelOptions(Service::class, 'name')
                    ->setHelp('Select the service this deliverable belongs to')
                    ->markLabelRequired()
            )
            ->withColumn(
                $form->text('Deliverable Name')
                    ->setName('deliverable_name')
                    ->setHelp('Name of what the client receives (e.g., "User Manual", "SSL Certificate")')
                    ->setAttribute('maxlength', '200')
                    ->markLabelRequired()
            ),

        $form->row()
            ->withColumn(
                $form->select('Deliverable Type')
                    ->setName('deliverable_type')
                    ->setOptions([
                        'Equipment' => 'equipment',
                        'Software' => 'software',
                        'Documentation' => 'documentation',
                        'Training' => 'training',
                        'Access/Credentials' => 'access',
                        'Support Package' => 'support'
                    ])
                    ->setHelp('Category of deliverable')
                    ->markLabelRequired()
                    ->setAttribute('id', 'deliverable_type_select')
            )
            ->withColumn(
                $form->toggle('Included in Service')
                    ->setName('is_included')
                    ->setText('This deliverable is included in the base service price')
                    ->setHelp('Uncheck if this deliverable has additional cost')
                    ->setDefault(true)
                    ->setAttribute('id', 'is_included_toggle')
            ),

        $form->textarea('Description')
            ->setName('deliverable_description')
            ->setHelp('Detailed description of what this deliverable includes and provides')
    ]
);

/**
 * Quantity and Pricing Section
 */
$quantityPricing = $form->fieldset(
    'Quantity & Pricing',
    'Quantity specifications and cost information',
    [
        $form->row()
            ->withColumn(
                $form->text('Quantity')
                    ->setName('quantity')
                    ->setType('number')
                    ->setAttribute('min', '1')
                    ->setDefault(1)
                    ->setHelp('Number of units delivered')
            )
            ->withColumn(
                $form->text('Unit of Measure')
                    ->setName('unit_of_measure')
                    ->setHelp('Unit type (e.g., "license", "hour", "document", "device")')
                    ->setAttribute('maxlength', '50')
                    ->setAttribute('id', 'unit_of_measure_field')
            ),

        $form->row()
            ->withColumn(
                $form->text('Additional Cost')
                    ->setName('additional_cost')
                    ->setType('number')
                    ->setAttribute('step', '0.01')
                    ->setAttribute('min', '0')
                    ->setDefault(0)
                    ->setHelp('Additional cost per unit (0 if included in service)')
                    ->setAttribute('id', 'additional_cost_field')
            )
            ->withColumn(
                $form->text('Sort Order')
                    ->setName('sort_order')
                    ->setType('number')
                    ->setAttribute('min', '0')
                    ->setDefault(0)
                    ->setHelp('Display order (0 = auto-assign)')
            )
    ]
);

/**
 * Delivery Information Section
 */
$deliveryInfo = $form->fieldset(
    'Delivery Information',
    'When and how the deliverable is provided',
    [
        $form->text('Delivery Timeframe')
            ->setName('delivery_timeframe')
            ->setHelp('When this deliverable is provided (e.g., "Upon completion", "2 weeks", "Same day")')
            ->setAttribute('maxlength', '100')
            ->setAttribute('id', 'delivery_timeframe_field')
    ]
);

// Save button
$save = $form->save($button ?? 'Save Service Deliverable');

// Create tabs layout
$tabs = \TypeRocket\Elements\Tabs::new()
    ->setFooter($save)
    ->layoutLeft();

// Add tabs
$tabs->tab('Deliverable Details', 'portfolio', [$basicInfo])
    ->setDescription('Deliverable name, type, and service');

$tabs->tab('Quantity & Pricing', 'money-alt', [$quantityPricing])
    ->setDescription('Quantity, units, and cost information');

$tabs->tab('Delivery Info', 'calendar-alt', [$deliveryInfo])
    ->setDescription('Delivery timing and logistics');

// Render the tabbed interface
$tabs->render();

echo $form->close();
