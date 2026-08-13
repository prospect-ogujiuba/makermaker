<?php
namespace Maker\MakerMaker\Generator;

final class ResourceGeneratorFactory
{
    /** @return array{slug: string, namespace: string} */
    public function resolveContext( string $pluginSlug, string $namespace ): array
    {
        $pluginSlug = trim( $pluginSlug );
        $namespace = trim( $namespace );
        if ( $pluginSlug === '' && defined( 'MAKERMAKER_GALAXY_PLUGIN_SLUG' ) ) {
            $pluginSlug = (string) MAKERMAKER_GALAXY_PLUGIN_SLUG;
        }
        if ( $namespace === '' && defined( 'MAKERMAKER_GALAXY_PLUGIN_NAMESPACE' ) ) {
            $namespace = (string) MAKERMAKER_GALAXY_PLUGIN_NAMESPACE;
        }
        if ( $pluginSlug === '' || $namespace === '' ) {
            throw new GeneratorException( 'Plugin and namespace are required outside a plugin-specific Galaxy context.' );
        }
        return [ 'slug' => $pluginSlug, 'namespace' => $namespace ];
    }

    public function create( string $pluginSlug ): ResourceGenerator
    {
        if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
            throw new GeneratorException( 'WordPress plugin directory is not defined.' );
        }
        if ( ! preg_match( '/^[a-z][a-z0-9]*(?:-[a-z0-9]+)*$/', $pluginSlug ) ) {
            throw new GeneratorException( 'Plugin must be a lowercase kebab-case slug.' );
        }

        $plugins = realpath( (string) WP_PLUGIN_DIR );
        $plugin = $plugins === false ? false : realpath( $plugins . DIRECTORY_SEPARATOR . $pluginSlug );
        if ( $plugins === false || $plugin === false || ! is_dir( $plugin ) ) {
            throw new GeneratorException( 'Target plugin does not exist.' );
        }
        if ( dirname( $plugin ) !== $plugins || is_link( $plugins . DIRECTORY_SEPARATOR . $pluginSlug ) ) {
            throw new GeneratorException( 'Target plugin must be a direct, non-symbolic child of WP_PLUGIN_DIR.' );
        }

        return new ResourceGenerator( $plugin );
    }
}
