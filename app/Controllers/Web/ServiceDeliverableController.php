<?php

namespace MakerMaker\Controllers\Web;

use MakerMaker\Http\Fields\ServiceDeliverableFields;
use MakerMaker\Models\ServiceDeliverable;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

class ServiceDeliverableController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_deliverables.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add()
    {
        $form = tr_form(ServiceDeliverable::class)->useErrors()->useOld();
        $button = 'Create Service Deliverable';

        return View::new('service_deliverables.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceDeliverableFields $fields, ServiceDeliverable $deliverable, Response $response)
    {
        if (!$deliverable->can('create')) {
            $response->unauthorized('Unauthorized: Service Deliverable not created')->abort();
        }

        $deliverable->save($fields);

        return tr_redirect()->toPage('servicedeliverables', 'index')
            ->withFlash('Service Deliverable Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|ServiceDeliverable $deliverable
     *
     * @return mixed
     */
    public function edit(ServiceDeliverable $deliverable)
    {
        $form = tr_form($deliverable)->useErrors()->useOld();
        $button = 'Update Service Deliverable';

        return View::new('service_deliverables.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliverable $deliverable
     *
     * @return mixed
     */
    public function update(ServiceDeliverable $deliverable, ServiceDeliverableFields $fields, Response $response)
    {
        if (!$deliverable->can('update')) {
            $response->unauthorized('Unauthorized: Service Deliverable not updated')->abort();
        }

        $deliverable->save($fields);

        return tr_redirect()->toPage('servicedeliverables', 'edit', $deliverable->getID())
            ->withFlash('Service Deliverable Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceDeliverable $deliverable
     *
     * @return mixed
     */
    public function show(ServiceDeliverable $deliverable)
    {
        return $deliverable;
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceDeliverable $deliverable
     *
     * @return mixed
     */
    public function delete(ServiceDeliverable $deliverable)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliverable $deliverable
     *
     * @return mixed
     */
    public function destroy(ServiceDeliverable $deliverable, Response $response)
    {
        if (!$deliverable->can('destroy')) {
            $response->unauthorized('Unauthorized: Service Deliverable not deleted')->abort();
        }

        // Check if deliverable can be safely deleted
        if (!$deliverable->canBeDeleted()) {
            return $response->error('Cannot delete deliverable: This deliverable is required and cannot be deleted.');
        }

        $deliverable->delete();

        return $response->warning('Service Deliverable Deleted');
    }

    /**
     * AJAX endpoint to get deliverables for a specific service
     *
     * @param Response $response
     * @return mixed
     */
    public function getServiceDeliverable(Response $response)
    {
        $serviceId = (int)($_GET['service_id'] ?? 0);

        if (!$serviceId) {
            return $response->error('Service ID is required');
        }

        $deliverables = ServiceDeliverable::new()
            ->forService($serviceId)
            ->ordered()
            ->get();

        $deliverableData = [];
        $totalIncludedCost = 0;
        $totalAdditionalCost = 0;

        foreach ($deliverables as $deliverable) {
            $totalCost = $deliverable->getTotalCost();
            
            if ($deliverable->is_included) {
                $totalIncludedCost += $totalCost;
            } else {
                $totalAdditionalCost += $totalCost;
            }
            
            $deliverableData[] = [
                'id' => $deliverable->id,
                'name' => $deliverable->deliverable_name,
                'description' => $deliverable->deliverable_description,
                'type' => $deliverable->deliverable_type,
                'type_text' => $deliverable->getDeliverableTypeText(),
                'is_included' => $deliverable->is_included,
                'quantity' => $deliverable->quantity,
                'unit_of_measure' => $deliverable->unit_of_measure,
                'quantity_with_unit' => $deliverable->getQuantityWithUnit(),
                'additional_cost' => $deliverable->additional_cost,
                'formatted_cost' => $deliverable->getFormattedCost(),
                'total_cost' => $totalCost,
                'formatted_total_cost' => $deliverable->getFormattedTotalCost(),
                'delivery_timeframe' => $deliverable->delivery_timeframe,
                'delivery_timeframe_text' => $deliverable->getDeliveryTimeframeText(),
                'sort_order' => $deliverable->sort_order,
                'status_text' => $deliverable->getStatusText(),
                'is_premium' => $deliverable->isPremium(),
                'is_standard' => $deliverable->isStandard()
            ];
        }

        return $response->json([
            'service_id' => $serviceId,
            'deliverables' => $deliverableData,
            'count' => count($deliverableData),
            'included_count' => count(array_filter($deliverableData, function($d) { return $d['is_included']; })),
            'additional_count' => count(array_filter($deliverableData, function($d) { return !$d['is_included']; })),
            'total_included_cost' => $totalIncludedCost,
            'total_additional_cost' => $totalAdditionalCost,
            'formatted_included_cost' => '$' . number_format($totalIncludedCost, 2),
            'formatted_additional_cost' => '$' . number_format($totalAdditionalCost, 2)
        ]);
    }

    /**
     * AJAX endpoint to reorder deliverables
     *
     * @param Response $response
     * @return mixed
     */
    public function reorderDeliverables(Response $response)
    {
        $deliverableIds = $_POST['deliverable_ids'] ?? [];

        if (!is_array($deliverableIds) || empty($deliverableIds)) {
            return $response->error('Deliverable IDs are required');
        }

        try {
            foreach ($deliverableIds as $order => $deliverableId) {
                $deliverable = ServiceDeliverable::new()->findById((int)$deliverableId);
                if ($deliverable && $deliverable->can('update')) {
                    $deliverable->sort_order = $order + 1;
                    $deliverable->save();
                }
            }

            return $response->json([
                'success' => true,
                'message' => 'Deliverables reordered successfully'
            ]);

        } catch (\Exception $e) {
            return $response->error('Failed to reorder deliverables: ' . $e->getMessage());
        }
    }

    /**
     * Bulk action to update multiple deliverables
     *
     * @param Response $response
     * @return mixed
     */
    public function bulkUpdate(Response $response)
    {
        $deliverableIds = $_POST['deliverable_ids'] ?? [];
        $action = $_POST['bulk_action'] ?? '';

        if (!is_array($deliverableIds) || empty($deliverableIds)) {
            return $response->error('No deliverables selected');
        }

        if (!$action) {
            return $response->error('No action specified');
        }

        $updated = 0;
        $errors = [];

        foreach ($deliverableIds as $deliverableId) {
            try {
                $deliverable = ServiceDeliverable::new()->findById((int)$deliverableId);
                
                if (!$deliverable) {
                    $errors[] = "Deliverable ID {$deliverableId} not found";
                    continue;
                }

                if (!$deliverable->can('update')) {
                    $errors[] = "No permission to update deliverable ID {$deliverableId}";
                    continue;
                }

                switch ($action) {
                    case 'make_included':
                        $deliverable->is_included = true;
                        $deliverable->additional_cost = 0; // Clear cost if making included
                        break;
                    case 'make_additional':
                        $deliverable->is_included = false;
                        break;
                    case 'clear_cost':
                        $deliverable->additional_cost = 0;
                        break;
                    case 'delete':
                        if ($deliverable->canBeDeleted()) {
                            $deliverable->delete();
                            $updated++;
                            continue 2;
                        } else {
                            $errors[] = "Cannot delete deliverable ID {$deliverableId}";
                            continue 2;
                        }
                    default:
                        $errors[] = "Unknown action: {$action}";
                        continue 2;
                }

                $deliverable->save();
                $updated++;

            } catch (\Exception $e) {
                $errors[] = "Error updating deliverable ID {$deliverableId}: " . $e->getMessage();
            }
        }

        if ($updated > 0) {
            $message = "Successfully updated {$updated} deliverable(s)";
            if (!empty($errors)) {
                $message .= " with " . count($errors) . " error(s)";
            }
            return $response->json([
                'success' => true,
                'message' => $message,
                'updated' => $updated,
                'errors' => $errors
            ]);
        } else {
            return $response->error('No deliverables were updated. Errors: ' . implode(', ', $errors));
        }
    }

    /**
     * Generate deliverable report for a service
     *
     * @param Response $response
     * @return mixed
     */
    public function generateDeliverableReport(Response $response)
    {
        $serviceId = (int)($_GET['service_id'] ?? 0);

        if (!$serviceId) {
            return $response->error('Service ID is required');
        }

        $service = \MakerMaker\Models\Service::new()->findById($serviceId);
        if (!$service) {
            return $response->error('Service not found');
        }

        $deliverables = ServiceDeliverable::new()
            ->forService($serviceId)
            ->ordered()
            ->get();

        $report = [
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'sku' => $service->sku
            ],
            'summary' => [
                'total_deliverables' => $deliverables->count(),
                'included_deliverables' => $deliverables->included()->count(),
                'additional_deliverables' => $deliverables->additional()->count(),
                'total_additional_cost' => $deliverables->sum('additional_cost')
            ],
            'by_type' => [],
            'deliverables' => []
        ];

        // Group by type
        $byType = [];
        foreach ($deliverables as $deliverable) {
            $type = $deliverable->deliverable_type;
            if (!isset($byType[$type])) {
                $byType[$type] = [
                    'type' => $type,
                    'type_text' => $deliverable->getDeliverableTypeText(),
                    'count' => 0,
                    'total_cost' => 0
                ];
            }
            $byType[$type]['count']++;
            $byType[$type]['total_cost'] += $deliverable->getTotalCost();
        }
        $report['by_type'] = array_values($byType);

        // Detailed deliverables
        foreach ($deliverables as $deliverable) {
            $report['deliverables'][] = [
                'name' => $deliverable->deliverable_name,
                'type' => $deliverable->getDeliverableTypeText(),
                'quantity' => $deliverable->getQuantityWithUnit(),
                'status' => $deliverable->getStatusText(),
                'cost' => $deliverable->getFormattedTotalCost(),
                'timeframe' => $deliverable->getDeliveryTimeframeText()
            ];
        }

        return $response->json($report);
    }
}