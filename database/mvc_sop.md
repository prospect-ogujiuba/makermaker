# MVC Development Workflow w/ WordPress + TypeRocket Pro v6

The Standard Operating Procedure for Creating Custom Business Data Objects using TypeRocket and WordPress

## Infrastructure

This data object will typically be a menu in the admin and contains subpages for related entities

**Top Level Business Data**

1. **Resource Registration** - Register the top level resource, rest-api, pages and subpages.  
   i.e **_inc/resources/service.php_**

2. **Migration** - Define up and down migration to create and drop tables.  
   i.e **_database/migrations/1756346166.create_services_table.sql_**

3. **Model** - Database abstraction layer with relationships and validation.  
   i.e **_app/Models/Service.php_**

4. **Policy** - Authorization layer controlling user access to resources.  
   i.e **_app/Auth/Policies/ServicePolicy.php_**

5. **Field** - Custom field definitions for specialized form inputs.  
   i.e **_app/Http/Fields/ServiceFields.php_**

6. **Controller** - Handle HTTP requests and coordinate business logic.  
   i.e **_app/Controllers/ServiceController.php_**

7. **Form** - Define form structure and field layouts for data entry.  
   i.e **_resources/views/services/form.php_**

8. **Index** - Admin listing/table view with filtering and pagination.  
   i.e **_resources/views/services/index.php_**

9. **Integration** - Wire resources, routes, policies, assets, and capabilities into the plugin bootstrap.  
   i.e **_app/MakermakerTypeRocketPlugin.php_**, **_inc/capabilities/capabilities.php_**


## Development Checklist

### Resource Registration**
Create and register the main resource file

  - [ ] Create `inc/resources/service.php`
  - [ ] Register the top-level Service resource
    - `createServiceResource('Service', 'ServiceController', 'Services')->setIcon('cart')->setPosition(2);`
  - [ ] Define and register all Service subpages as child resources
    - `ServiceComplexity`, `ServicePricingModel`, `ServicePricingTier`,  
      `ServiceDeliveryMethod`, `ServiceCoverageArea`, `ServiceDeliverable`,  
      `ServiceEquipment`, `ServiceType`, `ServiceCategory`,  
      `ServiceAttributeDefinition`, `ServiceBundle`, `ServicePrice`,  
      `ServiceAddon`, `ServiceAttributeValue`, `ServiceCoverage`,  
      `ServiceDeliverableAssignment`, `ServiceDeliveryMethodAssignment`,  
      `ServiceEquipmentAssignment`, `ServiceRelationship`,  
      `ServiceBundleItem`
  - [ ] Register REST API endpoints where required (example: `service-complexity`)
    ```php
    \TypeRocket\Register\Registry::addCustomResource('service-complexity', [
        'controller' => '\MakerMaker\Controllers\ServiceComplexityController',
    ]);
    ```
  - [ ] Add all subpages to the main Service resource
    ```php
    foreach ($serviceSubpages as $subpage) {
        $service->addPage($subpage);
    }
    ```
### Migration ### 
Write up/down migration and run `php galaxy migrate up`

  - [ ] Create migration file:  
        `database/migrations/{timestamp}.create_services_table.sql`
  - [ ] Define **Up** migration with full table schema:
    - `id` as primary key (`bigint auto_increment`)
    - Unique identifiers (`sku`, `slug`)
    - Core fields (`name`, `short_desc`, `long_desc`)
    - Relationships (`category_id`, `service_type_id`, `complexity_id`)
    - Status flags (`is_active`, `is_addon`)
    - Config fields (`default_unit`, `metadata` as JSON)
    - Versioning (`version` for optimistic locking)
    - Audit fields (`created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`)
  - [ ] Add indexes for performance:
    - `uq_service__slug`, `uq_service__sku`
    - `idx_service__category_id`, `idx_service__service_type_id`, `idx_service__complexity_id`
    - `idx_service__is_active`, `idx_service__is_addon`
    - `idx_service__deleted_at`, `idx_service__sku`, `idx_service__slug`
  - [ ] Add foreign key constraints:
    - `fk_service__category` → `srvc_categories.id`
    - `fk_service__complexity` → `srvc_complexities.id`
    - `fk_service__service_type` → `srvc_service_types.id`
  - [ ] Define **Down** migration to drop table:
    ```sql
    DROP TABLE IF EXISTS `{!!prefix!!}srvc_services`;
    ```
  - [ ] Run migration:
    ```bash
    php galaxy migrate up
    ```
  - [ ] Verify schema was applied correctly and constraints are active

