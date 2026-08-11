<?php
namespace Maker\MakerMaker\Generator;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

final class PluginGenerator
{
    private const TOKEN_FILES = [
        'plugin.php',
        'app/MyClassTypeRocketPlugin.php',
        'app/View.php',
        'resources/views/settings.php',
        'composer.json',
        'uninstall.php',
    ];

    public function __construct( private readonly string $templateDirectory, private readonly string $pluginDirectory )
    {
    }

    public function generate( PluginDefinition $definition ): GenerationResult
    {
        $source = realpath( $this->templateDirectory );
        $root = realpath( $this->pluginDirectory );
        if ( $source === false || ! is_dir( $source ) ) {
            throw new GeneratorException( 'Template directory does not exist.' );
        }
        if ( $root === false || ! is_dir( $root ) || ! is_writable( $root ) ) {
            throw new GeneratorException( 'WordPress plugin directory is unavailable or not writable.' );
        }

        $destination = $root . DIRECTORY_SEPARATOR . $definition->slug;
        if ( file_exists( $destination ) || is_link( $destination ) ) {
            throw new GeneratorException( 'A plugin with this slug already exists.' );
        }

        $staging = $root . DIRECTORY_SEPARATOR . '.' . $definition->slug . '.makermaker-' . bin2hex( random_bytes( 6 ) );
        try {
            $this->copyTree( $source, $staging );
            $this->replaceTokens( $staging, $definition );
            $entry = $staging . DIRECTORY_SEPARATOR . 'plugin.php';
            $class = $staging . DIRECTORY_SEPARATOR . 'app/MyClassTypeRocketPlugin.php';
            $renamedEntry = $staging . DIRECTORY_SEPARATOR . $definition->slug . '.php';
            $renamedClass = $staging . DIRECTORY_SEPARATOR . 'app/' . $definition->className() . 'TypeRocketPlugin.php';

            if ( ! rename( $entry, $renamedEntry ) || ! rename( $class, $renamedClass ) ) {
                throw new GeneratorException( 'Unable to finalize generated plugin filenames.' );
            }
            if ( ! rename( $staging, $destination ) ) {
                throw new GeneratorException( 'Unable to atomically publish the generated plugin.' );
            }

            return new GenerationResult( $destination, $destination . DIRECTORY_SEPARATOR . $definition->slug . '.php', $definition );
        } catch ( Throwable $error ) {
            $this->removeTree( $staging );
            if ( $error instanceof GeneratorException ) {
                throw $error;
            }
            throw new GeneratorException( 'Plugin generation failed: ' . $error->getMessage(), 0, $error );
        }
    }

    private function copyTree( string $source, string $destination ): void
    {
        if ( ! mkdir( $destination, 0755 ) && ! is_dir( $destination ) ) {
            throw new GeneratorException( 'Unable to create staging directory.' );
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $source, FilesystemIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ( $iterator as $item ) {
            if ( $item->isLink() ) {
                throw new GeneratorException( 'The official template contains an unsupported symbolic link.' );
            }
            $relative = substr( $item->getPathname(), strlen( $source ) + 1 );
            $target = $destination . DIRECTORY_SEPARATOR . $relative;
            if ( $item->isDir() ) {
                if ( ! mkdir( $target, 0755 ) && ! is_dir( $target ) ) {
                    throw new GeneratorException( 'Unable to create a template directory.' );
                }
            } elseif ( ! copy( $item->getPathname(), $target ) ) {
                throw new GeneratorException( 'Unable to copy a template file.' );
            }
        }
    }

    private function replaceTokens( string $staging, PluginDefinition $definition ): void
    {
        $tokens = [ '{{name}}', 'MyClass', 'MyNamespace', '{{slug}}', '{{key}}', '{{package}}', '__key__', '__KEY__' ];
        $values = [ $definition->name, $definition->className(), $definition->namespace, $definition->slug, $definition->key(), $definition->packageName(), $definition->key(), strtoupper( $definition->key() ) ];

        foreach ( self::TOKEN_FILES as $relative ) {
            $file = $staging . DIRECTORY_SEPARATOR . $relative;
            if ( ! is_file( $file ) ) {
                throw new GeneratorException( 'The official template is missing ' . $relative . '.' );
            }
            $contents = file_get_contents( $file );
            $fileValues = $values;
            if ( $relative === 'composer.json' ) {
                $fileValues[2] = str_replace( '\\', '\\\\', $definition->namespace );
            }
            if ( $contents === false || file_put_contents( $file, str_replace( $tokens, $fileValues, $contents ) ) === false ) {
                throw new GeneratorException( 'Unable to customize ' . $relative . '.' );
            }
        }
    }

    private function removeTree( string $path ): void
    {
        if ( $path === '' || ( ! file_exists( $path ) && ! is_link( $path ) ) ) {
            return;
        }
        if ( is_link( $path ) || is_file( $path ) ) {
            @unlink( $path );
            return;
        }
        $items = new FilesystemIterator( $path, FilesystemIterator::SKIP_DOTS );
        foreach ( $items as $item ) {
            $this->removeTree( $item->getPathname() );
        }
        @rmdir( $path );
    }
}
