<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\PriceRecordFields;
use MakerMaker\Models\PriceRecord;
use MakerMaker\Models\ServicePrice;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Http\Request;
use TypeRocket\Models\AuthUser;

class PriceRecordController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('price_records.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {

        $form = tr_form(PriceRecord::class)->useErrors()->useOld()->useConfirm();
        return View::new('price_records.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(PriceRecordFields $fields, PriceRecord $price_record, Response $response, AuthUser $user)
    {
        if (!$price_record->can('create')) {
            $response->unauthorized('Unauthorized: Price Record not created')->abort();
        }

        $price_record->save($fields);

        return tr_redirect()->toPage('PriceRecord', 'index')
            ->withFlash('Price Record created');
    }

    /**
     * The edit page for admin
     *
     * @param string|PriceRecord $price_record
     *
     * @return mixed
     */
    public function edit(PriceRecord $price_record, AuthUser $user)
    {

        $current_id = $price_record->getID();
        $changedBy = $price_record->changedBy;

        $form = tr_form($price_record)->useErrors()->useOld()->useConfirm();
        return View::new('price_records.form', compact('form', 'changedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param PriceRecord $price_record
     *
     * @return mixed
     */
    public function update(PriceRecord $price_record, PriceRecordFields $fields, Response $response, AuthUser $user)
    {

        if (!$price_record->can('update')) {
            $response->unauthorized('Unauthorized: Price Record not updated')->abort();
        }

        $price_record->save($fields);

        return tr_redirect()->toPage('PriceRecord', 'edit', $price_record->getID())
            ->withFlash('Price Record updated');

        // tr_redirect()->back()->withFlash('Price Record records are read only.', 'warning');
    }

    /**
     * The show page for admin
     *
     * @param string|PriceRecord $price_record
     *
     * @return mixed
     */
    public function show(PriceRecord $price_record, AuthUser $user)
    {

        $current_id = $price_record->getID();
        $changedBy = $price_record->changedBy;

        $form = tr_form($price_record)->useErrors()->useOld()->useConfirm();
        return View::new('price_records.form', compact('form', 'changedBy', 'user'));
    }

    /**
     * The delete page for admin
     *
     * @param string|PriceRecord $price_record
     *
     * @return mixed
     */
    public function delete(PriceRecord $price_record)
    {
        //
    }

    /**
     * Destroy item
     * 
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|PriceRecord $price_record
     *
     * @return mixed
     */
    public function destroy(PriceRecord $price_record, Response $response)
    {
        if (!$price_record->can('destroy')) {
            return $response->unauthorized('Unauthorized: Price Record not deleted');
        }

        return $response->error('Price Record cannot be manually deleted.')->setData('price_record', $price_record);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $price_record = PriceRecord::new()->get();

            if (empty($price_record)) {
                return $response
                    ->setData('price_record', [])
                    ->setMessage('No Price Record found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('price_record', $price_record)
                ->setMessage('Price Records retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Price Record indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Price Records: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param PriceRecord $price_record
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(PriceRecord $price_record, Response $response)
    {
        try {
            $price_record = PriceRecord::new()->find($price_record->getID());

            if (empty($price_record)) {
                return $response
                    ->setData('price_record', null)
                    ->setMessage('Price Record not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('price_record', $price_record)
                ->setMessage('Price Record retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Price Record showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Price Record', 'error')
                ->setStatus(500);
        }
    }
}
