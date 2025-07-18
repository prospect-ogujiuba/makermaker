<?php

/** @var \TypeRocket\Elements\Form $form */

use MakerMaker\Models\Service;

// Get the service and related data
// $service = Service::new()->load([
//     // 'service_requests'
// ])->find($form->getModel()->id);

echo $form->save($button)->setFields([
    // Service Information
    $form->fieldset('Service Details', 'Basic service information', [
        $form->row()
            ->withColumn(
                $form->text('Service Code')->setName('code')
                    ->setRequired()
                    ->setHelp('Unique identifier for this service (e.g., voip_hosting)')
                    ->setAttribute('maxlength', '50')
                    ->setAttribute('pattern', '^[a-z_]{1,50}$')
            )->withColumn(
                $form->text('Service Name')->setName('name')
                    ->setRequired()
                    ->setHelp('Display name shown to clients')
                    ->setAttribute('maxlength', '100')
            ),
        $form->row()
            ->withColumn(
                $form->select('Category')->setName('category')
                    ->setRequired()
                    ->setOptions([
                        '' => '-- Select Category --',
                        'telecommunications' => 'Telecommunications',
                        'security' => 'Security Systems', 
                        'networking' => 'Networking & Infrastructure',
                        'infrastructure' => 'Server & Infrastructure',
                        'support' => 'Support & Maintenance',
                        'consulting' => 'IT Consulting',
                        'other' => 'Other Services'
                    ])
                    ->setHelp('Service category for organization')
            )->withColumn(),
        $form->row()
            ->withColumn(
                $form->textarea('Description')->setName('description')
                    ->setHelp('Detailed description of what this service includes')
                    ->setAttribute('rows', '4')
            )
    ]),

    // Pricing Configuration
    $form->fieldset('Pricing & Quote Settings', 'Service pricing configuration', [
        $form->row()
            ->withColumn(
                $form->number('Base Price (CAD)')->setName('base_price')
                    ->setHelp('Leave empty if service always requires custom quote')
                    ->setAttribute('step', '0.01')
                    ->setAttribute('min', '0')
            )->withColumn(),
        $form->row()
            ->withColumn(
                $form->toggle('Requires Quote')->setName('requires_quote')
                    ->setHelp('Check if this service always needs a custom quote')
            )->withColumn(
                $form->toggle('Allow File Upload')->setName('allows_file_upload') 
                    ->setHelp('Check if clients can upload files for this service')
            )
    ]),

    // Service Configuration
    $form->fieldset('Service Settings', 'Service availability and configuration', [
        $form->row()
            ->withColumn(
                $form->toggle('Service is Active')->setName('is_active')
                    ->setHelp('Only active services will be available for new requests')
            )->withColumn()
    ])
]);