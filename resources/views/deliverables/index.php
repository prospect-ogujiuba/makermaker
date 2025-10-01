<?php

/**
 * Deliverable Index View
 */

use MakerMaker\Models\Deliverable;

$table = tr_table(Deliverable::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'deliverable_type' => [
        'label' => 'Type',
        'sort' => true,
        'callback' => function ($value) {
            return "<code>{$value}</code>";
        }
    ],
    'estimated_effort_hours' => [
        'label' => 'Est. Hours',
        'sort' => true
    ],
    'requires_approval' => [
        'label' => 'Approval Required',
        'sort' => true,
        'callback' => function ($item, $value) {
            return $item ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
        }
    ],
    'description' => [
        'label' => 'Description',
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
