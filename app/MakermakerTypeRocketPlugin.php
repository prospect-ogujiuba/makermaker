<?php

namespace MakerMaker;


use TypeRocket\Core\System;
use TypeRocket\Utility\Helper;
use TypeRocket\Pro\Register\BasePlugin;
use MakerMaker\View;

class MakermakerTypeRocketPlugin extends BasePlugin
{
    protected $title = 'Makermaker';
    protected $slug = 'makermaker';
    protected $migrationKey = 'makermaker_migrations';
    protected $migrations = true;

    public function init()
    {

        $resources = [
            'service',
        ];

        foreach ($resources as $resource) {

            include MAKERMAKER_PLUGIN_DIR . 'inc/resources/' . $resource . '.php';
        }



        // Plugin Settings
        $page = $this->pluginSettingsPage([
            'view' => View::new('settings', [
                'form' => Helper::form()->setGroup('makermaker_settings')->useRest()
            ])
        ]);

        $this->inlinePluginLinks(function () use ($page) {
            return [
                'settings' => "<a href=\"{$page->getUrl()}\" aria-label=\"Settings\">Settings</a>"
            ];
        });

        // Assets Manifest
        $manifest = $this->manifest('public');
        $uri = $this->uri('public');

        // Front Assets
        add_action('wp_enqueue_scripts', function () use ($manifest, $uri) {
            wp_enqueue_style('main-style-' . $this->slug, $uri . $manifest['/front/front.css']);
            wp_enqueue_script('main-script-' . $this->slug, $uri . $manifest['/front/front.js'], [], false, true);
        });

        // Admin Assets
        add_action('admin_enqueue_scripts', function () use ($manifest, $uri) {
            wp_enqueue_style('admin-style-' . $this->slug, $uri . $manifest['/admin/admin.css']);
            wp_enqueue_script('admin-script-' . $this->slug, $uri . $manifest['/admin/admin.js'], [], false, true);
        });

        // TODO: Add your init code here
    }


    public function routes()
    {
        include MAKERMAKER_PLUGIN_DIR . 'inc/routes/api.php';
        include MAKERMAKER_PLUGIN_DIR . 'inc/routes/public.php';
    }

    public function policies()
    {
        // TODO: Add your TypeRocket policies here
        return [
            '\MakerMaker\Models\ServiceComplexity' => '\MakerMaker\Auth\ServiceComplexityPolicy',
            '\MakerMaker\Models\ServicePricingModel' => '\MakerMaker\Auth\ServicePricingModelPolicy',
            '\MakerMaker\Models\ServicePricingTier' => '\MakerMaker\Auth\ServicePricingTierPolicy',
            '\MakerMaker\Models\ServiceDeliveryMethod' => '\MakerMaker\Auth\ServiceDeliveryMethodPolicy',
            '\MakerMaker\Models\ServiceCoverageArea' => '\MakerMaker\Auth\ServiceCoverageAreaPolicy',
            '\MakerMaker\Models\ServiceDeliverable' => '\MakerMaker\Auth\ServiceDeliverablePolicy',
            '\MakerMaker\Models\ServiceEquipment' => '\MakerMaker\Auth\ServiceEquipmentPolicy',
            '\MakerMaker\Models\ServiceType' => '\MakerMaker\Auth\ServiceTypePolicy',
            '\MakerMaker\Models\ServiceCategory' => '\MakerMaker\Auth\ServiceCategoryPolicy',
            '\MakerMaker\Models\ServiceAttributeDefinition' => '\MakerMaker\Auth\ServiceAttributeDefinitionPolicy',
            '\MakerMaker\Models\ServiceBundle' => '\MakerMaker\Auth\ServiceBundlePolicy',
            '\MakerMaker\Models\Service' => '\MakerMaker\Auth\ServicePolicy',
            '\MakerMaker\Models\ServicePrice' => '\MakerMaker\Auth\ServicePricePolicy',
            '\MakerMaker\Models\ServiceAddon' => '\MakerMaker\Auth\ServiceAddonPolicy',
            '\MakerMaker\Models\ServiceAttributeValue' => '\MakerMaker\Auth\ServiceAttributeValuePolicy',
            '\MakerMaker\Models\ServiceCoverage' => '\MakerMaker\Auth\ServiceCoveragePolicy',
            '\MakerMaker\Models\ServiceDeliverableAssignment' => '\MakerMaker\Auth\ServiceDeliverableAssignmentPolicy',
            '\MakerMaker\Models\ServiceDeliveryMethodAssignment' => '\MakerMaker\Auth\ServiceDeliveryMethodAssignmentPolicy',
            '\MakerMaker\Models\ServiceEquipmentAssignment' => '\MakerMaker\Auth\ServiceEquipmentAssignmentPolicy',
            '\MakerMaker\Models\ServiceRelationship' => '\MakerMaker\Auth\ServiceRelationshipPolicy',
            '\MakerMaker\Models\ServiceBundleItem' => '\MakerMaker\Auth\ServiceBundleItemPolicy',
        ];
    }

    public function activate()
    {
        $this->migrateUp();
        System::updateSiteState('flush_rewrite_rules');

        include MAKERMAKER_PLUGIN_DIR . 'inc/capabilities/capabilities.php';


        // TODO: Add your plugin activation code here
    }

    public function deactivate()
    {
        // Migrate `down` only on plugin uninstall
        System::updateSiteState('flush_rewrite_rules');

        // TODO: Add your plugin deactivation code here
        $this->migrateDown();
    }

    public function uninstall()
    {
        $this->migrateDown();

        // TODO: Add your plugin uninstall code here
    }
}
