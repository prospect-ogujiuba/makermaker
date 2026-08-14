<?php
namespace Maker\MakerMaker;

use RuntimeException;
use TypeRocket\Register\Page;

final class ResourceRegistrar
{
    /** @return array<string, array<string, mixed>> */
    public static function load( string $registryPath ): array
    {
        if ( ! is_file( $registryPath ) ) {
            return [];
        }

        $resources = require $registryPath;
        if ( ! is_array( $resources ) ) {
            throw new RuntimeException( 'MakerMaker resource registry must return an array.' );
        }

        return $resources;
    }

    public static function register( string $registryPath ): void
    {
        foreach ( self::load( $registryPath ) as $key => $resource ) {
            if ( ! is_array( $resource ) ) {
                throw new RuntimeException( 'Invalid MakerMaker resource definition: ' . $key );
            }

            foreach ( [ 'name', 'title', 'controller', 'capabilities' ] as $required ) {
                if ( ! array_key_exists( $required, $resource ) ) {
                    throw new RuntimeException( 'MakerMaker resource ' . $key . ' is missing ' . $required . '.' );
                }
            }

            if ( function_exists( 'tr_roles' ) && is_array( $resource['capabilities'] ) ) {
                tr_roles()->updateRolesCapabilities( 'administrator', $resource['capabilities'] );
            }

            $page = tr_resource_pages(
                $resource['name'] . '@' . $resource['controller'],
                $resource['title']
            );
            if ( ! $page instanceof Page ) {
                throw new RuntimeException( 'TypeRocket did not create the admin resource page: ' . $key );
            }

            if ( isset( $resource['icon'] ) && is_string( $resource['icon'] ) && $resource['icon'] !== '' ) {
                $page->setIcon( $resource['icon'] );
            }
            if ( isset( $resource['position'] ) && is_int( $resource['position'] ) ) {
                $page->setPosition( $resource['position'] );
            }
            if ( is_string( $resource['capabilities'][0] ?? null ) ) {
                $page->setCapability( $resource['capabilities'][0] );
            }
        }
    }
}
