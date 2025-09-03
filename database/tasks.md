## Project Files

- [MakermakerTypeRocketPlugin][MakermakerTypeRocketPlugin]
- [Migration-Order][Migration-Order]
- [MVC-SOP][MVC-SOP]
- [Sample-Data][Sample-Data]
- [Roles-Capabilities][Roles-Capabilities]
- [Service-Resources][Service-Resources]
- [Plugin-Bootstrap][Plugin-Bootstrap]

[MakermakerTypeRocketPlugin]: /app/MakermakerTypeRocketPlugin.php
[Migration-Order]: /database/migration_order.md
[MVC-SOP]: /database/mvc_sop.md
[Sample-Data]: /database/migrations/1756668700.add_sample_data.sql
[Roles-Capabilities]: /inc/capabilities/capabilities.php
[Service-Resources]: /inc/resources/service.php
[Plugin-Bootstrap]: /makermaker.php

---

## Service Complexity

[ServiceComplexity-Migration]: /database/migrations/1756191393.create_service_complexities_table.sql
[ServiceComplexity-Model]: /app/Models/ServiceComplexity.php
[ServiceComplexity-Policy]: /app/Auth/ServiceComplexityPolicy.php
[ServiceComplexity-Fields]: /app/Http/Fields/ServiceComplexityFields.php
[ServiceComplexity-Controller]: /app/Controllers/ServiceComplexityController.php
[ServiceComplexity-Form]: /resources/views/service_complexities/form.php
[ServiceComplexity-Index]: /resources/views/service_complexities/index.php

- [Migration][ServiceComplexity-Migration] - [Model][ServiceComplexity-Model] - [Policy][ServiceComplexity-Policy] - [Fields][ServiceComplexity-Fields] - [Controller][ServiceComplexity-Controller] - [Form][ServiceComplexity-Form] - [Index][ServiceComplexity-Index]

### Todo

- [ ] Fix unique id not being passed – [Fields][ServiceComplexity-Fields]
- [ ] Get CRUD API to work – [Controller][ServiceComplexity-Controller]
- [ ] Fix relationship table in form – [Form][ServiceComplexity-Form]
- [ ] Clean up filters – [Index][ServiceComplexity-Index]

---

## Service Pricing Model

[ServicePricingModel-Migration]: /database/migrations/1756230789.create_service_pricing_models_table.sql
[ServicePricingModel-Model]: /app/Models/ServicePricingModel.php
[ServicePricingModel-Policy]: /app/Auth/ServicePricingModelPolicy.php
[ServicePricingModel-Fields]: /app/Http/Fields/ServicePricingModelFields.php
[ServicePricingModel-Controller]: /app/Controllers/ServicePricingModelController.php
[ServicePricingModel-Form]: /resources/views/service_pricing_models/form.php
[ServicePricingModel-Index]: /resources/views/service_pricing_models/index.php

- [Migration][ServicePricingModel-Migration] - [Model][ServicePricingModel-Model] - [Policy][ServicePricingModel-Policy] - [Fields][ServicePricingModel-Fields] - [Controller][ServicePricingModel-Controller] - [Form][ServicePricingModel-Form] - [Index][ServicePricingModel-Index]

### Todo
- [ ] Task


---

## Service Pricing Tier

[ServicePricingTier-Migration]: /database/migrations/1756251629.create_service_pricing_tiers_table.sql
[ServicePricingTier-Model]: /app/Models/ServicePricingTier.php
[ServicePricingTier-Policy]: /app/Auth/ServicePricingTierPolicy.php
[ServicePricingTier-Fields]: /app/Http/Fields/ServicePricingTierFields.php
[ServicePricingTier-Controller]: /app/Controllers/ServicePricingTierController.php
[ServicePricingTier-Form]: /resources/views/service_pricing_tiers/form.php
[ServicePricingTier-Index]: /resources/views/service_pricing_tiers/index.php

- [Migration][ServicePricingTier-Migration] - [Model][ServicePricingTier-Model] - [Policy][ServicePricingTier-Policy] - [Fields][ServicePricingTier-Fields] - [Controller][ServicePricingTier-Controller] - [Form][ServicePricingTier-Form] - [Index][ServicePricingTier-Index]

### Todo
- [ ] Task

---

## Service Delivery Method

