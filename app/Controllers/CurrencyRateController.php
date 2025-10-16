<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\CurrencyRate;
use MakerMaker\Http\Fields\CurrencyRateFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Models\AuthUser;

class CurrencyRateController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('currency_rates.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(CurrencyRate::class)->useErrors()->useOld()->useConfirm();
        return View::new('currency_rates.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(CurrencyRateFields $fields, CurrencyRate $currency_rate, Response $response, AuthUser $user)
    {
        if (!$currency_rate->can('create')) {
            $response->unauthorized('Unauthorized: Currency Rate not created')->abort();
        }

        $currency_rate->created_by = $user->ID;
        $currency_rate->updated_by = $user->ID;

        $currency_rate->save($fields);

        return tr_redirect()->toPage('currencyrate', 'index')
            ->withFlash('Currency Rate created');
    }

    /**
     * The edit page for admin
     *
     * @param CurrencyRate $currency_rate
     *
     * @return mixed
     */
    public function edit(CurrencyRate $currency_rate, AuthUser $user)
    {
        $current_id = $currency_rate->getID();
        $createdBy = $currency_rate->createdBy;
        $updatedBy = $currency_rate->updatedBy;

        $form = tr_form($currency_rate)->useErrors()->useOld()->useConfirm();
        return View::new('currency_rates.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param CurrencyRate $currency_rate
     *
     * @return mixed
     */
    public function update(CurrencyRate $currency_rate, CurrencyRateFields $fields, Response $response, AuthUser $user)
    {
        if (!$currency_rate->can('update')) {
            $response->unauthorized('Unauthorized: Currency Rate not updated')->abort();
        }

        $currency_rate->updated_by = $user->ID;

        $currency_rate->save($fields);

        return tr_redirect()->toPage('currencyrate', 'edit', $currency_rate->getID())
            ->withFlash('Currency Rate updated');
    }

    /**
     * The show page for admin
     *
     * @param CurrencyRate $currency_rate
     *
     * @return mixed
     */
    public function show(CurrencyRate $currency_rate)
    {
        return $currency_rate;
    }

    /**
     * The delete page for admin
     *
     * @param CurrencyRate $currency_rate
     *
     * @return mixed
     */
    public function delete(CurrencyRate $currency_rate)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param CurrencyRate $currency_rate
     *
     * @return mixed
     */
    public function destroy(CurrencyRate $currency_rate, Response $response)
    {
        if (!$currency_rate->can('destroy')) {
            return $response->unauthorized('Unauthorized: Currency Rate not deleted');
        }

        $pricesCount = $currency_rate->count();

        if ($pricesCount > 0) {
            return $response
                ->error("Cannot delete: {$pricesCount} Service Price(s) still use this currency Rate.")
                ->setStatus(409)
                ->setData('currency_rate', $currency_rate);
        }

        $deleted = $currency_rate->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Currency Rate deleted.')->setData('currency_rate', $currency_rate);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $currency_rates = CurrencyRate::new()->get();

            if (empty($currency_rates)) {
                return $response
                    ->setData('currency_rates', [])
                    ->setMessage('No Currency Rates found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('currency_rates', $currency_rates)
                ->setMessage('Currency Rates retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Currency Rate indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Currency Rates: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param CurrencyRate $currency_rate
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(CurrencyRate $currency_rate, Response $response)
    {
        try {
            $currency_rate = CurrencyRate::new()
                ->find($currency_rate->getID());

            if (empty($currency_rate)) {
                return $response
                    ->setData('currency_rate', null)
                    ->setMessage('Currency Rate not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('currency_rate', $currency_rate)
                ->setMessage('Currency Rate retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Currency Rate showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Currency Rate', 'error')
                ->setStatus(500);
        }
    }
}
