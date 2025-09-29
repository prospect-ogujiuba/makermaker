<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\PriceHistoryFields;
use MakerMaker\Models\PriceHistory;
use MakerMaker\Models\ServicePrice;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Http\Request;
use TypeRocket\Models\AuthUser;

class PriceHistoryController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('price_history.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {

        $form = tr_form(PriceHistory::class)->useErrors()->useOld()->useConfirm();
        return View::new('price_history.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(PriceHistoryFields $fields, PriceHistory $price_history, Response $response, AuthUser $user)
    {
        if (!$price_history->can('create')) {
            $response->unauthorized('Unauthorized: Price History not created')->abort();
        }

        $price_history->save($fields);

        return tr_redirect()->toPage('pricehistory', 'index')
            ->withFlash('Price History created');
    }

    /**
     * The edit page for admin
     *
     * @param string|PriceHistory $price_history
     *
     * @return mixed
     */
    public function edit(PriceHistory $price_history, AuthUser $user)
    {

        $current_id = $price_history->getID();
        $changedBy = $price_history->changedBy;

        $form = tr_form($price_history)->useErrors()->useOld()->useConfirm();
        return View::new('price_history.form', compact('form', 'changedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param PriceHistory $price_history
     *
     * @return mixed
     */
    public function update(PriceHistory $price_history, PriceHistoryFields $fields, Response $response, AuthUser $user)
    {

        if (!$price_history->can('update')) {
            $response->unauthorized('Unauthorized: Price History not updated')->abort();
        }

        $price_history->save($fields);

        return tr_redirect()->toPage('pricehistory', 'edit', $price_history->getID())
            ->withFlash('Price History updated');

        // tr_redirect()->back()->withFlash('Price history records are read only.', 'warning');
    }

    /**
     * The show page for admin
     *
     * @param string|PriceHistory $price_history
     *
     * @return mixed
     */
    public function show(PriceHistory $price_history, AuthUser $user)
    {

        $current_id = $price_history->getID();
        $changedBy = $price_history->changedBy;

        $form = tr_form($price_history)->useErrors()->useOld()->useConfirm();
        return View::new('price_history.form', compact('form', 'changedBy', 'user'));
    }

    /**
     * The delete page for admin
     *
     * @param string|PriceHistory $price_history
     *
     * @return mixed
     */
    public function delete(PriceHistory $price_history)
    {
        //
    }

    /**
     * Destroy item
     * 
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|PriceHistory $price_history
     *
     * @return mixed
     */
    public function destroy(PriceHistory $price_history, Response $response)
    {
        if (!$price_history->can('destroy')) {
            return $response->unauthorized('Unauthorized: Price History not deleted');
        }

        return $response->error('Price History cannot be manually deleted.')->setData('price_history', $price_history);
    }

     /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $price_history = PriceHistory::new()->get();

            if (empty($price_history)) {
                return $response
                    ->setData('price_history', [])
                    ->setMessage('No Price History found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('price_history', $price_history)
                ->setMessage('Price History retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Price History indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Price History: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param PriceHistory $price_history
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(PriceHistory $price_history, Response $response)
    {
        try {
            $price_history = PriceHistory::new()->find($price_history->getID());

            if (empty($price_history)) {
                return $response
                    ->setData('price_history', null)
                    ->setMessage('Price History not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('price_history', $price_history)
                ->setMessage('Price History retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Price History showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Price History', 'error')
                ->setStatus(500);
        }
    }
}