[ServiceDeliveryMethod-Migration]: /database/migrations/1756252687.create_service_delivery_methods_table.sql
[ServiceDeliveryMethod-Model]: /app/Models/ServiceDeliveryMethod.php
[ServiceDeliveryMethod-Policy]: /app/Auth/ServiceDeliveryMethodPolicy.php
[ServiceDeliveryMethod-Fields]: /app/Http/Fields/ServiceDeliveryMethodFields.php
[ServiceDeliveryMethod-Controller]: /app/Controllers/ServiceDeliveryMethodController.php
[ServiceDeliveryMethod-Form]: /resources/views/service_delivery_methods/form.php
[ServiceDeliveryMethod-Index]: /resources/views/service_delivery_methods/index.php

- [Migration][ServiceDeliveryMethod-Migration] - [Model][ServiceDeliveryMethod-Model] - [Policy][ServiceDeliveryMethod-Policy] - [Fields][ServiceDeliveryMethod-Fields] - [Controller][ServiceDeliveryMethod-Controller] - [Form][ServiceDeliveryMethod-Form] - [Index][ServiceDeliveryMethod-Index]

### Todo
- [ ] Task

---

## Service Coverage Area

[ServiceCoverageArea-Migration]: /database/migrations/1756253851.create_service_coverage_areas_table.sql
[ServiceCoverageArea-Model]: /app/Models/ServiceCoverageArea.php
[ServiceCoverageArea-Policy]: /app/Auth/ServiceCoverageAreaPolicy.php
[ServiceCoverageArea-Fields]: /app/Http/Fields/ServiceCoverageAreaFields.php
[ServiceCoverageArea-Controller]: /app/Controllers/ServiceCoverageAreaController.php
[ServiceCoverageArea-Form]: /resources/views/service_coverage_areas/form.php
[ServiceCoverageArea-Index]: /resources/views/service_coverage_areas/index.php

- [Migration][ServiceCoverageArea-Migration] - [Model][ServiceCoverageArea-Model] - [Policy][ServiceCoverageArea-Policy] - [Fields][ServiceCoverageArea-Fields] - [Controller][ServiceCoverageArea-Controller] - [Form][ServiceCoverageArea-Form] - [Index][ServiceCoverageArea-Index]

### Todo
- [ ] Task

---

## Service Deliverable

[ServiceDeliverable-Migration]: /database/migrations/1756308065.create_service_deliverables_table.sql
[ServiceDeliverable-Model]: /app/Models/ServiceDeliverable.php
[ServiceDeliverable-Policy]: /app/Auth/ServiceDeliverablePolicy.php
[ServiceDeliverable-Fields]: /app/Http/Fields/ServiceDeliverableFields.php
[ServiceDeliverable-Controller]: /app/Controllers/ServiceDeliverableController.php
[ServiceDeliverable-Form]: /resources/views/service_deliverables/form.php
[ServiceDeliverable-Index]: /resources/views/service_deliverables/index.php

- [Migration][ServiceDeliverable-Migration] - [Model][ServiceDeliverable-Model] - [Policy][ServiceDeliverable-Policy] - [Fields][ServiceDeliverable-Fields] - [Controller][ServiceDeliverable-Controller] - [Form][ServiceDeliverable-Form] - [Index][ServiceDeliverable-Index]

### Todo
- [ ] Task

---

## Service Equipment

[ServiceEquipment-Migration]: /database/migrations/1756309210.create_service_equipment_table.sql
[ServiceEquipment-Model]: /app/Models/ServiceEquipment.php
[ServiceEquipment-Policy]: /app/Auth/ServiceEquipmentPolicy.php
[ServiceEquipment-Fields]: /app/Http/Fields/ServiceEquipmentFields.php
[ServiceEquipment-Controller]: /app/Controllers/ServiceEquipmentController.php
[ServiceEquipment-Form]: /resources/views/service_equipment/form.php
[ServiceEquipment-Index]: /resources/views/service_equipment/index.php

- [Migration][ServiceEquipment-Migration] - [Model][ServiceEquipment-Model] - [Policy][ServiceEquipment-Policy] - [Fields][ServiceEquipment-Fields] - [Controller][ServiceEquipment-Controller] - [Form][ServiceEquipment-Form] - [Index][ServiceEquipment-Index]

### Todo
- [ ] Task

---

## Service Type

[ServiceType-Migration]: /database/migrations/1756309963.create_service_types_table.sql
[ServiceType-Model]: /app/Models/ServiceType.php
[ServiceType-Policy]: /app/Auth/ServiceTypePolicy.php
[ServiceType-Fields]: /app/Http/Fields/ServiceTypeFields.php
[ServiceType-Controller]: /app/Controllers/ServiceTypeController.php
[ServiceType-Form]: /resources/views/service_types/form.php
[ServiceType-Index]: /resources/views/service_types/index.php

