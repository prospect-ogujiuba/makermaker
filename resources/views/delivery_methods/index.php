<?php

/**
 * DeliveryMethod Index View
 */

use MakerMaker\Models\DeliveryMethod;

$table = tr_table(DeliveryMethod::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'code' => [
        'label' => 'Code',
        'sort' => true
    ],
    'requires_site_access' => [
        'label' => 'Site Access',
        'sort' => true,
        'callback' => function($item, $value) {
            return $value ? "<i class='bi bi-check-circle-fill' style='color: green;'></i>" : "<i class='bi bi-x-circle-fill' style='color: gray;'></i>";
        }
    ],
    'supports_remote' => [
        'label' => 'Remote',
        'sort' => true,
        'callback' => function($item, $value) {
            return $value ? "<i class='bi bi-check-circle-fill' style='color: green;'></i>" : "<i class='bi bi-x-circle-fill' style='color: gray;'></i>";
        }
    ],
    'default_lead_time_days' => [
        'label' => 'Lead Time (Days)',
        'sort' => true
    ],
    'default_sla_hours' => [
        'label' => 'SLA (Hours)',
        'sort' => true
    ],
    'created_at' => [
        'label' => 'Created',
        'sort' => true
    ],
    'updated_at' => [
        'label' => 'Updated',
        'sort' => true
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
], 'name')->setOrder('id', 'DESC')->render();
