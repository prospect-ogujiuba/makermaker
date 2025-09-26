<?php

/**
 * ServiceType Index View
 */

use MakerMaker\Models\ServiceType;

$table = tr_table(ServiceType::class);

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
    'description' => [
        'label' => 'Description',
        'callback' => function ($value) {
            return $value ?
                (strlen($value) > 60 ? substr($value, 0, 57) . '...' : $value) :
                '<span class="text-muted">No description</span>';
        }
    ],
    'delivery_options' => [
        'label' => 'Delivery',
        'callback' => function ($value, $item) {
            $badges = [];
            if ($item->requires_site_visit) {
                $badges[] = '<span class="badge tr-warning">On-Site</span>';
            }
            if ($item->supports_remote) {
                $badges[] = '<span class="badge tr-table-tag">Remote</span>';
            }
            return implode(' ', $badges) ?: '<span class="text-muted">Not specified</span>';
        }
    ],
    'estimated_duration_hours' => [
        'label' => 'Est. Hours',
        'sort' => true,
        'callback' => function ($value) {
            return $value ?
                number_format($value, 2) . 'h' :
                '<span class="text-muted">N/A</span>';
        }
    ],
    'services_count' => [
        'label' => 'Services',
        'callback' => function ($value, $item) {
            $count = count((array)$item->services);
            return $count > 0 ?
                "<span class=\"badge badge-secondary\">{$count}</span>" :
                '<span class="text-muted">0</span>';
        }
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
], 'name')->setOrder('id', 'DESC')->render();

$table;
