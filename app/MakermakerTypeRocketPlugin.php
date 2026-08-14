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
    protected $migrations = true;

    public function init(): void
    {
        ResourceRegistrar::register( dirname( __DIR__ ) . '/config/makermaker-resources.php' );

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
        $this->migrateUp();
    }

    public function deactivate(): void
    {
    }

    public function uninstall(): void
    {
        $this->migrateDown();
    }
}
