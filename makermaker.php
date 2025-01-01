<?php
/*
Plugin Name: Makermaker
Version: 1.0
Description: Boilerplate TypeRocket Plugin.
Author: TypeRocket Galaxy CLI
License: GPLv2 or later
*/

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

if(!defined('TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH')) {
    define('TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH', __DIR__ . '/resources/views');
}

$__typerocket_plugin_makermaker = null;

function typerocket_plugin_makermaker() {
    global $__typerocket_plugin_makermaker;

    if($__typerocket_plugin_makermaker) {
        return;
    }

    if(file_exists(__DIR__ . '/vendor/autoload.php')) {
        require __DIR__ . '/vendor/autoload.php';
    } else {
        $map = [
            'prefix' => 'MakerMaker',
            'folder' => __DIR__ . '/app',
        ];

        typerocket_autoload_psr4($map);
    }

    $__typerocket_plugin_makermaker = call_user_func('MakerMaker\MakermakerTypeRocketPlugin::new', __FILE__, __DIR__);
}

register_activation_hook( __FILE__, 'typerocket_plugin_makermaker');
add_action('delete_plugin', 'typerocket_plugin_makermaker');
add_action('typerocket_loaded', 'typerocket_plugin_makermaker', 9);