### Model
ORM Layer Class for database interaction, relationships and connecting application to tables

  - [ ] Create `app/Models/Service.php`
  - [ ] Extend `TypeRocket\Models\Model`
  - [ ] Set `$resource` to match database table:  
    ```php
    protected $resource = 'srvc_services';
    ```
  - [ ] Define `$fillable` fields for mass assignment:
    - `sku`, `slug`, `name`, `short_desc`, `long_desc`
    - `category_id`, `service_type_id`, `complexity_id`
    - `is_active`, `is_addon`, `default_unit`, `metadata`
  - [ ] Define `$guard` fields to protect system-managed columns:
    - `id`, `version`
    - `created_at`, `updated_at`, `deleted_at`
    - `created_by`, `updated_by`
  - [ ] Implement relationships:
    - `complexity()` → `belongsTo(ServiceComplexity::class, 'complexity_id')`
  - [ ] Add convenience accessors for business logic:
    - `getPriceMultiplier()` → returns complexity’s `price_multiplier` or defaults to `1.0`
    - `getComplexityLevel()` → returns complexity’s `level` or defaults to `1`
    - `getComplexityName()` → returns complexity’s `name` or defaults to `"Basic"`
  - [ ] Add validation rules (if needed) for required fields, uniqueness, etc.
  - [ ] Confirm relationships align with foreign keys defined in migration (`complexity_id`, `category_id`, `service_type_id`)

### Policy ###
Implement authorization rules (if needed)

  - [ ] Create `app/Auth/Policies/ServicePolicy.php`
  - [ ] Namespace `MakerMaker\Auth`; extend `TypeRocket\Auth\Policy`
  - [ ] Import `TypeRocket\Models\AuthUser` (and any User model if needed)
  - [ ] Define deny-all defaults (tightest security)  
    ```php
    class ServicePolicy extends Policy {
        public function create(AuthUser $auth, $object) { return false; }
        public function read(AuthUser $auth, $object)   { return false; }
        public function update(AuthUser $auth, $object) { return false; }
        public function destroy(AuthUser $auth, $object){ return false; }
    }
    ```
  - [ ] Associate the policy with the Service domain (model/resource) per your convention
    - (e.g., ensure controllers call `$model->can('create|read|update|destroy')` before actions)
  - [ ] Plan rule expansions (later): role/cap checks, ownership, and granular field guards

### Field ###
Build custom validation for fields (if needed)

- [ ] Create dedicated class in `app/Http/Fields/`  
    Example: `app/Http/Fields/ServiceFields.php`
  - [ ] Extend `TypeRocket\Http\Fields`
  - [ ] Enable auto-validation on import:  
    ```php
    protected $run = true;
    ```
  - [ ] Optionally override `fillable()` to control which attributes are mass assignable
  - [ ] Define `rules()` method for validation logic
    - Use `unique:field:table@id:id_value` pattern for uniqueness checks
    - Add type and requirement rules (`numeric|required|string|min|max`)
    - Example rules:
      ```php
      $rules['slug'] = "unique:slug:{$table_prefix}srvc_services@id:{$id}|required";
      $rules['name'] = "required|string|max:255";
      $rules['sku']  = "unique:sku:{$table_prefix}srvc_services@id:{$id}";
      ```
  - [ ] Provide `messages()` method for custom validation error messages
  - [ ] Confirm rules align with migration constraints (unique indexes, not nulls, numeric types)
  - [ ] Ensure controller imports and applies this Field class in CRUD operations

### Controller ###
Handle CRUD operations and business logic

