<?php

namespace MakerMaker\Controllers\Web;

use MakerMaker\Http\Fields\ServiceAttributeFields;
use MakerMaker\Models\ServiceAttribute;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

class ServiceAttributeController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_attributes.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add()
    {
        $form = tr_form(ServiceAttribute::class)->useErrors()->useOld();
        $button = 'Create Service Attribute';

        return View::new('service_attributes.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceAttributeFields $fields, ServiceAttribute $attribute, Response $response)
    {
        if (!$attribute->can('create')) {
            $response->unauthorized('Unauthorized: Service Attribute not created')->abort();
        }

        // Sanitize value based on type before saving
        $attributeType = $fields->getProperty('attribute_type');
        $attributeValue = $fields->getProperty('attribute_value');
        
        if ($attributeType && $attributeValue) {
            $tempAttribute = new ServiceAttribute();
            $tempAttribute->attribute_type = $attributeType;
            $sanitizedValue = $tempAttribute->sanitizeValue($attributeValue);
            $fields->setProperty('attribute_value', $sanitizedValue);
        }

        $attribute->save($fields);

        return tr_redirect()->toPage('serviceattributes', 'index')
            ->withFlash('Service Attribute Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|ServiceAttribute $attribute
     *
     * @return mixed
     */
    public function edit(ServiceAttribute $attribute)
    {
        $form = tr_form($attribute)->useErrors()->useOld();
        $button = 'Update Service Attribute';

        return View::new('service_attributes.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAttribute $attribute
     *
     * @return mixed
     */
    public function update(ServiceAttribute $attribute, ServiceAttributeFields $fields, Response $response)
    {
        if (!$attribute->can('update')) {
            $response->unauthorized('Unauthorized: Service Attribute not updated')->abort();
        }

        // Sanitize value based on type before saving
        $attributeType = $fields->getProperty('attribute_type');
        $attributeValue = $fields->getProperty('attribute_value');
        
        if ($attributeType && $attributeValue !== null) {
            $tempAttribute = new ServiceAttribute();
            $tempAttribute->attribute_type = $attributeType;
            $sanitizedValue = $tempAttribute->sanitizeValue($attributeValue);
            $fields->setProperty('attribute_value', $sanitizedValue);
        }

        $attribute->save($fields);

        return tr_redirect()->toPage('serviceattributes', 'edit', $attribute->getID())
            ->withFlash('Service Attribute Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceAttribute $attribute
     *
     * @return mixed
     */
    public function show(ServiceAttribute $attribute)
    {
        return $attribute;
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceAttribute $attribute
     *
     * @return mixed
     */
    public function delete(ServiceAttribute $attribute)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAttribute $attribute
     *
     * @return mixed
     */
    public function destroy(ServiceAttribute $attribute, Response $response)
    {
        if (!$attribute->can('destroy')) {
            $response->unauthorized('Unauthorized: Service Attribute not deleted')->abort();
        }

        // Check if attribute can be safely deleted
        if (!$attribute->canBeDeleted()) {
            return $response->error('Cannot delete attribute: This attribute is required and cannot be deleted.');
        }

        $attribute->delete();

        return $response->warning('Service Attribute Deleted');
    }

    /**
     * AJAX endpoint to validate attribute value for given type
     *
     * @param Response $response
     * @return mixed
     */
    public function validateValue(Response $response)
    {
        $attributeType = $_POST['attribute_type'] ?? '';
        $attributeValue = $_POST['attribute_value'] ?? '';

        if (!$attributeType) {
            return $response->error('Attribute type is required');
        }

        $tempAttribute = new ServiceAttribute();
        $tempAttribute->attribute_type = $attributeType;
        $tempAttribute->attribute_value = $attributeValue;

        $isValid = $tempAttribute->isValidValue();
        $sanitizedValue = $tempAttribute->sanitizeValue($attributeValue);
        $formattedValue = $tempAttribute->getFormattedValue();

        return $response->json([
            'is_valid' => $isValid,
            'sanitized_value' => $sanitizedValue,
            'formatted_value' => $formattedValue,
            'message' => $isValid ? 'Valid value' : 'Invalid value for this attribute type'
        ]);
    }

    /**
     * AJAX endpoint to get attributes for a specific service
     *
     * @param Response $response
     * @return mixed
     */
    public function getServiceAttribute(Response $response)
    {
        $serviceId = (int)($_GET['service_id'] ?? 0);

        if (!$serviceId) {
            return $response->error('Service ID is required');
        }

        $attributes = ServiceAttribute::new()
            ->forService($serviceId)
            ->ordered()
            ->get();

        $attributeData = [];
        foreach ($attributes as $attribute) {
            $attributeData[] = [
                'id' => $attribute->id,
                'name' => $attribute->attribute_name,
                'value' => $attribute->attribute_value,
                'type' => $attribute->attribute_type,
                'type_text' => $attribute->getAttributeTypeText(),
                'formatted_value' => $attribute->getFormattedValue(),
                'is_configurable' => $attribute->is_configurable,
                'display_order' => $attribute->display_order
            ];
        }

        return $response->json([
            'service_id' => $serviceId,
            'attributes' => $attributeData,
            'count' => count($attributeData)
        ]);
    }

    /**
     * AJAX endpoint to reorder attributes
     *
     * @param Response $response
     * @return mixed
     */
    public function reorderAttributes(Response $response)
    {
        $attributeIds = $_POST['attribute_ids'] ?? [];

        if (!is_array($attributeIds) || empty($attributeIds)) {
            return $response->error('Attribute IDs are required');
        }

        try {
            foreach ($attributeIds as $order => $attributeId) {
                $attribute = ServiceAttribute::new()->findById((int)$attributeId);
                if ($attribute && $attribute->can('update')) {
                    $attribute->display_order = $order + 1;
                    $attribute->save();
                }
            }

            return $response->json([
                'success' => true,
                'message' => 'Attributes reordered successfully'
            ]);

        } catch (\Exception $e) {
            return $response->error('Failed to reorder attributes: ' . $e->getMessage());
        }
    }

    /**
     * Bulk action to update multiple attributes
     *
     * @param Response $response
     * @return mixed
     */
    public function bulkUpdate(Response $response)
    {
        $attributeIds = $_POST['attribute_ids'] ?? [];
        $action = $_POST['bulk_action'] ?? '';

        if (!is_array($attributeIds) || empty($attributeIds)) {
            return $response->error('No attributes selected');
        }

        if (!$action) {
            return $response->error('No action specified');
        }

        $updated = 0;
        $errors = [];

        foreach ($attributeIds as $attributeId) {
            try {
                $attribute = ServiceAttribute::new()->findById((int)$attributeId);
                
                if (!$attribute) {
                    $errors[] = "Attribute ID {$attributeId} not found";
                    continue;
                }

                if (!$attribute->can('update')) {
                    $errors[] = "No permission to update attribute ID {$attributeId}";
                    continue;
                }

                switch ($action) {
                    case 'make_configurable':
                        $attribute->is_configurable = true;
                        break;
                    case 'make_display_only':
                        $attribute->is_configurable = false;
                        break;
                    case 'delete':
                        if ($attribute->canBeDeleted()) {
                            $attribute->delete();
                            $updated++;
                            continue 2;
                        } else {
                            $errors[] = "Cannot delete attribute ID {$attributeId}";
                            continue 2;
                        }
                    default:
                        $errors[] = "Unknown action: {$action}";
                        continue 2;
                }

                $attribute->save();
                $updated++;

            } catch (\Exception $e) {
                $errors[] = "Error updating attribute ID {$attributeId}: " . $e->getMessage();
            }
        }

        if ($updated > 0) {
            $message = "Successfully updated {$updated} attribute(s)";
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
            return $response->error('No attributes were updated. Errors: ' . implode(', ', $errors));
        }
    }
}