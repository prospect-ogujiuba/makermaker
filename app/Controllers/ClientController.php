<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ClientFields;
use MakerMaker\Models\Client;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Redirect;
use TypeRocket\Http\Request;
use TypeRocket\Http\Response;
use TypeRocket\Pro\Utility\Log;

class ClientController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('clients.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add()
    {
        $form = tr_form(Client::class)->useErrors()->useOld();
        $button = 'Add';

        return View::new('clients.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ClientFields $fields, Client $client, Response $response)
    {
        if (!$client->can('create')) {
            $response->unauthorized('Unauthorized: Client not created')->abort();
        }

        // Auto-set last contact date on creation
        // $fields->fillModel($client);
        $client->last_contact_at = date('Y-m-d H:i:s');
        $client->save();

        return tr_redirect()->toPage('client', 'index')
            ->withFlash('Client Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|Client $client
     *
     * @return mixed
     */
    public function edit(Client $client)
    {
        $form = tr_form($client)->useErrors()->useOld();
        $button = 'Update';

        return View::new('clients.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Client $client
     *
     * @return mixed
     */
    public function update(Client $client, ClientFields $fields, Response $response)
    {
        if (!$client->can('update')) {
            $response->unauthorized('Unauthorized: Client not updated')->abort();
        }

        $client->save($fields);

        return tr_redirect()->toPage('client', 'edit', $client->getID())
            ->withFlash('Client Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|Client $client
     *
     * @return mixed
     */
    public function show(Client $client, Response $response)
    {
        // Load relationships when they're available
        // $client->load(['serviceRequests', 'quotes', 'payments']);
        
        if (current_user_can('manage_clients')) {
            echo $client;
        } else {
            $response->unauthorized('Unauthorized: You are not authorized to view this Client')->abort();
        }
    }

    /**
     * The delete page for admin
     *
     * @param string|Client $client
     *
     * @return mixed
     */
    public function delete(Client $client)
    {
        // TODO: Implement delete() method if needed
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Client $client
     *
     * @return mixed
     */
    public function destroy(Client $client, Response $response)
    {
        if (!$client->can('destroy')) {
            $response->unauthorized('Unauthorized: Client not deleted')->abort();
        }

        $client->delete();

        return $response->warning('Client Deleted');
    }
}