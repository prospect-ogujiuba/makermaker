<?php
namespace Maker\MakerMaker\Generator;

final class TemplateLocator
{
    public function locate(): string
    {
        if ( ! defined( 'TYPEROCKET_PATH' ) ) {
            throw new GeneratorException( 'TypeRocket Pro v6 is not loaded.' );
        }

        $typeRocketRoot = realpath( (string) TYPEROCKET_PATH );
        $template = realpath( (string) TYPEROCKET_PATH . '/vendor/typerocket/professional/templates/Plugin' );

        if ( $typeRocketRoot === false || $template === false || ! is_dir( $template ) ) {
            throw new GeneratorException( 'The official TypeRocket Professional plugin template is unavailable.' );
        }
        if ( ! str_starts_with( $template . DIRECTORY_SEPARATOR, $typeRocketRoot . DIRECTORY_SEPARATOR ) ) {
            throw new GeneratorException( 'The TypeRocket template resolved outside the installed framework.' );
        }

        foreach ( [ 'plugin.php', 'app/MyClassTypeRocketPlugin.php', 'composer.json' ] as $required ) {
            if ( ! is_file( $template . DIRECTORY_SEPARATOR . $required ) ) {
                throw new GeneratorException( 'The TypeRocket plugin template is incomplete.' );
            }
        }

        return $template;
    }
}
