<?php

/**
 * Enhanced ServiceComplexity Form - Complete Data Management
 * File: resources/views/admin/service_complexities/form.php
 * 
 * This form handles ServiceComplexity properties organized into logical tabs
 * based on the actual database structure and system logic requirements.
 */

use MakerMaker\Models\ServiceComplexity;
use TypeRocket\Models\WPUser;

// Form instance
$form = tr_form($service_complexity ?? ServiceComplexity::new())->useErrors()->useOld();
$service_complexity = $service_complexity ?? NULL;

// Relationships
$services = $service_complexity->services ?? [];



echo $form->open();

// Tab Layout
$tabs = \TypeRocket\Elements\Tabs::new()
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
                    $form->text('Name')
                        ->setName('name')
                        ->setHelp('Descriptive name for this complexity level (e.g., "Basic", "Standard", "Advanced", "Expert")')
                        ->setAttribute('maxlength', '100')
                        ->setAttribute('placeholder', 'e.g., Advanced Implementation')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->number('Complexity Level')
                        ->setName('level')
                        ->setHelp('Numeric ranking (1 = simplest, higher numbers = more complex)')
                        ->setAttribute('min', '1')
                        ->setAttribute('step', '1')
                        ->markLabelRequired()
                ),

            $form->row()
                ->withColumn(
                    $form->number('Price Multiplier')
                        ->setName('price_multiplier')
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

if ($service_complexity != NULL) {

    // System Info Tab
    $tabs->tab('System', 'info', [
        $form->fieldset(
            'System Info',
            'Core system metadata fields',
            [
                $form->row()
                    ->withColumn(
                        $form->text('ID')
                            ->setName('id')
                            ->setHelp('System generated ID')
                            ->setAttribute('readonly', 'readonly')
                    )
                    ->withColumn(),
                $form->row()
                    ->withColumn(
                        $form->text('Created At')
                            ->setName('created_at')
                            ->setHelp('Record creation timestamp')
                            ->setAttribute('readonly', 'readonly')
                    )
                    ->withColumn(
                        $form->text('Updated At')
                            ->setName('updated_at')
                            ->setHelp('Last update timestamp')
                            ->setAttribute('readonly', 'readonly')
                    ),
                $form->row()
                    ->withColumn(
                        $form->text('Created By')
                            ->setName('created_by')
                            ->setHelp('User ID who originally created this record')
                            ->setAttribute('readonly', 'readonly')
                    )
                    ->withColumn(
                        $form->text('Updated By')
                            ->setName('updated_by')
                            ->setHelp('User ID who last updated this record')
                            ->setAttribute('readonly', 'readonly')
                    ),



                $form->row()
                    ->withColumn(
                        $form->text('Deleted At')
                            ->setName('deleted_at')
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

    // // Services relationship sub-tab
    // if ($services && count($services) > 0) {
    //     foreach ($services as $service) {
    //         $service_fields[] = $form->text($service->name ?? "Service #{$service->id}")
    //             ->setAttribute('value', $service->name)
    //             ->setAttribute('readonly', 'readonly')
    //             ->setHelp("Service ID: {$service->id}");
    //     }
    // } else {
    //     $service_fields[] = $form->text('No Services')
    //         ->setAttribute('value', 'No services are currently associated with this complexity level')
    //         ->setAttribute('readonly', 'readonly');
    // }

    $relationshipNestedTabs->tab('Services', 'admin-post', tr_table($service_complexity))->setDescription('Services using this complexity');

    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationship', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Relationship information');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
