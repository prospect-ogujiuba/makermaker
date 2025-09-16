<?php

/**
 * ServiceBundleItem Form View
 * 
 * This view displays a form for creating/editing ServiceBundleItem.
 * Add your form fields and functionality here.
 */

use MakerMaker\Models\ServiceBundle;
use MakerMaker\Models\Service;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Bundle Item',
        'Define services included in this bundle',
        [
            $form->row()
                ->withColumn(
                    $form->select('bundle_id')
                        ->setLabel('Bundle')
                        ->setHelp('Select the service bundle this item belongs to')
                        ->setOptions(['Select Bundle' => null])
                        ->setModelOptions(ServiceBundle::class, 'name', 'id')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Service')
                        ->setHelp('Select the service to include in this bundle')
                        ->setOptions(['Select Service' => null])
                        ->setModelOptions(Service::class, 'name', 'id')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->number('quantity')
                        ->setLabel('Quantity')
                        ->setHelp('Number of units of this service included in the bundle')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->number('discount_pct')
                        ->setLabel('Discount %')
                        ->setHelp('Percentage discount applied to this service in the bundle (0-100)')
                        ->markLabelRequired()
                )
        ]
    )
])->setDescription('Service Bundle Item');

// Conditional System Info Tab
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
                            ->setLabel('Service Deliverable Assignment ID')
                            ->setHelp('System generated unique identifier')
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
                            ->setHelp('User who originally created this record')
                            ->setAttribute('value', $createdBy->user_nicename ?? 'System')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    )
                    ->withColumn(
                        $form->text('updated_by_user')
                            ->setLabel('Last Updated By')
                            ->setHelp('User who last updated this record')
                            ->setAttribute('value', $updatedBy->user_nicename ?? 'System')
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
                            ->setAttribute('disabled', true)
                    )
                    ->withColumn()
            ]
        )
    ])->setDescription('System information');


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
            ->setAttribute('value', 'No services are currently associated with this category level')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Other services in the selected bundle',
        $service_fields
    ));

    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