- [ ] **Controller** - Handle CRUD operations and business logic
  - [ ] Create controller in `app/Controllers/`  
    Example: `app/Controllers/ServiceController.php`
  - [ ] Imports
    - Model (e.g., `MakerMaker\Models\Service`)
    - Fields class (e.g., `MakerMaker\Http\Fields\ServiceFields`)
    - `TypeRocket\Controllers\Controller`, `TypeRocket\Http\Response`
    - View facade (e.g., `MakerMaker\View`)
    - Auth user (`TypeRocket\Models\AuthUser`)
  - [ ] Admin pages
    - `index()` → returns index view (e.g., `services.index`)
    - `add(AuthUser $user)` → returns form view with current user (e.g., `services.form`)
    - `edit(Service $service, AuthUser $user)` → returns form view with model + user
    - `delete(Service $service)` → returns delete-confirm view
  - [ ] Create
    - Method: `create(ServiceFields $fields, Service $service, Response $response, AuthUser $user)`
    - Authorization: `if (!$service->can('create')) $response->unauthorized(...)->abort();`
    - System columns: set `created_by`, `updated_by` from `$user->ID`
    - Persist: `$service->save($fields);`
    - Redirect with flash to resource index
  - [ ] Update
    - Method: `update(Service $service, ServiceFields $fields, Response $response, AuthUser $user)`
    - Authorization: `can('update')`
    - System columns: set `updated_by` from `$user->ID`
    - Persist: `$service->update($fields);`
    - Redirect with flash back to edit page
  - [ ] Read (show)
    - Method: `show(Service $service)`
    - Return model with eager-loaded relations (e.g., `->with(['createdBy','updatedBy', /* domain rels */])->get()` or `find(...)`)
  - [ ] Destroy (safe delete)
    - Method: `destroy(Service $service, Response $response)`
    - Authorization: `can('destroy')`
    - Precondition check: block delete when related rows exist (count dependent relations and 409 on conflict)
    - Execute delete; handle DB failure (500) vs success (200) with message
  - [ ] REST endpoints (optional but recommended)
    - `indexRest(Response $response)` → list with eager loads; return 200 with data, 200/empty set message, 500 on exception
    - `showRest(Service $service, Response $response)` → single with eager loads; 200 on success, 404 when not found, 500 on exception
    - Wrap both in `try/catch`, log exceptions, and return structured messages
  - [ ] Consistency & UX
    - Use consistent flash messages: “Created”, “Updated”, “Deleted”
    - Use `tr_redirect()->toPage('{resource}', '{action}', $id)` helpers
    - Keep view folder names plural & snake_case (e.g., `resources/views/services/...`)
  - [ ] Security & correctness
    - Always gate actions via policy: `can('create|read|update|destroy')`
    - Never trust request input directly; rely on validated `ServiceFields`
    - Set/guard system fields (`created_by`, `updated_by`) server-side
    - Prefer eager loading for API responses to avoid N+1