- [Migration][ServiceType-Migration] - [Model][ServiceType-Model] - [Policy][ServiceType-Policy] - [Fields][ServiceType-Fields] - [Controller][ServiceType-Controller] - [Form][ServiceType-Form] - [Index][ServiceType-Index]

### Todo
- [ ] Task

---

## Service Category

[ServiceCategory-Migration]: /database/migrations/1756312227.create_service_categories_table.sql
[ServiceCategory-Model]: /app/Models/ServiceCategory.php
[ServiceCategory-Policy]: /app/Auth/ServiceCategoryPolicy.php
[ServiceCategory-Fields]: /app/Http/Fields/ServiceCategoryFields.php
[ServiceCategory-Controller]: /app/Controllers/ServiceCategoryController.php
[ServiceCategory-Form]: /resources/views/service_categories/form.php
[ServiceCategory-Index]: /resources/views/service_categories/index.php

- [Migration][ServiceCategory-Migration] - [Model][ServiceCategory-Model] - [Policy][ServiceCategory-Policy] - [Fields][ServiceCategory-Fields] - [Controller][ServiceCategory-Controller] - [Form][ServiceCategory-Form] - [Index][ServiceCategory-Index]

### Todo
- [ ] Task

---

## Attribute Definition

[AttributeDefinition-Migration]: /database/migrations/1756320652.create_attribute_definitions_table.sql
[AttributeDefinition-Model]: /app/Models/AttributeDefinition.php
[AttributeDefinition-Policy]: /app/Auth/AttributeDefinitionPolicy.php
[AttributeDefinition-Fields]: /app/Http/Fields/AttributeDefinitionFields.php
[AttributeDefinition-Controller]: /app/Controllers/AttributeDefinitionController.php
[AttributeDefinition-Form]: /resources/views/attribute_definitions/form.php
[AttributeDefinition-Index]: /resources/views/attribute_definitions/index.php

- [Migration][AttributeDefinition-Migration] - [Model][AttributeDefinition-Model] - [Policy][AttributeDefinition-Policy] - [Fields][AttributeDefinition-Fields] - [Controller][AttributeDefinition-Controller] - [Form][AttributeDefinition-Form] - [Index][AttributeDefinition-Index]

### Todo
- [ ] Task

---

## Service Bundle

[ServiceBundle-Migration]: /database/migrations/1756345584.create_service_bundles_table.sql
[ServiceBundle-Model]: /app/Models/ServiceBundle.php
[ServiceBundle-Policy]: /app/Auth/ServiceBundlePolicy.php
[ServiceBundle-Fields]: /app/Http/Fields/ServiceBundleFields.php
[ServiceBundle-Controller]: /app/Controllers/ServiceBundleController.php
[ServiceBundle-Form]: /resources/views/service_bundles/form.php
[ServiceBundle-Index]: /resources/views/service_bundles/index.php

- [Migration][ServiceBundle-Migration] - [Model][ServiceBundle-Model] - [Policy][ServiceBundle-Policy] - [Fields][ServiceBundle-Fields] - [Controller][ServiceBundle-Controller] - [Form][ServiceBundle-Form] - [Index][ServiceBundle-Index]

### Todo
- [ ] Task

---

## Service (Core)

[Service-Migration]: /database/migrations/1756346166.create_services_table.sql
[Service-Model]: /app/Models/Service.php
[Service-Policy]: /app/Auth/ServicePolicy.php
[Service-Fields]: /app/Http/Fields/ServiceFields.php
[Service-Controller]: /app/Controllers/ServiceController.php
[Service-Form]: /resources/views/services/form.php
[Service-Index]: /resources/views/services/index.php

- [Migration][Service-Migration] - [Model][Service-Model] - [Policy][Service-Policy] - [Fields][Service-Fields] - [Controller][Service-Controller] - [Form][Service-Form] - [Index][Service-Index]

### Todo
- [ ] Task

---

## Service Price

[ServicePrice-Migration]: /database/migrations/1756411355.create_service_prices_table.sql
[ServicePrice-Model]: /app/Models/ServicePrice.php
[ServicePrice-Policy]: /app/Auth/ServicePricePolicy.php
[ServicePrice-Fields]: /app/Http/Fields/ServicePriceFields.php
[ServicePrice-Controller]: /app/Controllers/ServicePriceController.php
[ServicePrice-Form]: /resources/views/service_prices/form.php
[ServicePrice-Index]: /resources/views/service_prices/index.php

- [Migration][ServicePrice-Migration] - [Model][ServicePrice-Model] - [Policy][ServicePrice-Policy] - [Fields][ServicePrice-Fields] - [Controller][ServicePrice-Controller] - [Form][ServicePrice-Form] - [Index][ServicePrice-Index]

