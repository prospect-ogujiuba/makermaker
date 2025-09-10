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
        'actions' => ['edit', 'view', 'delete'],
    ],

    'deliverymethod.name' => [
        'label' => 'Delivery Method',
        'sort' => true,
    ],
    
    'lead_time_days' => [
        'label' => 'Lead Time (Days)',
        'sort' => true,
    ],
    
    'sla_hours' => [
        'label' => 'SLA Hours',
        'sort' => true,
    ],
    
    'surcharge' => [
        'label' => 'Surcharge',
        'sort' => true,
    ],
    
    'created_at' => [
        'label' => 'Created',
        'sort' => true,
        'callback' => function ($value, $item) {
            return date('M j, Y g:i A', strtotime($value));
        }
    ],

    'updated_at' => [
        'label' => 'Updated',
        'sort' => true,
        'callback' => function ($value, $item) {
            return date('M j, Y g:i A', strtotime($value));
        }
    ],
    'createdBy.user_nicename' => [
        'label' => 'Addon Service Name',
        'sort' => true,
    ],
    'updatedBy.user_nicename' => [
        'label' => 'Addon Service Name',
        'sort' => true,
    ],

    'id' => [
        'label' => 'ID',
        'sort' => true
    ]
], 'service.name',)->setOrder('id', 'DESC')->render();
