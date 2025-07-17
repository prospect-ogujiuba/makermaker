<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\Status;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Request;
use TypeRocket\Http\Response;

class StatusController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        // return tr_view('statuses.index');
        return View::new('statuses.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add()
    {
        $form = tr_form(Status::class);
        $button = 'Add';

        // return tr_view('statuses.form', compact('form', 'button'));
        return View::new('statuses.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(Request $request, Status $status)
    {
        $status->save($request->fields());

        return tr_redirect()->toPage('status', 'index')
            ->withFlash('Status Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|Status $status
     *
     * @return mixed
     */
    public function edit(Status $status)
    {
        $form = tr_form($status);
        $button = 'Update';

        // return tr_view('statuses.form', compact('form', 'button'));
        return View::new('statuses.form', compact('form', 'button'));

    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Status $status
     *
     * @return mixed
     */
    public function update(Status $status, Request $request)
    {
        $status->save($request->fields());

        return tr_redirect()->toPage('status', 'edit', $status->getID())
            ->withFlash('Status Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|Status $status
     *
     * @return mixed
     */
    public function show(Status $status)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|Status $status
     *
     * @return mixed
     */
    public function delete(Status $status)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Status $status
     *
     * @return mixed
     */
    public function destroy(Status $status, Response $response)
    {

        if (!$status->can('delete')) {
            $response->unauthorized('Unauthorized: Status not deleted')->abort();
        }

        $status->delete();

        return $response->warning('Status Deleted');
    }
}