<?php
namespace Maker\MakerMaker;

use Maker\MakerMaker\Admin\GeneratorPage;
use Maker\MakerMaker\Cli\MakerMakerCommand;
use Maker\MakerMaker\Generator\GeneratorFactory;
use TypeRocket\Pro\Register\BasePlugin;

final class MakermakerTypeRocketPlugin extends BasePlugin
{
    protected $title = 'MakerMaker';
    protected $slug = 'makermaker';
    protected $migrationKey = 'makermaker_migrations';
    protected $migrations = false;

    public function init(): void
    {
        $factory = new GeneratorFactory();
        ( new GeneratorPage( $factory ) )->register();

        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            \WP_CLI::add_command( 'makermaker', new MakerMakerCommand( $factory ) );
        }
    }

    public function routes(): void
    {
    }

    public function policies(): array
    {
        return [];
    }

    public function activate(): void
    {
    }

    public function deactivate(): void
    {
    }

    public function uninstall(): void
    {
    }
}
