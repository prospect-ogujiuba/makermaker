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
        'Category Information',
        'Define the category characteristics and organization',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setHelp('Display name for this service category')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., VoIP Systems')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('slug')
                        ->setLabel('Slug')
                        ->setHelp('URL-friendly version of the name (lowercase, hyphens only)')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., voip-systems')
                        ->markLabelRequired()
                ),

            $form->row()
                ->withColumn(
                    $form->select('parent_id')
                        ->setLabel('Parent Category')
                        ->setHelp('Optional parent category for hierarchical organization')
                        ->setModelOptions(ServiceCategory::class, 'name', 'id', 'Top Level Category')
                )
                ->withColumn(
                    $form->text('icon')
                        ->setLabel('Icon')
                        ->setHelp('Icon name for UI display (e.g., phone, shield, cloud)')
                        ->setAttribute('maxlength', '64')
                        ->setAttribute('placeholder', 'e.g., phone-call')
                ),

            $form->row()
                ->withColumn(
                    $form->number('sort_order')
                        ->setLabel('Sort Order')
                        ->setHelp('Numeric order for category display (lower numbers appear first)')
                        ->setAttribute('min', '0')
                        ->setAttribute('max', '4294967295')
                        ->setAttribute('step', '1')
                        ->setAttribute('placeholder', '0')
                )
                ->withColumn(
                    $form->toggle('is_active')
                        ->setLabel('Active')
                        ->setHelp('Whether this category is available for use')
                        ->setAttribute('value', '1')
                ),

            $form->row()
                ->withColumn(
                    $form->textarea('description')
                        ->setLabel('Description')
                        ->setHelp('Detailed description of what services belong in this category')
                        ->setAttribute('placeholder', 'e.g., Communication systems and telephony services')
                        ->setAttribute('rows', '3')
                )
                ->withColumn()
        ]
    )

])->setDescription('Category Configuration');

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
