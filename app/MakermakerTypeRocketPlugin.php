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

        tr_resource_pages('Status@\MakerMaker\Controllers\StatusController', 'Statuses')
            ->setIcon('dashicons-post-status')
            ->setPosition(5);

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
        // Status routes
        tr_route()->get()->match('ats/statuses/([^\/]+)', ['id'])->do(function ($id) {
            $status = \MakerMaker\Models\Status::new()->with(['applicants'])->find($id);
            if (current_user_can('administrator')) {
                echo $status;
            } else {
                render_clean_template('permission-error.html');
            }
        });

        tr_route()->get()->match('ats-statuses/')->do(function () {
            $statuses = \MakerMaker\Models\Status::new()->with(['applicants'])->findAll()->get();
            if (current_user_can('administrator')) {
                echo $statuses;
            } else {
                render_clean_template('permission-error.html');
            }
        });
    }

    public function policies()
    {
        // TODO: Add your TypeRocket policies here
        return [];
    }

    public function activate()
    {
        $this->migrateUp();
        System::updateSiteState('flush_rewrite_rules');

        // TODO: Add your plugin activation code here
    }

    public function deactivate()
    {
        // Migrate `down` only on plugin uninstall
        System::updateSiteState('flush_rewrite_rules');

        // TODO: Add your plugin deactivation code here
    }

    public function uninstall()
    {
        $this->migrateDown();

        // TODO: Add your plugin uninstall code here
    }
}
