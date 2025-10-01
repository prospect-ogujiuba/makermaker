## Project Files

[MakermakerTypeRocketPlugin]: /app/MakermakerTypeRocketPlugin.php
[Migration-Order]: /database/migration_order.md
[MVC-SOP]: /database/mvc_sop.md
[Sample-Data]: /database/migrations/1756668700.add_sample_data.sql
[Roles-Capabilities]: /inc/capabilities/capabilities.php
[Service-Resources]: /inc/resources/service.php
[Plugin-Bootstrap]: /makermaker.php

- [MakermakerTypeRocketPlugin][MakermakerTypeRocketPlugin]
- [Migration-Order][Migration-Order]
- [MVC-SOP][MVC-SOP]
- [Sample-Data][Sample-Data]
- [Roles-Capabilities][Roles-Capabilities]
- [Service-Resources][Service-Resources]
- [Plugin-Bootstrap][Plugin-Bootstrap]

---

## Complexity Level

[ComplexityLevel-Migration]: /database/migrations/1758851896.create_complexity_levels_table.sql
[ComplexityLevel-Model]: /app/Models/ComplexityLevel.php
[ComplexityLevel-Policy]: /app/Auth/ComplexityLevelPolicy.php
[ComplexityLevel-Fields]: /app/Http/Fields/ComplexityLevelFields.php
[ComplexityLevel-Controller]: /app/Controllers/ComplexityLevelController.php
[ComplexityLevel-Form]: /resources/views/service_complexities/form.php
[ComplexityLevel-Index]: /resources/views/service_complexities/index.php

- [Migration][ComplexityLevel-Migration] - [Model][ComplexityLevel-Model] - [Policy][ComplexityLevel-Policy] - [Fields][ComplexityLevel-Fields] - [Controller][ComplexityLevel-Controller] - [Form][ComplexityLevel-Form] - [Index][ComplexityLevel-Index]

### Todo

- [x] Fix unique id not being passed – [Fields][ComplexityLevel-Fields]
- [x] Fix relationship table in form – [Form][ComplexityLevel-Form]

---

## Pricing Model

[PricingModel-Migration]: /database/migrations/1758855155.create_pricing_models_table.sql
[PricingModel-Model]: /app/Models/PricingModel.php
[PricingModel-Policy]: /app/Auth/PricingModelPolicy.php
[PricingModel-Fields]: /app/Http/Fields/PricingModelFields.php
[PricingModel-Controller]: /app/Controllers/PricingModelController.php
[PricingModel-Form]: /resources/views/service_pricing_models/form.php
[PricingModel-Index]: /resources/views/service_pricing_models/index.php

- [Migration][PricingModel-Migration] - [Model][PricingModel-Model] - [Policy][PricingModel-Policy] - [Fields][PricingModel-Fields] - [Controller][PricingModel-Controller] - [Form][PricingModel-Form] - [Index][PricingModel-Index]

### Todo

- [ ] Task

---

## Pricing Tier

[PricingTier-Migration]: /database/migrations/1758858074.create_pricing_tiers_table.sql
[PricingTier-Model]: /app/Models/PricingTier.php
[PricingTier-Policy]: /app/Auth/PricingTierPolicy.php
[PricingTier-Fields]: /app/Http/Fields/PricingTierFields.php
[PricingTier-Controller]: /app/Controllers/PricingTierController.php
[PricingTier-Form]: /resources/views/service_pricing_tiers/form.php
[PricingTier-Index]: /resources/views/service_pricing_tiers/index.php

- [Migration][PricingTier-Migration] - [Model][PricingTier-Model] - [Policy][PricingTier-Policy] - [Fields][PricingTier-Fields] - [Controller][PricingTier-Controller] - [Form][PricingTier-Form] - [Index][PricingTier-Index]

### Todo

- [ ] Task

---

## Service Delivery Method

[DeliveryMethod-Migration]: /database/migrations/1756252687.create_service_delivery_methods_table.sql
[DeliveryMethod-Model]: /app/Models/DeliveryMethod.php
[DeliveryMethod-Policy]: /app/Auth/DeliveryMethodPolicy.php
[DeliveryMethod-Fields]: /app/Http/Fields/DeliveryMethodFields.php
[DeliveryMethod-Controller]: /app/Controllers/DeliveryMethodController.php
[DeliveryMethod-Form]: /resources/views/service_delivery_methods/form.php
[DeliveryMethod-Index]: /resources/views/service_delivery_methods/index.php

- [Migration][DeliveryMethod-Migration] - [Model][DeliveryMethod-Model] - [Policy][DeliveryMethod-Policy] - [Fields][DeliveryMethod-Fields] - [Controller][DeliveryMethod-Controller] - [Form][DeliveryMethod-Form] - [Index][DeliveryMethod-Index]

### Todo

- [ ] Task

---

## Coverage Area

[CoverageArea-Migration]: /database/migrations/1756253851.create_coverage_areas_table.sql
[CoverageArea-Model]: /app/Models/CoverageArea.php
[CoverageArea-Policy]: /app/Auth/CoverageAreaPolicy.php
[CoverageArea-Fields]: /app/Http/Fields/CoverageAreaFields.php
[CoverageArea-Controller]: /app/Controllers/CoverageAreaController.php
[CoverageArea-Form]: /resources/views/coverage_areas/form.php
[CoverageArea-Index]: /resources/views/coverage_areas/index.php

