<?php

/** @var \TypeRocket\Elements\Form $form */

use MakerMaker\Models\Status;

// Get the status and related data
// $status = Status::new()->load([
//     // 'applicants'
// ])->find($form->getModel()->id);

echo $form->save($button)->setFields([
    // // Show Associations Information Toggle
    // $form->fieldset(
    //     'Status Associations',
    //     'Related status data',
    //     [
    //         $form->row()
    //             ->withColumn($form->toggle('Applicants')->setName('show_applicants'))
    //             ->withColumn($form->toggle('System')->setName('show_system_info')),

    //         // Applicant Information
    //         $form->fieldset(
    //             'Applicant Information',
    //             'Applicants currently in this status',
    //             [
    //                 ...(function () use ($form, $status) {
    //                     $fields = [];
    //                     if ($status && $status->applicants && $status->applicants->count() > 0) {
    //                         // Summary count
    //                         $fields[] = $form->row()
    //                             ->withColumn(
    //                                 $form->text('Total Applicants')
    //                                     ->setAttribute('readonly', 'readonly')
    //                                     ->setAttribute('value', $status->applicants->count())
    //                                     ->setHelp('Number of applicants in this status')
    //                             )->withColumn();

    //                         // List each applicant
    //                         foreach ($status->applicants as $applicant) {
    //                             $fields[] = $form->row()
    //                                 ->withColumn(
    //                                     $form->text('Name')
    //                                         ->setAttribute('readonly', 'readonly')
    //                                         ->setAttribute('value', $applicant->firstname . ' ' . $applicant->lastname)
    //                                 )
    //                                 ->withColumn(
    //                                     $form->text('Since')
    //                                         ->setAttribute('readonly', 'readonly')
    //                                         ->setAttribute('value', $applicant->status_date ?
    //                                             date('Y-m-d', strtotime($applicant->status_date)) : 'N/A')
    //                                 );
    //                         }
    //                     } else {
    //                         $fields[] = $form->row()
    //                             ->withColumn(
    //                                 $form->text('Status')
    //                                     ->setAttribute('readonly', 'readonly')
    //                                     ->setAttribute('value', 'No applicants currently in this status')
    //                             );
    //                     }
    //                     return $fields;
    //                 })()
    //             ]
    //         )->when('show_applicants', '=', true),

    //         // System Info
    //         $form->fieldset(
    //             'System Information',
    //             'System generated status information',
    //             [
    //                 $form->row()
    //                     ->withColumn(
    //                         $form->text('Status ID')->setName('id')
    //                             ->setAttribute('readonly', 'readonly')
    //                             ->setHelp('System-generated Status ID')
    //                     )->withColumn(),
    //                 $form->row()
    //                     ->withColumn(
    //                         $form->text('Created At')
    //                             ->setAttribute('readonly', 'readonly')
    //                             ->setAttribute('value', $status ? date('Y-m-d H:i', strtotime($status->created_at)) : '')
    //                     )
    //                     ->withColumn(
    //                         $form->text('Updated At')
    //                             ->setAttribute('readonly', 'readonly')
    //                             ->setAttribute('value', $status ? date('Y-m-d H:i', strtotime($status->updated_at)) : '')
    //                     )
    //             ]
    //         )->when('show_system_info', '=', true)
    //     ]
    // )->when($form->getModel()->id, '!=', '0'),

    // Status Information
    $form->fieldset('Status Details', 'Basic status information', [
        $form->row()
            ->withColumn(
                $form->text('Status Code')->setName('code')
                    ->setRequired()
                    ->setHelp('Unique identifier for this status')
                    ->setAttribute('maxlength', '20')
                    ->setAttribute('pattern', '^[A-Za-z0-9_-]{1,20}$')
            )->withColumn(
                $form->text('Description')->setName('description')
                    ->setRequired()
                    ->setHelp('Clear description of what this status means')
                    ->setAttribute('maxlength', '100')
            )
    ])
]);
