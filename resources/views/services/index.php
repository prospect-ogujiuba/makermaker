<?php
$table = tr_table(\MakerMaker\Models\Service::class);

$table->setColumns([
    'code' => [
        'label' => 'Service Code',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'name' => [
        'label' => 'Service Name',
        'sort' => true,
    ],
    'category' => [
        'label' => 'Category',
        'sort' => true,
    ],
    'base_price' => [
        'label' => 'Base Price',
        'sort' => true,
        'callback' => function($value) {
            return $value ? '$' . number_format($value, 2) : 'Quote Required';
        }
    ],
    'is_active' => [
        'label' => 'Active',
        'sort' => true,
        'callback' => function($value) {
            return $value ? '✓' : '✗';
        }
    ],
    'requires_quote' => [
        'label' => 'Quote Required',
        'sort' => true,
        'callback' => function($value) {
            return $value ? '✓' : '✗';
        }
    ],
    'allows_file_upload' => [
        'label' => 'File Upload',
        'sort' => true,
        'callback' => function($value) {
            return $value ? '✓' : '✗';
        }
    ],
    'id' => [
        'label' => 'ID',
        'sort' => true,
    ]
], 'id');
$table->render();