- [Migration][CoverageArea-Migration] - [Model][CoverageArea-Model] - [Policy][CoverageArea-Policy] - [Fields][CoverageArea-Fields] - [Controller][CoverageArea-Controller] - [Form][CoverageArea-Form] - [Index][CoverageArea-Index]

### Todo

- [ ] Task

---

## Service Deliverable

[Deliverable-Migration]: /database/migrations/1756308065.create_service_deliverables_table.sql
[Deliverable-Model]: /app/Models/Deliverable.php
[Deliverable-Policy]: /app/Auth/DeliverablePolicy.php
[Deliverable-Fields]: /app/Http/Fields/DeliverableFields.php
[Deliverable-Controller]: /app/Controllers/DeliverableController.php
[Deliverable-Form]: /resources/views/service_deliverables/form.php
[Deliverable-Index]: /resources/views/service_deliverables/index.php

- [Migration][Deliverable-Migration] - [Model][Deliverable-Model] - [Policy][Deliverable-Policy] - [Fields][Deliverable-Fields] - [Controller][Deliverable-Controller] - [Form][Deliverable-Form] - [Index][Deliverable-Index]

### Todo

- [ ] Task

---

## Service Equipment

[Equipment-Migration]: /database/migrations/1756309210.create_service_equipment_table.sql
[Equipment-Model]: /app/Models/Equipment.php
[Equipment-Policy]: /app/Auth/EquipmentPolicy.php
[Equipment-Fields]: /app/Http/Fields/EquipmentFields.php
[Equipment-Controller]: /app/Controllers/EquipmentController.php
[Equipment-Form]: /resources/views/service_equipment/form.php
[Equipment-Index]: /resources/views/service_equipment/index.php

- [Migration][Equipment-Migration] - [Model][Equipment-Model] - [Policy][Equipment-Policy] - [Fields][Equipment-Fields] - [Controller][Equipment-Controller] - [Form][Equipment-Form] - [Index][Equipment-Index]

### Todo

- [ ] Task

---

## Service Type

[ServiceType-Migration]: /database/migrations/1758889989.create_service_types_table.sql
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

## Service Deliverable

[ServiceDeliverable-Migration]: /database/migrations/1756660896.create_service_deliverables_table.sql
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

## Service Delivery

[ServiceDelivery-Migration]: /database/migrations/1756666041.create_service_delivery_methods_table.sql
[ServiceDelivery-Model]: /app/Models/ServiceDelivery.php
[ServiceDelivery-Policy]: /app/Auth/ServiceDeliveryPolicy.php
[ServiceDelivery-Fields]: /app/Http/Fields/ServiceDeliveryFields.php
[ServiceDelivery-Controller]: /app/Controllers/ServiceDeliveryController.php
[ServiceDelivery-Form]: /resources/views/service_delivery_methods/form.php
[ServiceDelivery-Index]: /resources/views/service_delivery_methods/index.php

- [Migration][ServiceDelivery-Migration] - [Model][ServiceDelivery-Model] - [Policy][ServiceDelivery-Policy] - [Fields][ServiceDelivery-Fields] - [Controller][ServiceDelivery-Controller] - [Form][ServiceDelivery-Form] - [Index][ServiceDelivery-Index]

### Todo

- [ ] Task

---

## Service Equipment Assignment

[ServiceEquipment-Migration]: /database/migrations/1756666767.create_service_equipments_table.sql
[ServiceEquipment-Model]: /app/Models/ServiceEquipment.php
[ServiceEquipment-Policy]: /app/Auth/ServiceEquipmentPolicy.php
[ServiceEquipment-Fields]: /app/Http/Fields/ServiceEquipmentFields.php
[ServiceEquipment-Controller]: /app/Controllers/ServiceEquipmentController.php
[ServiceEquipment-Form]: /resources/views/service_equipments/form.php
[ServiceEquipment-Index]: /resources/views/service_equipments/index.php

- [Migration][ServiceEquipment-Migration] - [Model][ServiceEquipment-Model] - [Policy][ServiceEquipment-Policy] - [Fields][ServiceEquipment-Fields] - [Controller][ServiceEquipment-Controller] - [Form][ServiceEquipment-Form] - [Index][ServiceEquipment-Index]

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

## Bundle Item

[BundleItem-Migration]: /database/migrations/1756668483.create_bundle_items_table.sql
[BundleItem-Model]: /app/Models/BundleItem.php
[BundleItem-Policy]: /app/Auth/BundleItemPolicy.php
[BundleItem-Fields]: /app/Http/Fields/BundleItemFields.php
[BundleItem-Controller]: /app/Controllers/BundleItemController.php
[BundleItem-Form]: /resources/views/bundle_items/form.php
[BundleItem-Index]: /resources/views/bundle_items/index.php

- [Migration][BundleItem-Migration] - [Model][BundleItem-Model] - [Policy][BundleItem-Policy] - [Fields][BundleItem-Fields] - [Controller][BundleItem-Controller] - [Form][BundleItem-Form] - [Index][BundleItem-Index]

### Todo

- [ ] Task

---
