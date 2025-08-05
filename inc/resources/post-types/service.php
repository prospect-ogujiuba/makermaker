<?php

// Simple Service Post Type
$service = tr_post_type('Service')->setIcon('cart')->setPosition(2);
$service->setTitlePlaceholder('Enter service name here');
$service->featureless();
$service->setSupports(['title']);
$service->setRest('services');
$service->saveTitleAs(function ($service) {
    return $service->meta->name;
});

$service->setMainForm(function () {
    $form = tr_form();

    echo $form->row()
        ->withColumn(
            $form->text('Service Name')->setName('name')
                ->markLabelRequired()
                ->setHelp('Display name shown to clients (max 100 characters)')
                ->setAttribute('maxlength', '100')
                ->setAttribute('placeholder', 'e.g., VoIP Hosting Premium')
        )
        ->withColumn(
            $form->text('Service Code')->setName('code')
                ->setHelp('Unique identifier (lowercase letters and underscores only, max 255 chars)')
                ->setAttribute('maxlength', '255')
                ->setAttribute('placeholder', 'e.g., voip_hosting_premium')
        );
    echo $form->row()
        ->withColumn(
            $form->textarea('Description')->setName('description')
                ->markLabelRequired()
                ->setHelp('Detailed description of what this service includes')
                ->setAttribute('rows', '4')
                ->setAttribute('placeholder', 'Describe the service features, benefits, and what clients can expect...')
        );


    echo $form->row()
        ->withColumn(
            $form->number('Base Price (CAD)')->setName('base_price')
                ->markLabelRequired()
                ->setHelp('Base price in Canadian dollars (use 0.00 for quote-only services)')
                ->setAttribute('step', '0.01')
                ->setAttribute('min', '0')
                ->setAttribute('placeholder', '0.00')
        )->withColumn(
            $form->text('Icon')->setName('icon')
                ->markLabelRequired()
                ->setHelp('Icon class or identifier (max 100 characters)')
                ->setAttribute('maxlength', '100')
                ->setAttribute('placeholder', 'e.g., fas fa-phone, service-icon-voip')
        );
    echo $form->row()
        ->withColumn(
            $form->select('Active Status')->setName('active')
                ->markLabelRequired()
                ->setOptions([
                    '-- Select Status --' => NULL,
                    'Active' => true,
                    'Inactive' => false,
                ])
                ->setHelp('Only active services are available for new requests')
        )->withColumn();
});

// Optional: Add custom columns to the admin list
$service->addColumn('code', true, 'Service Code', function ($value, $post_id) {
    return get_post_meta($post_id, 'code', true) ?: '—';
});

$service->addColumn('base_price', true, 'Base Price', function ($value, $post_id) {
    $price = get_post_meta($post_id, 'base_price', true);
    return $price ? '$' . number_format($price, 2) : 'Quote Required';
});

$service->addColumn('active', true, 'Active', function ($value, $post_id) {
    $active = get_post_meta($post_id, 'active', true);
    return $active === '1' ? '<span style="color:#008000;">✓</span>' : '<span style="color:#FF2C2C;">✗</span>';
});

// Optional: Remove default columns we don't need
// $service->removeColumn('date');
// $service->removeColumn('author');