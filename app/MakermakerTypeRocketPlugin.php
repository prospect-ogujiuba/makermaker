<?php

namespace MakerMaker;

use MakerMaker\Models\Service;
use MakerMaker\Models\Client;
use MakerMaker\Models\Status;


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
        tr_resource_pages('Service@\MakerMaker\Controllers\ServiceController', 'Services')
            ->setIcon('dashicons-products')
            ->setPosition(5);
        tr_resource_pages('Client@\MakerMaker\Controllers\ClientController', 'Clients')
            ->setIcon('dashicons-businessman')
            ->setPosition(0)
            ->mapAction('PUT', 'bulk');

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
        \TypeRocket\Register\Registry::addCustomResource('client', [
            'controller' => '\App\Controllers\ClientController',
        ]);
    }


    public function render_clean_template($template_file)
    {
        $template_path = get_stylesheet_directory() . '/templates/' . $template_file;

        if (!file_exists($template_path)) {
            $template_path = get_template_directory() . '/templates/' . $template_file;
            if (!file_exists($template_path)) {
                wp_die("Template not found: {$template_file}");
            }
        }

        // Keep essential WordPress head actions
        remove_all_actions('wp_head');
        add_action('wp_head', 'wp_enqueue_scripts', 1);
        add_action('wp_head', '_wp_render_title_tag', 1);
        add_action('wp_head', 'wp_print_styles', 8);
        add_action('wp_head', 'wp_print_head_scripts', 9);

        // Keep essential footer actions
        remove_all_actions('wp_footer');
        add_action('wp_footer', 'wp_print_footer_scripts', 20);

        $content = file_get_contents($template_path);
        $blocks = parse_blocks($content);

?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php wp_head(); ?>
        </head>

        <body <?php body_class(); ?>>
            <?php
            wp_body_open();
            foreach ($blocks as $block) {
                echo render_block($block);
            }
            wp_footer();
            ?>
        </body>

        </html>
<?php
    }

    public function routes()
    {


        // Route for specific client
        tr_route()->get()->match('api/v1/clients/([^\/]+)', ['id'])->do(function ($id) {
            $client = Client::new()->find($id);
            if (current_user_can('administrator')) {
                echo $client;
            }
        });

        // Route for all clients
        tr_route()->get()->match('api/v1/clients/')->do(function () {
            $clients = Client::new()->findAll()->get();
            if (current_user_can('administrator')) {
                echo $clients;
            }
        });

        // Route for client with related data (for future use when relationships are added)
        tr_route()->get()->match('api/v1/clients/([^\/]+)/detailed', ['id'])->do(function ($id) {
            $client = Client::new()
                // ->with(['serviceRequests', 'quotes', 'payments']) // Uncomment when relationships exist
                ->find($id);
            if (current_user_can('administrator')) {
                echo $client;
            }
        });

        // Route for clients by status
        tr_route()->get()->match('api/v1/clients/status/([^\/]+)', ['status'])->do(function ($status) {
            $clients = Client::new()
                ->where('status', '=', $status)
                ->findAll()
                ->get();
            if (current_user_can('administrator')) {
                echo $clients;
            }
        });

        // Route for clients by assigned user
        tr_route()->get()->match('api/v1/clients/assigned/([^\/]+)', ['user_id'])->do(function ($user_id) {
            $clients = Client::new()
                ->where('assigned_to', '=', $user_id)
                ->findAll()
                ->get();
            if (current_user_can('administrator')) {
                echo $clients;
            }
        });
    }

    public function policies()
    {
        // TODO: Add your TypeRocket policies here
        return [

            '\MakerMaker\Models\Client' => '\MakerMaker\Auth\ClientPolicy',
        ];
    }

    public function activate()
    {
        $this->migrateUp();
        System::updateSiteState('flush_rewrite_rules');
        tr_roles()->updateRolesCapabilities('administrator', ['manage_clients']);

        // TODO: Add your plugin activation code here
    }

    public function deactivate()
    {
        // Migrate `down` only on plugin uninstall
        System::updateSiteState('flush_rewrite_rules');
        // Uncomment the line below if you want to run migrations down on deactivation
        $this->migrateDown();

        tr_roles()->updateRolesCapabilities('administrator', ['manage_clients'], true);


        // TODO: Add your plugin deactivation code here
    }

    public function uninstall()
    {
        $this->migrateDown();

        // TODO: Add your plugin uninstall code here
    }
}
