<?php
$table = tr_table(\MakerMaker\Models\Status::class);
$table->setColumns([
    'code' => [
        'label' => 'Status Code',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'description' => [
        'label' => 'Description',
        'sort' => true,
    ],
    'id' => [
        'label' => 'ID',
        'sort' => true,
    ]
], 'id');
$table->render();