### Todo
- [ ] Task

---

## Service Addon

[ServiceAddon-Migration]: /database/migrations/1756480611.create_service_addons_table.sql
[ServiceAddon-Model]: /app/Models/ServiceAddon.php
[ServiceAddon-Policy]: /app/Auth/ServiceAddonPolicy.php
[ServiceAddon-Fields]: /app/Http/Fields/ServiceAddonFields.php
[ServiceAddon-Controller]: /app/Controllers/ServiceAddonController.php
[ServiceAddon-Form]: /resources/views/service_addons/form.php
[ServiceAddon-Index]: /resources/views/service_addons/index.php

- [Migration][ServiceAddon-Migration] - [Model][ServiceAddon-Model] - [Policy][ServiceAddon-Policy] - [Fields][ServiceAddon-Fields] - [Controller][ServiceAddon-Controller] - [Form][ServiceAddon-Form] - [Index][ServiceAddon-Index]

### Todo
- [ ] Task

---

## Service Attribute Value

[ServiceAttributeValue-Migration]: /database/migrations/1756509317.create_service_attribute_values_table.sql
[ServiceAttributeValue-Model]: /app/Models/ServiceAttributeValue.php
[ServiceAttributeValue-Policy]: /app/Auth/ServiceAttributeValuePolicy.php
[ServiceAttributeValue-Fields]: /app/Http/Fields/ServiceAttributeValueFields.php
[ServiceAttributeValue-Controller]: /app/Controllers/ServiceAttributeValueController.php
[ServiceAttributeValue-Form]: /resources/views/service_attribute_values/form.php
[ServiceAttributeValue-Index]: /resources/views/service_attribute_values/index.php

- [Migration][ServiceAttributeValue-Migration] - [Model][ServiceAttributeValue-Model] - [Policy][ServiceAttributeValue-Policy] - [Fields][ServiceAttributeValue-Fields] - [Controller][ServiceAttributeValue-Controller] - [Form][ServiceAttributeValue-Form] - [Index][ServiceAttributeValue-Index]

### Todo
- [ ] Task

---

## Service Coverage

[ServiceCoverage-Migration]: /database/migrations/1756518066.create_service_coverage_table.sql
[ServiceCoverage-Model]: /app/Models/ServiceCoverage.php
[ServiceCoverage-Policy]: /app/Auth/ServiceCoveragePolicy.php
[ServiceCoverage-Fields]: /app/Http/Fields/ServiceCoverageFields.php
[ServiceCoverage-Controller]: /app/Controllers/ServiceCoverageController.php
[ServiceCoverage-Form]: /resources/views/service_coverage/form.php
[ServiceCoverage-Index]: /resources/views/service_coverage/index.php

- [Migration][ServiceCoverage-Migration] - [Model][ServiceCoverage-Model] - [Policy][ServiceCoverage-Policy] - [Fields][ServiceCoverage-Fields] - [Controller][ServiceCoverage-Controller] - [Form][ServiceCoverage-Form] - [Index][ServiceCoverage-Index]

### Todo
- [ ] Task

---

## Service Deliverable Assignment

[ServiceDeliverableAssignment-Migration]: /database/migrations/1756660896.create_service_deliverable_assignments_table.sql
[ServiceDeliverableAssignment-Model]: /app/Models/ServiceDeliverableAssignment.php
[ServiceDeliverableAssignment-Policy]: /app/Auth/ServiceDeliverableAssignmentPolicy.php
[ServiceDeliverableAssignment-Fields]: /app/Http/Fields/ServiceDeliverableAssignmentFields.php
[ServiceDeliverableAssignment-Controller]: /app/Controllers/ServiceDeliverableAssignmentController.php
[ServiceDeliverableAssignment-Form]: /resources/views/service_deliverable_assignments/form.php
[ServiceDeliverableAssignment-Index]: /resources/views/service_deliverable_assignments/index.php

- [Migration][ServiceDeliverableAssignment-Migration] - [Model][ServiceDeliverableAssignment-Model] - [Policy][ServiceDeliverableAssignment-Policy] - [Fields][ServiceDeliverableAssignment-Fields] - [Controller][ServiceDeliverableAssignment-Controller] - [Form][ServiceDeliverableAssignment-Form] - [Index][ServiceDeliverableAssignment-Index]

### Todo
- [ ] Task

---

## Service Delivery Method Assignment