### Form ###
Design data entry interface and validation

  - [ ] Create view file (plural, snake_case):  
    `resources/views/services/form.php`
  - [ ] Instantiate form bound to model instance; enable errors + old input
    ```php
    $form = tr_form($service ?? \MakerMaker\Models\Service::new())->useErrors()->useOld();
    ```
  - [ ] Use tabbed layout with a save footer
    ```php
    $tabs = \TypeRocket\Elements\Tabs::new()->setFooter($form->save())->layoutLeft();
    ```
  - [ ] **Overview tab**: primary business fields (match model/migration)
    - `Name` (required, maxlength)
    - `Slug` (required, unique, placeholder)
    - `SKU` (unique, optional)
    - `Short Description` (optional)
    - `Long Description` (textarea)
    - `Category` (select from `srvc_categories`)
    - `Service Type` (select from `srvc_service_types`)
    - `Complexity` (select from `srvc_complexities`)
    - `Default Unit` (select/text: hour, user, site, device)
    - `Is Active` (toggle/checkbox; default true)
    - `Is Addon` (toggle/checkbox; default false)
    - `Metadata` (JSON editor / textarea with JSON hint)
  - [ ] **System tab** (read-only system metadata; only when editing existing)
    - `ID`, `Created At`, `Updated At`, `Deleted At`
    - `Created By`, `Updated By`
    - All fields set `readonly`
  - [ ] **Relationships tab** (optional nested tabs/tables)
    - Example subtabs using tables or read-only lists:
      - `Prices`, `Deliverables`, `Coverage`, `Bundles`, `Related Services`
    - Prefer `tr_table($service)` or list components for quick context
  - [ ] Render order
    ```php
    echo $form->open();
    // tabs->tab('Overview' ...); tabs->tab('System' ... if $service); tabs->tab('Relationships' ...);
    $tabs->render();
    echo $form->close();
    ```
  - [ ] Wire validation to Field class (`ServiceFields`)
    - Ensure controller uses `ServiceFields` so errors surface via `->useErrors()->useOld()`
  - [ ] UX polish
    - Mark required labels (`->markLabelRequired()`)
    - Set sensible `min|max|step|maxlength|placeholder`
    - Group fields using `fieldset()` and `row()->withColumn(...)`
  - [ ] Data integrity
    - Keep system columns read-only in the form (set by controller/server)
    - Ensure select options come from canonical sources (Models/Repos) and match FKs


