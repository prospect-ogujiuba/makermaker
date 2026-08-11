<?php
/**
 * Plugin Name:       MakerMaker
 * Plugin URI:        https://github.com/prospect-ogujiuba/makermaker
 * Description:       Generate structured custom plugins from the installed TypeRocket Pro v6 scaffold.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.2
 * Author:            Maker
 * License:           GPL-3.0-or-later
 * Text Domain:       makermaker
 */

defined( 'ABSPATH' ) || exit;

if ( defined( 'MAKERMAKER_BOOTSTRAPPED' ) ) {
    return;
}
define( 'MAKERMAKER_BOOTSTRAPPED', true );

$makermakerPath = __DIR__;
if ( ! is_dir( $makermakerPath . '/app' ) ) {
    $muRoot = defined( 'WPMU_PLUGIN_DIR' ) ? WPMU_PLUGIN_DIR : __DIR__;
    $makermakerPath = $muRoot . '/makermaker';
}
if ( ! is_dir( $makermakerPath . '/app' ) ) {
    return;
}
define( 'MAKERMAKER_PATH', $makermakerPath );

spl_autoload_register( static function ( string $class ): void {
    $prefix = 'Maker\\MakerMaker\\';
    if ( ! str_starts_with( $class, $prefix ) ) {
        return;
    }
    $relative = str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) );
    $file = MAKERMAKER_PATH . '/app/' . $relative . '.php';
    if ( is_file( $file ) ) {
        require_once $file;
    }
} );

function typerocket_plugin_makermaker(): void
{
    static $plugin = null;
    if ( $plugin !== null || ! class_exists( 'TypeRocket\\Pro\\Register\\BasePlugin' ) ) {
        return;
    }
    $plugin = \Maker\MakerMaker\MakermakerTypeRocketPlugin::new( __FILE__, MAKERMAKER_PATH );
}

add_action( 'typerocket_loaded', 'typerocket_plugin_makermaker', 9 );
register_activation_hook( __FILE__, 'typerocket_plugin_makermaker' );

add_action( 'admin_notices', static function (): void {
    if ( current_user_can( 'activate_plugins' ) && ! defined( 'TYPEROCKET_PATH' ) ) {
        echo '<div class="notice notice-error"><p>' . esc_html__( 'MakerMaker requires TypeRocket Pro v6 to be installed and loaded.', 'makermaker' ) . '</p></div>';
    }
} );
