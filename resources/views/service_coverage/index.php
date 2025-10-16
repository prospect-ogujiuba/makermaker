<?php

/**
 * ServiceCoverage Index View
 */

use MakerMaker\Models\ServiceCoverage;

$table = tr_table(ServiceCoverage::class);

$table->setBulkActions(tr_form()->useConfirm(), []);
$table->setColumns([
    'service.name' => [
        'label' => 'Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'coverageArea.name' => [
        'label' => 'Coverage Area',
        'sort' => true
    ],
    'delivery_surcharge' => [
        'label' => 'Surcharge',
        'sort' => true
    ],
    'lead_time_adjustment_days' => [
        'label' => 'Lead Time Adj.',
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
