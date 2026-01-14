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
        $this->loadResources();
        $this->setupSettingsPage();
        $this->registerAssets();
        $this->initReflectiveRestApi();
    }


    public function routes()
    {
        include MAKERMAKER_PLUGIN_DIR . 'inc/routes/api.php';
        include MAKERMAKER_PLUGIN_DIR . 'inc/routes/public.php';
    }

    public function policies()
    {
        return $this->discoverPolicies();
    }

    public function activate()
    {
        $this->migrateUp();
        System::updateSiteState('flush_rewrite_rules');
    }

    public function deactivate()
    {
        System::updateSiteState('flush_rewrite_rules');
    }

    public function uninstall()
    {
        $this->migrateDown();
    }

    private function loadResources()
    {
        $resources_dir = MAKERMAKER_PLUGIN_DIR . 'inc/resources';

        if (!is_dir($resources_dir)) {
            return;
        }

        // Get all PHP files
        $resource_files = glob($resources_dir . '/*.php');

        if (empty($resource_files)) {
            return;
        }

        // Include each resource file
        foreach ($resource_files as $file) {
            include_once $file;
        }
    }

    private function discoverPolicies()
    {
        $policies = [];
        $policies_dir = MAKERMAKER_PLUGIN_DIR . 'app/Auth';

        if (!is_dir($policies_dir)) {
            return $policies;
        }

        $policy_files = glob($policies_dir . '/*Policy.php');

        foreach ($policy_files as $file) {
            $filename = basename($file, '.php');

            // Extract model name (e.g., ProductPolicy -> Product)
            if (preg_match('/^(.+)Policy$/', $filename, $matches)) {
                $model_name = $matches[1];
                $model_class = '\\MakerMaker\\Models\\' . $model_name;
                $policy_class = '\\MakerMaker\\Auth\\' . $filename;

                // Only register if model exists
                if (class_exists($model_class) && class_exists($policy_class)) {
                    $policies[$model_class] = $policy_class;
                }
            }
        }

        return $policies;
    }

    private function setupSettingsPage()
    {
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
    }

    private function registerAssets()
    {
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
            // Bootstrap Icons CDN
            wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', [], '1.11.3');
            wp_enqueue_style('admin-style-' . $this->slug, $uri . $manifest['/admin/admin.css'], ['bootstrap-icons']);
            wp_enqueue_script('admin-script-' . $this->slug, $uri . $manifest['/admin/admin.js'], [], false, true);
        });
    }

    /**
     * Initialize Reflective REST API Wrapper
     *
     * This single call automatically enhances ALL custom resources with:
     * - Full-text search
     * - Field filtering
     * - Sorting and pagination
     * - Custom actions (for models implementing HasRestActions)
     *
     * Zero per-model configuration required.
     */
    private function initReflectiveRestApi()
    {
        // Configure model namespace for this plugin
        \MakermakerCore\Rest\ReflectiveRestWrapper::setModelNamespace('\\MakerMaker\\Models\\');

        // Configure list query modifier for user-based filtering
        \MakermakerCore\Rest\ReflectiveRestWrapper::setListQueryModifier(function ($model, $resource, $user) {
            // Apply user-based filtering for contact-submissions
            // Non-admin users can only see submissions they created
            if ($resource === 'contact-submissions' && !$user->isCapable('manage_contact_submissions')) {
                $model = $model->where('created_by', $user->ID);
            }
            return $model;
        });

        // Initialize the REST API wrapper
        \MakermakerCore\Rest\ReflectiveRestWrapper::init();
    }
}
