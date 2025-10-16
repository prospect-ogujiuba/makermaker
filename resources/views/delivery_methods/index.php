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
        'sort' => true,
        'callback' => function ($value) {
            return "<code>{$value}</code>";
        }
    ],
    'requires_site_access' => [
        'label' => 'Site Access',
        'sort' => true,
        'callback' => function ($value) {
            return $value ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
        }
    ],
    'supports_remote' => [
        'label' => 'Remote',
        'sort' => true,
        'callback' => function ($item, $value) {
            return $value ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
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
