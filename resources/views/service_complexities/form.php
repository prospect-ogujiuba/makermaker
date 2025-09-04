<?php

/**
 * Enhanced ServiceComplexity Form - Complete Data Management
 * File: resources/views/admin/service_complexities/form.php
 * 
 * This form handles ServiceComplexity properties organized into logical tabs
 * based on the actual database structure and system logic requirements.
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\ServiceComplexity;
use TypeRocket\Models\WPUser;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Complexity Overview',
        'Define the complexity level characteristics and pricing impact',
        [
            $form->row()
                ->withColumn(
                    $form->text('name') // Changed from 'Name' to 'name'
                        ->setLabel('Name') // Use setLabel to set display label
                        ->setHelp('Descriptive name for this complexity level (e.g., "Basic", "Standard", "Advanced", "Expert")')
                        ->setAttribute('maxlength', '100')
                        ->setAttribute('placeholder', 'e.g., Advanced Implementation')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->number('level') // Changed from 'Complexity Level' to 'level'
                        ->setLabel('Complexity Level') // Use setLabel to set display label
                        ->setHelp('Numeric ranking (1 = simplest, higher numbers = more complex)')
                        ->setAttribute('min', '1')
                        ->setAttribute('step', '1')
                        ->markLabelRequired()
                ),

            $form->row()
                ->withColumn(
                    $form->number('price_multiplier') // Changed from 'Price Multiplier' to 'price_multiplier'
                        ->setLabel('Price Multiplier') // Use setLabel to set display label
                        ->setHelp('Decimal multiplier for pricing (1.0 = base price, 1.5 = 150% markup)')
                        ->setAttribute('min', '1')
                        ->setAttribute('max', '10.00')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('placeholder', '1.00')
                        ->markLabelRequired()
                )
                ->withColumn(),
        ]
    )

])->setDescription('Complexity information');

if (isset($current_id)) {

    // System Info Tab
    $tabs->tab('System', 'info', [
        $form->fieldset(
            'System Info',
            'Core system metadata fields',
            [
                $form->row()
                    ->withColumn(
                        $form->text('id')
                            ->setLabel('ID')
                            ->setHelp('System generated ID')
                            ->setAttribute('readonly', 'readonly')
                    )
                    ->withColumn(),
                $form->row()
                    ->withColumn(
                        $form->text('created_at')
                            ->setLabel('Created At')
                            ->setHelp('Record creation timestamp')
                            ->setAttribute('readonly', 'readonly')
                    )
                    ->withColumn(
                        $form->text('updated_at')
                            ->setLabel('Updated At')
                            ->setHelp('Last update timestamp')
                            ->setAttribute('readonly', 'readonly')
                    ),
                $form->row()
                    ->withColumn(
                        $form->text('created_by')
                            ->setLabel('Created By')
                            ->setHelp('User ID who originally created this record')
                            ->setAttribute('readonly', 'readonly')
                    )
                    ->withColumn(
                        $form->text('updated_by')
                            ->setLabel('Updated By')
                            ->setHelp('User ID who last updated this record')
                            ->setAttribute('readonly', 'readonly')
                    ),

                $form->row()
                    ->withColumn(
                        $form->text('deleted_at')
                            ->setLabel('Deleted At')
                            ->setHelp('Timestamp when this record was soft-deleted, if applicable')
                            ->setAttribute('readonly', 'readonly')
                    )
                    ->withColumn()
            ]
        )

    ])->setDescription('System information');

    // Nested Tabs for relationship information
    $relationshipNestedTabs = \TypeRocket\Elements\Tabs::new()
        ->layoutTop();

    $service_fields = [];

    if ($services && count($services) > 0) {
        foreach ($services as $service) {
            $service_fields[] = $form->text($service->name ?? "Service #{$service->id}")
                ->setAttribute('value', $service->name)
                ->setAttribute('readonly', 'readonly')
                ->setHelp("Service ID: {$service->id}");
        }
    } else {
        $service_fields[] = $form->text('No Services')
            ->setAttribute('value', 'No services are currently associated with this complexity level')
            ->setAttribute('readonly', 'readonly');
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $service_fields)->setDescription('Services using this complexity');

    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationship', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Relationship information');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
