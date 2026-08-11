<?php
namespace Maker\MakerMaker\Cli;

use Maker\MakerMaker\Generator\GeneratorFactory;
use Maker\MakerMaker\Generator\PluginDefinition;
use Throwable;
use WP_CLI;

final class MakerMakerCommand
{
    public function __construct( private readonly GeneratorFactory $factory )
    {
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