### Index ###
Build admin listing with search/filter functionality

  - [ ] Create view file (plural, snake_case):  
    `resources/views/services/index.php`
  - [ ] Instantiate a TypeRocket table bound to the model
    ```php
    $table = tr_table(\MakerMaker\Models\Service::class);
    ```
  - [ ] Configure bulk actions (optional)
    ```php
    $table->setBulkActions(tr_form()->useConfirm(), []);
    ```
  - [ ] Add **search form filters** (UI) aligned to domain fields
    - Render any global/advanced actions (e.g., `renderAdvancedSearchActions('service')`)
    - Provide inputs that map 1:1 to GET params your model filter will read:
      - Text inputs: `name`, `sku`, `slug`
      - Selects for FKs: `category_id`, `service_type_id`, `complexity_id`
      - Flags: `is_active`, `is_addon`
      - Ranges: `created_date_from/to`, `updated_date_from/to`
      - (Optional) Related entity filters via preloaded selects (e.g., Users)
    ```php
    $table->addSearchFormFilter(function () {
        // Example data sources
        $categories = tr_query()->table('wp_srvc_categories')->select('id','name')->orderBy('name')->get();
        $types      = tr_query()->table('wp_srvc_service_types')->select('id','name')->orderBy('name')->get();
        $complex    = tr_query()->table('wp_srvc_complexities')->select('id','name','level')->orderBy('level')->get();
        $users      = tr_query()->table('wp_users')->select('id','user_nicename','user_email')->orderBy('user_nicename')->get();

        // Render inputs that write to $_GET[...] (names below must match the model filter)
        ?>
        <div class="tr-search-filters">
          <div class="tr-filter-group">
            <label>Name</label>
            <input type="text" name="name" class="tr-filter" value="<?= esc_attr($_GET['name'] ?? '') ?>" placeholder="Search Name">
          </div>

          <div class="tr-filter-group">
            <label>SKU</label>
            <input type="text" name="sku" class="tr-filter" value="<?= esc_attr($_GET['sku'] ?? '') ?>" placeholder="Search SKU">
          </div>

          <div class="tr-filter-group">
            <label>Category</label>
            <select name="category_id" class="tr-filter">
              <option value="">All</option>
              <?php foreach($categories as $c): ?>
                <option value="<?= (int)$c->id ?>" <?= (($_GET['category_id'] ?? '') == $c->id) ? 'selected' : '' ?>>
                  <?= esc_html($c->name) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="tr-filter-group">
            <label>Service Type</label>
            <select name="service_type_id" class="tr-filter">
              <option value="">All</option>
              <?php foreach($types as $t): ?>
                <option value="<?= (int)$t->id ?>" <?= (($_GET['service_type_id'] ?? '') == $t->id) ? 'selected' : '' ?>>
                  <?= esc_html($t->name) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="tr-filter-group">
            <label>Complexity</label>
            <select name="complexity_id" class="tr-filter">
              <option value="">All</option>
              <?php foreach($complex as $cx): ?>
                <option value="<?= (int)$cx->id ?>" <?= (($_GET['complexity_id'] ?? '') == $cx->id) ? 'selected' : '' ?>>
                  <?= esc_html($cx->level . ' - ' . $cx->name) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="tr-filter-group">
            <label>Created Date</label>
            <div class="tr-date-inputs">
              <input type="date" name="created_date_from" class="tr-filter" value="<?= esc_attr($_GET['created_date_from'] ?? '') ?>">
              <input type="date" name="created_date_to" class="tr-filter" value="<?= esc_attr($_GET['created_date_to'] ?? '') ?>">
            </div>
          </div>

          <div class="tr-filter-group">
            <label>Updated Date</label>
            <div class="tr-date-inputs">
              <input type="date" name="updated_date_from" class="tr-filter" value="<?= esc_attr($_GET['updated_date_from'] ?? '') ?>">
              <input type="date" name="updated_date_to" class="tr-filter" value="<?= esc_attr($_GET['updated_date_to'] ?? '') ?>">
            </div>
          </div>

          <div class="tr-filter-group">
            <label>Created By</label>
            <select name="created_by" class="tr-filter">
              <option value="">Any</option>
              <?php foreach($users as $u): ?>
                <option value="<?= (int)$u->id ?>" <?= (($_GET['created_by'] ?? '') == $u->id) ? 'selected' : '' ?>>
                  <?= esc_html($u->user_nicename) ?> (<?= esc_html($u->user_email) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="tr-filter-group">
            <label>Updated By</label>
            <select name="updated_by" class="tr-filter">
              <option value="">Any</option>
              <?php foreach($users as $u): ?>
                <option value="<?= (int)$u->id ?>" <?= (($_GET['updated_by'] ?? '') == $u->id) ? 'selected' : '' ?>>
                  <?= esc_html($u->user_nicename) ?> (<?= esc_html($u->user_email) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <?php
    });
    ```
  - [ ] Add **model filters** (server-side) that mirror the form inputs
    ```php
    $table->addSearchModelFilter(function ($args, $model, $table) {
        if (!empty($_GET['name']))  { $model->where('name', 'LIKE', '%' . $_GET['name'] . '%'); }
        if (!empty($_GET['sku']))   { $model->where('sku', 'LIKE', '%' . $_GET['sku'] . '%'); }
        if (!empty($_GET['category_id']))     { $model->where('category_id', '=', (int)$_GET['category_id']); }
        if (!empty($_GET['service_type_id'])) { $model->where('service_type_id', '=', (int)$_GET['service_type_id']); }
        if (!empty($_GET['complexity_id']))   { $model->where('complexity_id', '=', (int)$_GET['complexity_id']); }

        if (!empty($_GET['created_date_from'])) { $model->where('created_at', '>=', $_GET['created_date_from'] . ' 00:00:00'); }
        if (!empty($_GET['created_date_to']))   { $model->where('created_at', '<=', $_GET['created_date_to'] . ' 23:59:59'); }
        if (!empty($_GET['updated_date_from'])) { $model->where('updated_at', '>=', $_GET['updated_date_from'] . ' 00:00:00'); }
        if (!empty($_GET['updated_date_to']))   { $model->where('updated_at', '<=', $_GET['updated_date_to'] . ' 23:59:59'); }

        if (!empty($_GET['created_by'])) { $model->where('created_by', '=', (int)$_GET['created_by']); }
        if (!empty($_GET['updated_by'])) { $model->where('updated_by', '=', (int)$_GET['updated_by']); }

        // Example: join to a related table for “has” filters, if needed
        // $model->join('wp_srvc_prices', 'wp_srvc_prices.service_id', '=', 'wp_srvc_services.id')
        //       ->where('wp_srvc_prices.is_active', '=', 1);

        return $args;
    });
    ```
  - [ ] Define table columns & sorting defaults
    ```php
    $table->setColumns([
        'name' => ['label' => 'Name', 'sort' => 'true', 'actions' => ['edit','view','delete']],
        'sku'  => ['label' => 'SKU', 'sort' => 'true'],
        'category.name' => ['label' => 'Category'],           // if relation accessor exists
        'serviceType.name' => ['label' => 'Type'],            // if relation accessor exists
        'complexity.level' => ['label' => 'Level'],           // if relation accessor exists
        'is_active' => ['label' => 'Active', 'sort' => 'true'],
        'updated_at' => ['label' => 'Updated At', 'sort' => 'true'],
        'createdBy.user_nicename' => ['label' => 'Created By'],
        'updatedBy.user_nicename' => ['label' => 'Last Updated By'],
        'id' => ['label' => 'ID', 'sort' => 'true'],
    ], 'name')->setOrder('id', 'DESC')->render();
    ```
  - [ ] Performance & UX
    - Keep filter names identical between form and model filter
    - Prefer equality for exact FKs; LIKE for text fields
    - For related filters, join only when the GET param is present
    - Consider limiting options for large selects (AJAX, typeahead) if needed
  - [ ] Security & correctness
    - Escape all echoed values (`esc_html`, `esc_attr`) in form inputs
    - Avoid exposing raw email/user data unless necessary
    - Ensure policy checks are enforced on row-level actions in controllers

