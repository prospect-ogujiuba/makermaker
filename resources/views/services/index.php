<?php

/**
 * Service Index View
 */

use MakerMaker\Models\Service;

$table = tr_table(Service::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'sku' => [
        'label' => 'SKU',
        'sort' => true,
        'callback' => function ($value) {
            return $value ? "<code>{$value}</code>" : '<span class="text-muted">No SKU</span>';
        }
    ],
    'category_id' => [
        'label' => 'Category',
        'callback' => function ($value, $item) {
            return $item->category ? $item->category->name : '<span class="text-muted">N/A</span>';
        }
    ],
    'service_type_id' => [
        'label' => 'Type',
        'callback' => function ($value, $item) {
            return $item->serviceType ? $item->serviceType->name : '<span class="text-muted">N/A</span>';
        }
    ],
    'complexity_id' => [
        'label' => 'Complexity',
        'callback' => function ($value, $item) {
            return $item->complexity ? $item->complexity->name : '<span class="text-muted">N/A</span>';
        }
    ],
    'skill_level' => [
        'label' => 'Skill Level',
        'sort' => true,
        'callback' => function ($value) {
            if (!$value) return '<span class="text-muted">Not set</span>';

            $badges = [
                'entry' => '<span class="badge badge-light">Entry</span>',
                'intermediate' => '<span class="badge badge-info">Intermediate</span>',
                'advanced' => '<span class="badge badge-primary">Advanced</span>',
                'expert' => '<span class="badge badge-warning">Expert</span>',
                'specialist' => '<span class="badge badge-danger">Specialist</span>'
            ];
            return $badges[$value] ?? $value;
        }
    ],
    'quantity_range' => [
        'label' => 'Qty Range',
        'callback' => function ($value, $item) {
            $min = number_format($item->minimum_quantity, 3);
            $max = $item->maximum_quantity ? number_format($item->maximum_quantity, 3) : '∞';
            return "{$min} - {$max}";
        }
    ],
    'estimated_hours' => [
        'label' => 'Est. Hours',
        'sort' => true,
        'callback' => function ($value) {
            return $value ?
                number_format($value, 2) . 'h' :
                '<span class="text-muted">N/A</span>';
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
    'id' => [
        'label' => 'ID',
        'sort' => true
    ]
], 'name')->setOrder('id', 'DESC')->render();

$table;
