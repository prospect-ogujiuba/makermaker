<?php

/**
 * ServiceBundle Form
 */

use MakerMaker\Models\ServiceType;

// Form instance
echo $form->open();

echo to_resource('ServiceBundle', 'index', 'Back To Service Bundles');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Service Bundle',
        'Define the service bundle characteristics, pricing, and validity',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Bundle Name')
                        ->setHelp('Display name for this service bundle')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., Complete Office Setup')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('slug')
                        ->setLabel('Slug')
                        ->setAttribute('maxlength', '64')
                        ->setHelp('URL-friendly identifier (auto-generated from name if left empty)')
                        ->setAttribute('placeholder', 'complete-office-setup')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->select('bundle_type')
                        ->setLabel('Bundle Type')
                        ->setHelp('Category of bundle offering')
                        ->setOptions([
                            'Select Bundle Type' => NULL,
                            'Package' => 'package',
                            'Collection' => 'collection',
                            'Suite' => 'suite',
                            'Solution' => 'solution'
                        ])
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->number('total_discount_pct')
                        ->setLabel('Total Discount %')
                        ->setHelp('Overall bundle discount percentage (0-100)')
                        ->setAttribute('step', '0.01')
                        ->setAttribute('min', '0')
                        ->setAttribute('max', '100')
                        ->setAttribute('placeholder', '0.00')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->textarea('short_desc')
                        ->setLabel('Short Description')
                        ->setHelp('Brief summary of what this bundle includes (max 512 characters)')
                        ->setAttribute('maxlength', '512')
                        ->setAttribute('rows', '2')
                        ->setAttribute('placeholder', 'e.g., Complete IT solution for small offices (5-15 employees)')
                ),
            $form->row()
                ->withColumn(
                    $form->editor('long_desc')
                        ->setLabel('Long Description')
                        ->setHelp('Detailed description of the bundle, benefits, and what\'s included')
                        ->setAttribute('placeholder', 'Provide a comprehensive description...')
                ),
            $form->row()
                ->withColumn(
                    $form->date('valid_from')
                        ->setLabel('Valid From')
                        ->setHelp('Start date when this bundle becomes available (optional)')
                        ->setHelp('Date when this pricing becomes effective')
                        ->setFormat('yy-mm-dd')
                        ->setAttribute('placeholder', 'Format: ' . date('Y-m-d H:i:s'))
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->date('valid_to')
                        ->setLabel('Valid To')
                        ->setHelp('End date when this bundle expires (optional)')
                        ->setHelp('Date when this pricing expires (leave empty for no expiration)')
                        ->setFormat('yy-mm-dd')
                        ->setAttribute('placeholder', 'Format: ' . date('Y-m-d H:i:s'))
                ),
        ]
    )

])->setDescription('Service Bundle');

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
                            ->setAttribute('disabled', true)
                    )
                    ->withColumn()
            ]
        )

    ])->setDescription('System information');

    // Nested Tabs for Related Entities
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
            ->setAttribute('value', 'No services are currently associated with this service bundle')
            ->setAttribute('readonly', true)
            ->setAttribute('name', false);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services using this service bundle',
        $service_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