[ServiceDeliveryMethodAssignment-Migration]: /database/migrations/1756666041.create_service_delivery_method_assignments_table.sql
[ServiceDeliveryMethodAssignment-Model]: /app/Models/ServiceDeliveryMethodAssignment.php
[ServiceDeliveryMethodAssignment-Policy]: /app/Auth/ServiceDeliveryMethodAssignmentPolicy.php
[ServiceDeliveryMethodAssignment-Fields]: /app/Http/Fields/ServiceDeliveryMethodAssignmentFields.php
[ServiceDeliveryMethodAssignment-Controller]: /app/Controllers/ServiceDeliveryMethodAssignmentController.php
[ServiceDeliveryMethodAssignment-Form]: /resources/views/service_delivery_method_assignments/form.php
[ServiceDeliveryMethodAssignment-Index]: /resources/views/service_delivery_method_assignments/index.php

- [Migration][ServiceDeliveryMethodAssignment-Migration] - [Model][ServiceDeliveryMethodAssignment-Model] - [Policy][ServiceDeliveryMethodAssignment-Policy] - [Fields][ServiceDeliveryMethodAssignment-Fields] - [Controller][ServiceDeliveryMethodAssignment-Controller] - [Form][ServiceDeliveryMethodAssignment-Form] - [Index][ServiceDeliveryMethodAssignment-Index]

### Todo
- [ ] Task

---

## Service Equipment Assignment

[ServiceEquipmentAssignment-Migration]: /database/migrations/1756666767.create_service_equipment_assignments_table.sql
[ServiceEquipmentAssignment-Model]: /app/Models/ServiceEquipmentAssignment.php
[ServiceEquipmentAssignment-Policy]: /app/Auth/ServiceEquipmentAssignmentPolicy.php
[ServiceEquipmentAssignment-Fields]: /app/Http/Fields/ServiceEquipmentAssignmentFields.php
[ServiceEquipmentAssignment-Controller]: /app/Controllers/ServiceEquipmentAssignmentController.php
[ServiceEquipmentAssignment-Form]: /resources/views/service_equipment_assignments/form.php
[ServiceEquipmentAssignment-Index]: /resources/views/service_equipment_assignments/index.php

- [Migration][ServiceEquipmentAssignment-Migration] - [Model][ServiceEquipmentAssignment-Model] - [Policy][ServiceEquipmentAssignment-Policy] - [Fields][ServiceEquipmentAssignment-Fields] - [Controller][ServiceEquipmentAssignment-Controller] - [Form][ServiceEquipmentAssignment-Form] - [Index][ServiceEquipmentAssignment-Index]

### Todo
- [ ] Task

---

## Service Relationship

[ServiceRelationship-Migration]: /database/migrations/1756667496.create_service_relationships_table.sql
[ServiceRelationship-Model]: /app/Models/ServiceRelationship.php
[ServiceRelationship-Policy]: /app/Auth/ServiceRelationshipPolicy.php
[ServiceRelationship-Fields]: /app/Http/Fields/ServiceRelationshipFields.php
[ServiceRelationship-Controller]: /app/Controllers/ServiceRelationshipController.php
[ServiceRelationship-Form]: /resources/views/service_relationships/form.php
[ServiceRelationship-Index]: /resources/views/service_relationships/index.php

- [Migration][ServiceRelationship-Migration] - [Model][ServiceRelationship-Model] - [Policy][ServiceRelationship-Policy] - [Fields][ServiceRelationship-Fields] - [Controller][ServiceRelationship-Controller] - [Form][ServiceRelationship-Form] - [Index][ServiceRelationship-Index]

### Todo
- [ ] Task

---

## Service Bundle Item

[ServiceBundleItem-Migration]: /database/migrations/1756668483.create_service_bundle_items_table.sql
[ServiceBundleItem-Model]: /app/Models/ServiceBundleItem.php
[ServiceBundleItem-Policy]: /app/Auth/ServiceBundleItemPolicy.php
[ServiceBundleItem-Fields]: /app/Http/Fields/ServiceBundleItemFields.php
[ServiceBundleItem-Controller]: /app/Controllers/ServiceBundleItemController.php
[ServiceBundleItem-Form]: /resources/views/service_bundle_items/form.php
[ServiceBundleItem-Index]: /resources/views/service_bundle_items/index.php

- [Migration][ServiceBundleItem-Migration] - [Model][ServiceBundleItem-Model] - [Policy][ServiceBundleItem-Policy] - [Fields][ServiceBundleItem-Fields] - [Controller][ServiceBundleItem-Controller] - [Form][ServiceBundleItem-Form] - [Index][ServiceBundleItem-Index]

### Todo
- [ ] Task

---
