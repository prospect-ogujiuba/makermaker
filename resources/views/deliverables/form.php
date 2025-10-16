<?php

/**
 * Deliverable Form
 */

// Form instance
echo $form->open();

echo to_resource('Deliverable', 'index', 'Back To Deliverables');

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Deliverable',
        'Define the service deliverable characteristics and requirements',
        [
            $form->row()
                ->withColumn(
                    $form->text('name')
                        ->setLabel('Name')
                        ->setHelp('Display name for this service deliverable')
                        ->setAttribute('maxlength', '128')
                        ->setAttribute('placeholder', 'e.g., System Design Document')
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->select('deliverable_type')
                        ->setLabel('Deliverable Type')
                        ->setHelp('Category of deliverable')
                        ->setOptions([
                            '' => '-- Select Type --',
                            'Document' => 'document',
                            'Software' => 'software',
                            'Hardware' => 'hardware',
                            'Service' => 'service',
                            'Training' => 'training',
                            'Report' => 'report'
                        ])
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->textarea('description')
                        ->setLabel('Description')
                        ->setHelp('Detailed description of what this deliverable includes and provides')
                        ->setAttribute('placeholder', 'Describe the deliverable contents, scope, and purpose...')
                        ->markLabelRequired()
                ),
            $form->row()
                ->withColumn(
                    $form->text('template_path')
                        ->setLabel('Template Path')
                        ->setHelp('File path or URL to template (optional)')
                        ->setAttribute('maxlength', '255')
                        ->setAttribute('placeholder', '/templates/deliverables/system-design.docx')
                )
                ->withColumn(
                    $form->number('estimated_effort_hours')
                        ->setLabel('Estimated Effort (Hours)')
                        ->setHelp('Expected hours to complete this deliverable')
                        ->setAttribute('step', '0.25')
                        ->setAttribute('min', '0')
                        ->setAttribute('placeholder', 'e.g., 0.00')
                ),
            $form->row()
                ->withColumn(
                    $form->toggle('requires_approval')
                        ->setLabel('Requires Approval')
                        ->setHelp('Does this deliverable require customer approval before completion?')
                )
                ->withColumn()
        ]
    )

])->setDescription('Service Deliverable');

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

    $service_fields = [];

    if ($services && count($services) > 0) {
        foreach ($services as $service) {
            $row = $form->row();

            // Additional info column (optional)
            $row->column(
                $form->text("Service Name")
                    ->setAttribute('value', $service->name ?? 'B2CNC-' . $service->id)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // ID Column (smaller width)
            $row->column(
                $form->text("SKU")
                    ->setAttribute('value', $service->sku)
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            // Name Column (main content)
            $row->column(
                $form->text("Service ID")
                    ->setAttribute('value', $service->id ?? "Service #{$service->id}")
                    ->setAttribute('readonly', true)
                    ->setAttribute('name', false)

            );

            $service_fields[] = $row;
        }
    } else {
        $service_fields[] = $form->text('No Services')
            ->setAttribute('value', 'No services are currently associated with this deliverable')
            ->setAttribute('readonly', true);
    }

    $relationshipNestedTabs->tab('Services', 'admin-post', $form->fieldset(
        'Related Services',
        'Services with this is a deliverable',
        $service_fields
    ));



    // Add the nested relationship tabs to main tabs
    $tabs->tab('Relationships', 'admin-links', [$relationshipNestedTabs])
        ->setDescription('Related Entities');
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close();
