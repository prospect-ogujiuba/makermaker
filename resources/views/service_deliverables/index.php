<?php

/**
 * ServiceDeliverable Index View
 */

use MakerMaker\Models\ServiceDeliverable;

$table = tr_table(ServiceDeliverable::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'deliverable.name' => [
        'label' => 'Deliverable Name',
        'sort' => true
    ],
    'is_optional' => [
        'label' => 'Optional',
        'sort' => true,
        'callback' => function ($item, $value) {
            return $item ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
        }
    ],
    'sequence_order' => [
        'label' => 'Sequence',
        'sort' => true
    ],
    'created_at' => [
        'label' => 'Created At',
        'sort' => true
    ],
    'updated_at' => [
        'label' => 'Updated At',
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
], 'service.name')->setOrder('id', 'DESC')->render();
