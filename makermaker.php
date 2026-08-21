<?php
/**
 * Plugin Name:       MakerMaker
 * Plugin URI:        https://github.com/prospect-ogujiuba/makermaker
 * Description:       A professional WordPress plugin generator for project-owned TypeRocket Pro applications and resources.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.2
 * Author:            Maker
 * Author URI:        https://github.com/prospect-ogujiuba
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
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

function makermaker_register_galaxy_command( array $commands ): array
{
    $command = \Maker\MakerMaker\Cli\MakerResourceGalaxyCommand::class;
    if ( ! in_array( $command, $commands, true ) ) {
        $commands[] = $command;
    }

    return $commands;
}
add_filter( 'typerocket_galaxy_commands', 'makermaker_register_galaxy_command' );

function makermaker_dependency_error(): ?string
{
    if ( ! defined( 'TYPEROCKET_PATH' ) ) {
        return __( 'MakerMaker requires TypeRocket Pro v6 to be installed and loaded. Install and activate TypeRocket Pro before using MakerMaker.', 'makermaker' );
    }
    if ( ! class_exists( 'TypeRocket\\Pro\\Register\\BasePlugin' ) ) {
        return __( 'MakerMaker could not find the TypeRocket Pro v6 plugin API. Verify the installed TypeRocket Pro version and reload WordPress.', 'makermaker' );
    }
    return null;
}

function typerocket_plugin_makermaker(): void
{
    static $plugin = null;
    if ( $plugin !== null || makermaker_dependency_error() !== null ) {
        return;
    }
    $plugin = \Maker\MakerMaker\MakermakerTypeRocketPlugin::new( __FILE__, MAKERMAKER_PATH );
}

add_action( 'typerocket_loaded', 'typerocket_plugin_makermaker', 9 );
register_activation_hook( __FILE__, 'typerocket_plugin_makermaker' );

add_action( 'admin_notices', static function (): void {
    $message = makermaker_dependency_error();
    if ( current_user_can( 'activate_plugins' ) && $message !== null ) {
        echo '<div class="notice notice-error"><p>' . esc_html( $message ) . '</p></div>';
    }
} );

function makermaker_register_dependency_cli(): void
{
    if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! class_exists( 'WP_CLI' ) || makermaker_dependency_error() === null ) {
        return;
    }

    \WP_CLI::add_command( 'makermaker', static function ( array $args, array $assocArgs ): void {
        unset( $args, $assocArgs );
        \WP_CLI::error( makermaker_dependency_error() ?? __( 'MakerMaker failed to initialize TypeRocket Pro v6.', 'makermaker' ) );
    } );
}
add_action( 'plugins_loaded', 'makermaker_register_dependency_cli', 100 );
