<?php

namespace MakerMaker\Controllers\Web;

use MakerMaker\Http\Fields\ServiceBundleFields;
use MakerMaker\Models\ServiceBundle;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

class ServiceBundleController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_bundles.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add()
    {
        $form = tr_form(ServiceBundle::class)->useErrors()->useOld();
        $button = 'Create Service Bundle';

        return View::new('service_bundles.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceBundleFields $fields, ServiceBundle $bundle, Response $response)
    {
        if (!$bundle->can('create')) {
            $response->unauthorized('Unauthorized: Service Bundle not created')->abort();
        }

        $bundle->save($fields);

        // Handle service relationships if provided
        $bundleServices = $fields->getProperty('bundle_services');
        if ($bundleServices && is_array($bundleServices)) {
            $this->saveBundleServices($bundle, $bundleServices);
        }

        return tr_redirect()->toPage('servicebundles', 'index')
            ->withFlash('Service Bundle Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|ServiceBundle $bundle
     *
     * @return mixed
     */
    public function edit(ServiceBundle $bundle)
    {
        $form = tr_form($bundle)->useErrors()->useOld();
        $button = 'Update Service Bundle';

        return View::new('service_bundles.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceBundle $bundle
     *
     * @return mixed
     */
    public function update(ServiceBundle $bundle, ServiceBundleFields $fields, Response $response)
    {
        if (!$bundle->can('update')) {
            $response->unauthorized('Unauthorized: Service Bundle not updated')->abort();
        }

        $bundle->save($fields);

        // Handle service relationships if provided
        $bundleServices = $fields->getProperty('bundle_services');
        if ($bundleServices && is_array($bundleServices)) {
            $this->saveBundleServices($bundle, $bundleServices);
        }

        return tr_redirect()->toPage('servicebundles', 'edit', $bundle->getID())
            ->withFlash('Service Bundle Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceBundle $bundle
     *
     * @return mixed
     */
    public function show(ServiceBundle $bundle)
    {
        return $bundle;
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceBundle $bundle
     *
     * @return mixed
     */
    public function delete(ServiceBundle $bundle)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceBundle $bundle
     *
     * @return mixed
     */
    public function destroy(ServiceBundle $bundle, Response $response)
    {
        if (!$bundle->can('destroy')) {
            $response->unauthorized('Unauthorized: Service Bundle not deleted')->abort();
        }

        // Check if bundle can be safely deleted
        if (!$bundle->canBeDeleted()) {
            return $response->error('Cannot delete bundle: This bundle is currently in use and cannot be deleted.');
        }

        // Remove all service relationships first
        $bundle->services()->detach();

        // Delete the bundle
        $bundle->delete();

        return $response->warning('Service Bundle Deleted');
    }

    /**
     * Handle bundle services relationship data
     *
     * @param ServiceBundle $bundle
     * @param array $bundleServicesData
     * @return void
     */
    private function saveBundleServices(ServiceBundle $bundle, array $bundleServicesData)
    {
        $syncData = [];

        foreach ($bundleServicesData as $serviceData) {
            if (empty($serviceData['service_id'])) {
                continue;
            }

            $serviceId = $serviceData['service_id'];
            $syncData[$serviceId] = [
                'quantity' => max(1, (int) ($serviceData['quantity'] ?? 1)),
                'is_optional' => !empty($serviceData['is_optional']) ? 1 : 0,
                'sort_order' => (int) ($serviceData['sort_order'] ?? 0)
            ];
        }

        if (!empty($syncData)) {
            $bundle->services()->sync($syncData);
        }
    }

    /**
     * AJAX endpoint to get bundle pricing information
     *
     * @param ServiceBundle $bundle
     * @param Response $response
     * @return mixed
     */
    public function getPricingInfo(ServiceBundle $bundle, Response $response)
    {
        if (!$bundle->can('read')) {
            $response->unauthorized('Unauthorized')->abort();
        }

        $data = [
            'bundle_price' => $bundle->base_price,
            'individual_value' => $bundle->getIndividualServicesValue(),
            'discount_amount' => $bundle->getDiscountAmount(),
            'discount_percentage' => $bundle->getActualDiscountPercentage(),
            'services_count' => $bundle->getServicesCount(),
            'active_services_count' => $bundle->getActiveServicesCount(),
            'formatted_price' => $bundle->getFormattedPrice(),
            'is_available' => $bundle->isAvailable()
        ];

        return $response->json($data);
    }

    /**
     * AJAX endpoint to add service to bundle
     *
     * @param ServiceBundle $bundle
     * @param Response $response
     * @return mixed
     */
    public function addServiceToBundle(ServiceBundle $bundle, Response $response)
    {
        if (!$bundle->can('update')) {
            $response->unauthorized('Unauthorized')->abort();
        }

        $serviceId = (int) ($_POST['service_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $isOptional = !empty($_POST['is_optional']);
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);

        if (!$serviceId) {
            return $response->error('Service ID is required');
        }

        // Check if service exists
        $service = \MakerMaker\Models\Service::new()->findById($serviceId);
        if (!$service) {
            return $response->error('Service not found');
        }

        // Add service to bundle
        try {
            $bundle->addService($serviceId, $quantity, $isOptional, $sortOrder);
            return $response->json([
                'success' => true,
                'message' => 'Service added to bundle',
                'services_count' => $bundle->getServicesCount()
            ]);
        } catch (\Exception $e) {
            return $response->error('Failed to add service to bundle: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint to remove service from bundle
     *
     * @param ServiceBundle $bundle
     * @param Response $response
     * @return mixed
     */
    public function removeServiceFromBundle(ServiceBundle $bundle, Response $response)
    {
        if (!$bundle->can('update')) {
            $response->unauthorized('Unauthorized')->abort();
        }

        $serviceId = (int) ($_POST['service_id'] ?? 0);

        if (!$serviceId) {
            return $response->error('Service ID is required');
        }

        try {
            $bundle->removeService($serviceId);
            return $response->json([
                'success' => true,
                'message' => 'Service removed from bundle',
                'services_count' => $bundle->getServicesCount()
            ]);
        } catch (\Exception $e) {
            return $response->error('Failed to remove service from bundle: ' . $e->getMessage());
        }
    }
}
