<?php

use \MakerMaker\Models\Service;
use \MakerMaker\Controllers\ServiceController;

// Register custom post type
$service = tr_post_type('Service')
    ->setIcon('cart')
    ->setPosition(2)
    ->setTitlePlaceholder('Enter service name here')
    ->setSupports(['none'])
    ->setRest('services')
    ->setModelClass(Service::class)
    ->setHandler(ServiceController::class)
    ->saveTitleAs(function (Service $service) {
        return $service->meta->name;
    })
    // Add slug generation based on service name or code
    ->savePostNameAs(function (Service $service) {
        // Use service code if available, otherwise use service name
        $slug_source = !empty($service->meta->code) ? $service->meta->code : $service->meta->name;
        
        if (empty($slug_source)) {
            return 'service-' . time(); // Fallback with timestamp
        }
        
        // Clean and format the slug
        $slug = strtolower($slug_source);
        $slug = preg_replace('/[^a-z0-9\-_]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug); // Remove multiple dashes
        $slug = trim($slug, '-');
        
        return $slug;
    });

// Define the main form
$service->setMainForm(function () {
    $form = tr_form();

    echo $form->row()->withColumn(
        $form->text('Service Name')->setName('name')
            ->markLabelRequired()
            ->setHelp('Display name shown to clients (max 100 characters)')
            ->setAttribute('maxlength', 100)
            ->setAttribute('placeholder', 'e.g., VoIP Hosting Premium')
    )->withColumn(
        $form->text('Service Code')->setName('code')
            ->setHelp('Unique identifier (lowercase letters and underscores only, max 255 chars)')
            ->setAttribute('maxlength', 255)
            ->setAttribute('placeholder', 'e.g., voip_hosting_premium')
    );

    echo $form->row()->withColumn(
        $form->textarea('Description')->setName('description')
            ->markLabelRequired()
            ->setHelp('Detailed description of what this service includes')
            ->setAttribute('rows', 4)
            ->setAttribute('placeholder', 'Describe the service features, benefits, and what clients can expect...')
    );

    echo $form->row()->withColumn(
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
            ->setAttribute('maxlength', 100)
            ->setAttribute('placeholder', 'e.g., fas fa-phone, service-icon-voip')
    );

    echo $form->row()->withColumn(
        $form->select('Active Status')->setName('active')
            ->markLabelRequired()
            ->setOptions([
                '-- Select Status --' => null,
                'Active' => true,
                'Inactive' => false,
            ])
            ->setHelp('Only active services are available for new requests')
    );
});

// Admin columns
$service->addColumn('Name', true, 'Service Name');
$service->addColumn('Code', true);
$service->addColumn('Base Price', true, null, function ($value) {
    return $value ? '$' . number_format($value, 2) : '-';
});
$service->addColumn('Active', false, null, function ($value) {
    return $value == '1'
    ? '<span style="color:#008000;">✓</span>'
    : '<span style="color:#FF2C2C;">✗</span>';
});
$service->addColumn('post_id', false, 'Post ID', function ($value, $item) {
    return $item['post_id'];
});
$service->addColumn('Icon', true);