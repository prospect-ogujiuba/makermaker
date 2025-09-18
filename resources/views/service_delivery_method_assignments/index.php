<?php

/**
 * ServiceDeliveryMethodAssignment Index View
 */

use MakerMaker\Models\ServiceDeliveryMethodAssignment;

$table = tr_table(ServiceDeliveryMethodAssignment::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'deliverymethod.name' => [
        'label' => 'Delivery Method',
        'sort' => true
    ],
    'lead_time_days' => [
        'label' => 'Lead Time (Days)',
        'sort' => true
    ],
    'sla_hours' => [
        'label' => 'SLA Hours',
        'sort' => true
    ],
    'surcharge' => [
        'label' => 'Surcharge',
        'sort' => true
    ],
    'created_at' => [
        'label' => 'Created At',
        'sort' => 'true'
    ],
    'updated_at' => [
        'label' => 'Updated At',
        'sort' => 'true'
    ],
    'createdBy.user_nicename' => [
        'label' => 'Created By'
    ],
    'updatedBy.user_nicename' => [
        'label' => 'Updated By'
    ],
    'id' => [
        'label' => 'ID',
        'sort' => true
    ]
], 'service.name',)->setOrder('id', 'DESC')->render();
