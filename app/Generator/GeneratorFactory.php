<?php
namespace Maker\MakerMaker\Generator;

final class GeneratorFactory
{
    public function create(): PluginGenerator
    {
        if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
            throw new GeneratorException( 'WordPress plugin directory is not defined.' );
        }

        return new PluginGenerator( ( new TemplateLocator() )->locate(), (string) WP_PLUGIN_DIR );
    }
}
