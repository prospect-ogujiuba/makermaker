<?php
namespace Maker\MakerMaker\Cli;

use Maker\MakerMaker\Generator\GeneratorFactory;
use Maker\MakerMaker\Cli\GalaxyRegistrar;
use Maker\MakerMaker\Generator\GalaxyContextInstaller;
use Maker\MakerMaker\Generator\PluginDefinition;
use Maker\MakerMaker\Generator\ResourceDefinition;
use Maker\MakerMaker\Generator\ResourceGeneratorFactory;
use Throwable;
use WP_CLI;

final class MakerMakerCommand
{
    public function __construct( private readonly GeneratorFactory $factory )
    {
    }

    /**
     * Generate a TypeRocket MVC resource in an existing plugin.
     *
     * ## OPTIONS
     *
     * <name>
     * : PascalCase resource name.
     *
     * --plugin=<slug>
     * : Existing plugin slug below WP_PLUGIN_DIR.
     *
     * --namespace=<namespace>
     * : Target plugin PHP namespace with at least two segments.
     *
     * [--plural=<plural>]
     * : Explicit lowercase snake_case resource/table plural.
     *
     * [--migration]
     * : Generate a TypeRocket migration skeleton.
     *
     * [--views]
     * : Generate index and form view skeletons.
     *
     * [--factory]
     * : Generate a focused test factory.
     *
     * [--tests]
     * : Generate a resource test skeleton.
     */
    public function resource( array $args, array $assocArgs ): void
    {
        try {
            $options = [];
            foreach ( [ 'migration', 'views', 'factory', 'tests' ] as $option ) {
                $options[$option] = isset( $assocArgs[$option] );
            }
            $definition = new ResourceDefinition(
                (string) ( $args[0] ?? '' ),
                (string) ( $assocArgs['namespace'] ?? '' ),
                $options,
                (string) ( $assocArgs['plural'] ?? '' )
            );
            $result = ( new ResourceGeneratorFactory() )
                ->create( (string) ( $assocArgs['plugin'] ?? '' ) )
                ->generate( $definition );
            WP_CLI::success( 'Generated ' . $definition->name . ' resource (' . count( $result->files ) . ' files) in ' . $result->pluginDirectory );
        } catch ( Throwable $error ) {
            WP_CLI::error( $error->getMessage() );
        }
    }

    /**
     * Generate a TypeRocket Pro v6 plugin.
     *
     * ## OPTIONS
     *
     * <slug>
     * : Lowercase kebab-case plugin slug.
     *
     * --name=<name>
     * : Plugin display name.
     *
     * --namespace=<namespace>
     * : PHP namespace with at least two segments.
     *
     * [--vendor=<vendor>]
     * : Composer package vendor. Defaults to maker.
     *
     * [--activate]
     * : Activate after generation.
     */
    /**
     * Register MakerMaker's Galaxy command idempotently.
     *
     * ## OPTIONS
     *
     * [--typerocket-path=<path>]
     * : TypeRocket root. Defaults to TYPEROCKET_PATH.
     *
     * @subcommand register-galaxy
     */
    public function register_galaxy( array $args, array $assocArgs ): void
    {
        try {
            $path = (string) ( $assocArgs['typerocket-path'] ?? ( defined( 'TYPEROCKET_PATH' ) ? TYPEROCKET_PATH : '' ) );
            $changed = ( new GalaxyRegistrar() )->register( $path );
            WP_CLI::success( $changed ? 'Registered MakerMaker Galaxy command.' : 'MakerMaker Galaxy command is already registered.' );
        } catch ( Throwable $error ) {
            WP_CLI::error( $error->getMessage() );
        }
    }

    /**
     * Backfill a plugin-specific Galaxy launcher for an existing plugin.
     *
     * ## OPTIONS
     *
     * --plugin=<slug>
     * : Existing direct plugin slug.
     *
     * --namespace=<namespace>
     * : Plugin PHP namespace.
     *
     * @subcommand register-plugin-galaxy
     */
    public function register_plugin_galaxy( array $args, array $assocArgs ): void
    {
        try {
            if ( ! defined( 'WP_PLUGIN_DIR' ) || ! defined( 'ABSPATH' ) ) {
                throw new \RuntimeException( 'WordPress plugin directory and root are required.' );
            }
            $definition = new PluginDefinition(
                (string) ( $assocArgs['plugin'] ?? '' ),
                (string) ( $assocArgs['plugin'] ?? '' ),
                str_replace( '/', '\\', (string) ( $assocArgs['namespace'] ?? '' ) )
            );
            $result = ( new GalaxyContextInstaller() )->install(
                $definition,
                (string) WP_PLUGIN_DIR,
                rtrim( (string) ABSPATH, '/\\' )
            );
            WP_CLI::success( $result['changed'] ? 'Registered plugin-specific Galaxy context.' : 'Plugin-specific Galaxy context is already registered.' );
        } catch ( Throwable $error ) {
            WP_CLI::error( $error->getMessage() );
        }
    }

    public function create( array $args, array $assocArgs ): void
    {
        try {
            $definition = new PluginDefinition(
                (string) ( $assocArgs['name'] ?? '' ),
                (string) ( $args[0] ?? '' ),
                (string) ( $assocArgs['namespace'] ?? '' ),
                (string) ( $assocArgs['vendor'] ?? 'maker' )
            );
            $result = $this->factory->create()->generate( $definition );

            if ( isset( $assocArgs['activate'] ) ) {
                if ( ! function_exists( 'activate_plugin' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }
                $activation = activate_plugin( $definition->slug . '/' . basename( $result->entryFile ) );
                if ( is_wp_error( $activation ) ) {
                    WP_CLI::warning( 'Plugin generated, but activation failed: ' . $activation->get_error_message() );
                    return;
                }
            }

            WP_CLI::success( 'Generated ' . $definition->name . ' at ' . $result->directory );
        } catch ( Throwable $error ) {
            WP_CLI::error( $error->getMessage() );
        }
    }
}
