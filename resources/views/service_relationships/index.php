<?php

/**
 * ServiceRelationship Index View
 */

use MakerMaker\Models\ServiceRelationship;

$table = tr_table(ServiceRelationship::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Service  Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'relation_type' => [
        'label' => 'Relation',
        'sort' => true
    ],
    'relatedservice.name' => [
        'label' => 'Service Name'
    ],
    'notes' => [
        'label' => 'Notes',
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
], 'service.name')->setOrder('id', 'DESC')->render();
