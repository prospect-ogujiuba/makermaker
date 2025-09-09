<?php

/**
 * ServiceAttributeValue Form
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\ServiceAttributeDefinition;

// Form instance
echo $form->open();

// Tab Layout
$tabs = tr_tabs()
    ->setFooter($form->save())
    ->layoutLeft();

// Main Tab
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Attribute Value',
        'Define the attribute value for this service attribute definition',
        [
            $form->row()
                ->withColumn(
                    $form->select('service_id')
                        ->setLabel('Service')
                        ->setHelp('Service this attribute value applies to')
                        ->setOptions(['Select Service' => NULL])
                        ->setModelOptions(Service::class, 'name', 'id')
                        ->markLabelRequired()
                )->withColumn(),
            $form->row()->when('service_id', '!=', '')
                ->withColumn(
                    $form->select('attribute_definition_id')
                        ->setLabel('Attribute Definition')
                        ->setHelp('Attribute definition this value applies to')
                        ->setOptions($options)
                        ->markLabelRequired()
                )
                ->withColumn(
                    $form->text('value')
                        ->setLabel('Attribute Value')
                        ->setHelp('Enter the value for this attribute (will be automatically typed based on the selected attribute definition)')
                        ->markLabelRequired()
                )
        ]
    )
])->setDescription('Attribute Value');

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
                            ->setHelp('User who originally created this record')
                            ->setAttribute('value', $createdBy->user_nicename ?? '')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    )
                    ->withColumn(
                        $form->text('updated_by_user')
                            ->setLabel('Last Updated By')
                            ->setHelp('User who last updated this record')
                            ->setAttribute('value', $updatedBy->user_nicename ?? '')
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
}

// Render the complete tabbed interface
$tabs->render();

echo $form->close(); ?>

<script>
const attributeData = <?php echo json_encode($jsData); ?>;
const serviceData = <?php 
// Get services with their service_type_id
$services = Service::new()->findAll()->get();
$serviceTypes = [];
foreach ($services as $service) {
    $serviceTypes[$service->id] = $service->service_type_id;
}
echo json_encode($serviceTypes);
?>;

document.addEventListener('DOMContentLoaded', function() {
    // TypeRocket uses complex name attributes, so we need to be more specific
    const serviceSelect = document.querySelector('select[name*="service_id"]');
    const attributeSelect = document.querySelector('select[name*="attribute_definition_id"]');
    
    console.log('Service select:', serviceSelect); // Debug
    console.log('Attribute select:', attributeSelect); // Debug
    
    if (serviceSelect && attributeSelect) {
        // Store original options for restoration
        const originalOptions = attributeSelect.innerHTML;
        
        serviceSelect.addEventListener('change', function() {
            const selectedServiceId = this.value;
            const serviceTypeId = serviceData[selectedServiceId];
            
            console.log('Selected service ID:', selectedServiceId); // Debug
            console.log('Service type ID:', serviceTypeId); // Debug
            
            // Clear current options except the first one
            attributeSelect.innerHTML = '<option value="">Select Attribute Definition</option>';
            
            if (!selectedServiceId) {
                // If no service selected, show all options
                attributeSelect.innerHTML = originalOptions;
                return;
            }
            
            // Add filtered options
            for (const [attrId, attrData] of Object.entries(attributeData)) {
                if (!serviceTypeId || attrData.service_type_id == serviceTypeId) {
                    const option = document.createElement('option');
                    option.value = attrId;
                    option.textContent = attrData.text;
                    attributeSelect.appendChild(option);
                }
            }
        });
    } else {
        console.log('Could not find form elements'); // Debug
    }
});
</script>