<?php
namespace Maker\MakerMaker\Cli;

use Maker\MakerMaker\Generator\ResourceDefinition;
use Maker\MakerMaker\Generator\ResourceGeneratorFactory;
use Symfony\Component\Console\Input\InputOption;
use Throwable;
use TypeRocket\Console\Command;

final class MakerResourceGalaxyCommand extends Command
{
    protected $command = [
        'make:maker-resource',
        'Generate a safe MakerMaker MVC resource in an existing plugin.',
        'Generates required MVC files and an explicit registry entry without overwriting files.',
    ];

    protected function config(): void
    {
        $this->addArgument( 'name', self::REQUIRED, 'PascalCase resource name.' );
        $this->addOption( 'plugin', null, InputOption::VALUE_REQUIRED, 'Target plugin slug.' );
        $this->addOption( 'namespace', null, InputOption::VALUE_REQUIRED, 'Target plugin PHP namespace.' );
        $this->addOption( 'plural', null, InputOption::VALUE_REQUIRED, 'Explicit lowercase snake_case resource/table plural.' );
        foreach ( [ 'migration', 'views', 'factory', 'tests' ] as $option ) {
            $this->addOption( $option, null, InputOption::VALUE_NONE, 'Generate optional ' . $option . ' files.' );
        }
    }

    protected function exec(): void
    {
        try {
            $options = [];
            foreach ( [ 'migration', 'views', 'factory', 'tests' ] as $option ) {
                $options[$option] = (bool) $this->getOption( $option );
            }
            $factory = new ResourceGeneratorFactory();
            $context = $factory->resolveContext(
                (string) $this->getOption( 'plugin' ),
                str_replace( '/', '\\', (string) $this->getOption( 'namespace' ) )
            );
            $definition = new ResourceDefinition(
                (string) $this->getArgument( 'name' ),
                $context['namespace'],
                $options,
                (string) $this->getOption( 'plural' )
            );
            $result = $factory->create( $context['slug'] )->generate( $definition );
            $message = 'Generated ' . $definition->name . ' resource (' . count( $result->files ) . ' files).';
            if ( $definition->enabled( 'migration' ) ) {
                $launcher = 'galaxy_' . str_replace( '-', '_', $context['slug'] );
                $message .= ' Migration generated but not applied. Run: php ' . $launcher . ' migrate up';
            }
            $this->success( $message );
        } catch ( Throwable $error ) {
            $this->error( $error->getMessage() );
            $this->success = 1;
        }
    }
}