### Plugin Bootstrap & Integration ###
Wire resources, routes, policies, assets, and capabilities

  - [ ] Create plugin bootstrap class extending `TypeRocket\Pro\Register\BasePlugin`
    - File: `app/MakermakerTypeRocketPlugin.php` (or `inc/plugin.php`)
    - Set core props: `$title`, `$slug`, `$migrationKey`, `$migrations = true`
  - [ ] Register top-level resources on init
    - Maintain list in `$resources = ['service', ...];`
    - Include each resource file: `inc/resources/{resource}.php`
  - [ ] Add a plugin **Settings** page
    - Use `pluginSettingsPage()` with `View::new('settings', ['form' => Helper::form()->setGroup('makermaker_settings')->useRest()])`
    - Expose quick link via `inlinePluginLinks()` → “Settings”
  - [ ] Configure **Assets** via manifest
    - Build manifest with `$this->manifest('public')` and `$this->uri('public')`
    - Enqueue **front** assets on `wp_enqueue_scripts`
    - Enqueue **admin** assets on `admin_enqueue_scripts`
  - [ ] Register **Routes**
    - Public: `inc/routes/public.php`
    - API: `inc/routes/api.php`
    - Include in `routes()` method
  - [ ] Register **Policies**
    - Map models to policies in `policies()`:
      - `\MakerMaker\Models\ServiceComplexity` → `\MakerMaker\Auth\ServiceComplexityPolicy`
      - `\MakerMaker\Models\ServicePricingModel` → `\MakerMaker\Auth\ServicePricingModelPolicy`
    - Ensure controllers call `$model->can('create|read|update|destroy')`
  - [ ] Define **Capabilities** & assign to roles
    - Cap list (example): `manage_services`, `edit_services`, `delete_services`, `view_services`, `publish_services`
    - File: `inc/capabilities/capabilities.php`
    - Apply with `tr_roles()->updateRolesCapabilities('administrator', $wp_caps)`
  - [ ] Activation/Deactivation/Uninstall lifecycle
    - **activate()**
      - Run `$this->migrateUp()`
      - `System::updateSiteState('flush_rewrite_rules')`
      - `include inc/capabilities/capabilities.php`
    - **deactivate()**
      - `System::updateSiteState('flush_rewrite_rules')`
    - **uninstall()**
      - Run `$this->migrateDown()`
  - [ ] Sanity checks
    - Verify all resource includes resolve and pages appear in admin
    - Confirm policies deny/allow as expected
    - Ensure manifests exist and assets load (front & admin)
    - Confirm routes return expected responses
    - Verify caps are assigned and respected by WordPress roles


