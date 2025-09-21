<?php

/**
 * ServiceCategory Index View
 */

use MakerMaker\Models\ServiceCategory;

$table = tr_table(ServiceCategory::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => 'true',
        'actions' => ['edit', 'view', 'delete']
    ],
    'parentCategory.name' => [
        'label' => 'Parent',
        'sort' => 'true',
        'callback' => function ($item, $value) {
            return $item ?? 'N/A';
        }
    ],
    'slug' => [
        'label' => 'Slug',
        'sort' => 'true'
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
        'sort' => 'true'
    ]
], 'name')->setOrder('ID', 'DESC')->render();

$table;
