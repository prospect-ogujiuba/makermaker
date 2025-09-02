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

// $userID = $user->ID;
// echo $user;

$form = tr_form($service_complexity ?? ServiceComplexity::new())->useErrors()->useOld();

// Empty or current item
$complexity_level = $service_complexity ?? NULL;

// Relationships
$services = $complexity_level->services ?? [];

echo $form->open();

// Tab Layout
$tabs = \TypeRocket\Elements\Tabs::new()
    ->setFooter($form->save())
    ->layoutLeft();

// Tabs
$overview = $form->fieldset(
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
                    ->setAttribute('placeholder', '1.00')
                    ->markLabelRequired()
            )
            ->withColumn(),

    ]
);

$system = $form->fieldset(
    'System Information',
    'System related metadata',
    [
        $form->row()
            ->withColumn(
                $form->text('ID')
                    ->setName('id')
                    ->setHelp('Timestamp when this record was soft-deleted, if applicable.')
                    ->setAttribute('readonly', 'readonly')
            )
            ->withColumn(),
        $form->row()
            ->withColumn(
                $form->text('Created At')
                    ->setName('created_at')
                    ->setHelp('User ID who originally created this record.')
                    ->setAttribute('min', '1')
                    ->setAttribute('readonly', 'readonly')

            )
            ->withColumn(
                $form->text('Updated At')
                    ->setName('updated_at')
                    ->setHelp('User ID who last updated this record.')
                    ->setAttribute('min', '1')
                    ->setAttribute('readonly', 'readonly')

            ),
        $form->row()
            ->withColumn(
                $form->text('Created By')
                    ->setName('created_by')
                    ->setHelp('User ID who originally created this record.')
                    ->setAttribute('min', '1')
                    ->setAttribute('readonly', 'readonly')

            )
            ->withColumn(
                $form->text('Updated By')
                    ->setName('updated_by')
                    ->setHelp('User ID who last updated this record.')
                    ->setAttribute('min', '1')
                    ->setAttribute('readonly', 'readonly')

            ),

        $form->row()
            ->withColumn(
                $form->text('Deleted At')
                    ->setName('deleted_at')
                    ->setHelp('Timestamp when this record was soft-deleted, if applicable.')
                    ->setAttribute('readonly', 'readonly')

            )
            ->withColumn()
    ]
);

// Readonly Relationship Fields
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

// Tab Pages
$tabs->tab('Overview', 'admin-settings', [$overview])
    ->setDescription('Complexity information');

// System and Relationship info with nested tabs
if ($complexity_level != NULL) {

    // Create nested tabs for System information
    $systemNestedTabs = \TypeRocket\Elements\Tabs::new()
        ->layoutTop(); // Use top layout for nested tabs to differentiate

    // System sub-tabs
    $systemNestedTabs->tab('Basic Info', 'admin-generic', [
        $form->fieldset(
            'Basic System Data',
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
                    )
            ]
        )
    ])->setDescription('Basic system timestamps and ID');

    $systemNestedTabs->tab('User Tracking', 'admin-users', [
        $form->fieldset(
            'User Activity Tracking',
            'User information for creation and updates',
            [
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
                    )
            ]
        )
    ])->setDescription('User creation and modification tracking');

    $systemNestedTabs->tab('Deletion Info', 'trash', [
        $form->fieldset(
            'Soft Delete Information',
            'Deletion tracking and restoration data',
            [
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
    ])->setDescription('Soft deletion tracking');

    // Add the nested system tabs to main tabs
    $tabs->tab('System', 'info', [$systemNestedTabs])
        ->setDescription('System information');

    // Create nested tabs for Relationship information
    $relationshipNestedTabs = \TypeRocket\Elements\Tabs::new()
        ->layoutTop();

    // Services relationship sub-tab
    $relationshipNestedTabs->tab('Services', 'admin-post', [
        $form->fieldset(
            'Associated Services',
            'Services linked to this complexity level',
            $service_fields // Using your existing $service_fields array for services
        )
    ])->setDescription('Services using this complexity');

    // Additional relationship sub-tabs (examples)
    $relationshipNestedTabs->tab('Dependencies', 'networking', [
        $form->fieldset(
            'System Dependencies',
            'Other system components this complexity depends on',
            [
                $form->text('No Dependencies')
                    ->setAttribute('value', 'No system dependencies defined')
                    ->setAttribute('readonly', 'readonly')
            ]
        )
    ])->setDescription('System dependencies and requirements');

    $relationshipNestedTabs->tab('Usage Stats', 'chart-bar', [
        $form->fieldset(
            'Usage Statistics',
            'Statistical information about this complexity level usage',
            [
                $form->text('Service Count')
                    ->setAttribute('value', $services ? count($services) : '0')
                    ->setAttribute('readonly', 'readonly')
                    ->setHelp('Total number of services using this complexity level'),
                $form->text('Usage Percentage')
                    ->setAttribute('value', 'Calculate based on total services')
                    ->setAttribute('readonly', 'readonly')
                    ->setHelp('Percentage of total services using this complexity')
            ]
        )
    ])->setDescription('Usage analytics and statistics');

    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationship', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Relationship information');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
