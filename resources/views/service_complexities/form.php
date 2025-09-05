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

// Conditional
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
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)

                    )
                    ->withColumn(),
                $form->row()
                    ->withColumn(
                        $form->text('created_at')
                            ->setLabel('Created At')
                            ->setHelp('Record creation timestamp')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    )
                    ->withColumn(
                        $form->text('updated_at')
                            ->setLabel('Updated At')
                            ->setHelp('Last update timestamp')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)

                    ),
                $form->row()
                    ->withColumn(
                        $form->text('created_by_user')
                            ->setLabel('Created By')
                            ->setHelp('User ID who originally created this record')
                            ->setAttribute('value', $createdBy->user_nicename)
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)


                    )
                    ->withColumn(
                        $form->text('updated_by_user')
                            ->setLabel('Last Updated By')
                            ->setHelp('User ID who last updated this record')
                            ->setAttribute('value', $updatedBy->user_nicename)
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)

                    ),

                $form->row()
                    ->withColumn(
                        $form->text('deleted_at')
                            ->setLabel('Deleted At')
                            ->setHelp('Timestamp when this record was soft-deleted, if applicable')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)

                    )
                    ->withColumn()
            ]
        )

    ])->setDescription('System information');

    // Nested Tabs for relationship information
    $relationshipNestedTabs = \TypeRocket\Elements\Tabs::new()
        ->layoutTop();



    if ($services && count($services) > 0) {
        foreach ($services as $service) {
            $row = $form->row();

            // Name Column (main content)
            $row->column(
                $form->text("Service Name")
                    ->setAttribute('value', $service->name ?? "Service #{$service->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // Additional info column (optional)
            $row->column(
                $form->text("SKU")
                    ->setAttribute('value', $service->sku ?? 'B2CNC-' . $service->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // ID Column (smaller width)
            $row->column(
                $form->text("ID")
                    ->setAttribute('value', $service->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $service_fields[] = $row;
        }
    } else {
        $service_fields[] = $form->text('No Services')
            ->setAttribute('value', 'No services are currently associated with this complexity level')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services using this complexity level',
        $service_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationship', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Relationship information');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
