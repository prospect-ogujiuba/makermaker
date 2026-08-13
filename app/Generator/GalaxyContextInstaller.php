<?php
namespace Maker\MakerMaker\Generator;

use Throwable;

final class GalaxyContextInstaller
{
    /** @return array{launcher: string, config: string, changed: bool} */
    public function install( PluginDefinition $definition, string $pluginDirectory, string $wordpressRoot ): array
    {
        $plugins = realpath( $pluginDirectory );
        $root = realpath( $wordpressRoot );
        $plugin = $plugins === false ? false : realpath( $plugins . DIRECTORY_SEPARATOR . $definition->slug );
        if ( $plugins === false || $root === false || $plugin === false || dirname( $plugin ) !== $plugins
            || is_link( $pluginDirectory . DIRECTORY_SEPARATOR . $definition->slug ) || ! is_writable( $root ) ) {
            throw new GeneratorException( 'Existing plugin or WordPress root is unavailable, indirect, symbolic, or not writable.' );
        }

        $launcher = $root . DIRECTORY_SEPARATOR . GalaxyContext::launcherName( $definition );
        $config = $root . DIRECTORY_SEPARATOR . GalaxyContext::configName( $definition );
        $expected = $this->expectedFiles( $definition, $plugin, $launcher, $config );
        $existing = 0;
        foreach ( $expected as $path => $contents ) {
            if ( is_link( $path ) ) {
                throw new GeneratorException( 'Galaxy destination cannot be symbolic: ' . basename( $path ) );
            }
            if ( file_exists( $path ) ) {
                $existing++;
                if ( ! is_file( $path ) || file_get_contents( $path ) !== $contents ) {
                    throw new GeneratorException( 'Refusing to overwrite different Galaxy destination: ' . basename( $path ) );
                }
            }
        }
        if ( $existing === count( $expected ) ) {
            $launcherMode = fileperms( $launcher ) & 0777;
            $configMode = fileperms( $config ) & 0777;
            if ( $launcherMode === 0755 && $configMode === 0644 ) {
                return [ 'launcher' => $launcher, 'config' => $config, 'changed' => false ];
            }
            return $this->repairModes( $launcher, $config, $root, $expected );
        }
        if ( $existing !== 0 ) {
            throw new GeneratorException( 'Galaxy context is incomplete; refusing a partial collision.' );
        }

        $lockPath = $root . DIRECTORY_SEPARATOR . '.makermaker-plugin.lock';
        $lock = fopen( $lockPath, 'c+' );
        if ( $lock === false ) {
            throw new GeneratorException( 'Unable to create the Galaxy context lock.' );
        }
        $staging = $root . DIRECTORY_SEPARATOR . '.makermaker-galaxy-backfill-' . bin2hex( random_bytes( 6 ) );
        $published = [];
        try {
            if ( ! flock( $lock, LOCK_EX ) || ! mkdir( $staging, 0700 ) ) {
                throw new GeneratorException( 'Unable to lock or stage the Galaxy context.' );
            }
            foreach ( $expected as $path => $contents ) {
                if ( file_exists( $path ) || is_link( $path ) ) {
                    throw new GeneratorException( 'Galaxy destination appeared during generation: ' . basename( $path ) );
                }
                $staged = $staging . DIRECTORY_SEPARATOR . basename( $path );
                $mode = $path === $launcher ? 0755 : 0644;
                if ( file_put_contents( $staged, $contents, LOCK_EX ) === false
                    || ! chmod( $staged, $mode ) ) {
                    throw new GeneratorException( 'Unable to stage Galaxy context file.' );
                }
            }
            foreach ( array_keys( $expected ) as $path ) {
                $staged = $staging . DIRECTORY_SEPARATOR . basename( $path );
                if ( file_exists( $path ) || is_link( $path ) || ! rename( $staged, $path ) ) {
                    throw new GeneratorException( 'Unable to atomically publish Galaxy context file.' );
                }
                $published[] = $path;
            }
            @rmdir( $staging );
        } catch ( Throwable $error ) {
            foreach ( array_reverse( $published ) as $path ) { @unlink( $path ); }
            foreach ( array_keys( $expected ) as $path ) { @unlink( $staging . DIRECTORY_SEPARATOR . basename( $path ) ); }
            @rmdir( $staging );
            if ( $error instanceof GeneratorException ) { throw $error; }
            throw new GeneratorException( 'Galaxy context installation failed: ' . $error->getMessage(), 0, $error );
        } finally {
            flock( $lock, LOCK_UN );
            fclose( $lock );
            @unlink( $lockPath );
        }

        return [ 'launcher' => $launcher, 'config' => $config, 'changed' => true ];
    }

    /** @return array<string, string> */
    private function expectedFiles( PluginDefinition $definition, string $plugin, string $launcher, string $config ): array
    {
        $expected = [
            $launcher => GalaxyContext::launcher( $definition ),
            $config => GalaxyContext::config( $definition ),
        ];
        if ( $definition->slug !== 'makermaker' || $definition->namespace !== 'Maker\\MakerMaker' ) {
            return $expected;
        }

        $sources = [
            $launcher => $plugin . '/galaxy/galaxy_makermaker',
            $config => $plugin . '/galaxy/galaxy-makermaker-config.php',
        ];
        foreach ( $sources as $destination => $source ) {
            if ( ! is_file( $source ) || is_link( $source ) ) {
                throw new GeneratorException( 'Canonical MakerMaker Galaxy source is missing, non-regular, or symbolic: ' . basename( $source ) );
            }
            $sourceMode = fileperms( $source ) & 0777;
            $expectedMode = $destination === $launcher ? 0755 : 0644;
            if ( $sourceMode !== $expectedMode ) {
                throw new GeneratorException( 'Canonical MakerMaker Galaxy source has an unsafe mode: ' . basename( $source ) );
            }
            $contents = file_get_contents( $source );
            if ( ! is_string( $contents ) || $contents !== $expected[$destination] ) {
                throw new GeneratorException( 'Canonical MakerMaker Galaxy source does not match the portable Galaxy contract: ' . basename( $source ) );
            }
            $expected[$destination] = $contents;
        }
        return $expected;
    }

    /** @return array{launcher: string, config: string, changed: bool} */
    /** @param array<string, string> $expected */
    private function repairModes( string $launcher, string $config, string $root, array $expected ): array
    {
        $lockPath = $root . DIRECTORY_SEPARATOR . '.makermaker-plugin.lock';
        $lock = fopen( $lockPath, 'c+' );
        if ( $lock === false ) {
            throw new GeneratorException( 'Unable to create the Galaxy context lock.' );
        }
        try {
            if ( ! flock( $lock, LOCK_EX ) ) {
                throw new GeneratorException( 'Unable to lock Galaxy context modes.' );
            }
            foreach ( $expected as $path => $contents ) {
                if ( is_link( $path ) || ! is_file( $path ) || file_get_contents( $path ) !== $contents ) {
                    throw new GeneratorException( 'Galaxy destination changed before mode repair: ' . basename( $path ) );
                }
            }
            if ( ! chmod( $launcher, 0755 ) || ! chmod( $config, 0644 ) ) {
                throw new GeneratorException( 'Unable to repair Galaxy context modes.' );
            }
        } finally {
            flock( $lock, LOCK_UN );
            fclose( $lock );
            @unlink( $lockPath );
        }
        return [ 'launcher' => $launcher, 'config' => $config, 'changed' => true ];
    }
}
