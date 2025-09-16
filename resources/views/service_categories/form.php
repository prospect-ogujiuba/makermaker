<?php

/**
 * ServiceCategory Form
 */

use App\Models\Category;
use MakerMaker\Models\ServiceCategory;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Category',
        'Define the category characteristics and pricing impact',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setHelp('Display name for this service category (max 100 characters)')
                        ->setAttribute('maxlength', '128')
                        ->setAttribute('placeholder', 'e.g., VoIP Systems')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('slug')
                        ->setLabel('Slug')
                        ->setHelp('URL-friendly version of the name (lowercase, hyphens only)')
                        ->setAttribute('maxlength', '128')
                        ->markLabelRequired()
                ),

            $form->row()
                ->withColumn(
                    $form->select('parent_id')
                        ->setLabel('Parent Category') // Use setLabel to set display label
                        ->setHelp('Optional parent category for hierarchical organization')
                        ->setModelOptions(ServiceCategory::class, 'name', 'id', 'Select A Category')
                )
                ->withColumn(),
        ]
    )

])->setDescription('Category information');

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
            ->setAttribute('value', 'No services are currently associated with this category level')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services using this category level',
        $service_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
