<?php

declare( strict_types=1 );

define( 'ABSPATH', __DIR__ . '/' );
define( 'WP_CLI', true );

$hooks = [];
$filters = [];
$activationHook = null;

function add_action( string $hook, callable|string $callback, int $priority = 10 ): void
{
    global $hooks;
    $hooks[$hook][] = [ $callback, $priority ];
}

function add_filter( string $hook, callable|string $callback, int $priority = 10, int $acceptedArgs = 1 ): void
{
    global $filters;
    $filters[$hook][] = [ $callback, $priority, $acceptedArgs ];
}

function apply_filters( string $hook, mixed $value, mixed ...$args ): mixed
{
    global $filters;
    $callbacks = $filters[$hook] ?? [];
    usort( $callbacks, static fn( array $left, array $right ): int => $left[1] <=> $right[1] );
    foreach ( $callbacks as [ $callback, , $acceptedArgs ] ) {
        $value = $callback( ...array_slice( [ $value, ...$args ], 0, $acceptedArgs ) );
    }

    return $value;
}

function register_activation_hook( string $file, callable|string $callback ): void
{
    global $activationHook;
    $activationHook = [ $file, $callback ];
}

function current_user_can( string $capability ): bool
{
    return $capability === 'activate_plugins';
}

function __( string $message, string $domain = 'default' ): string
{
    unset( $domain );
    return $message;
}

function esc_html__( string $message, string $domain = 'default' ): string
{
    return __( $message, $domain );
}

function esc_html( string $message ): string
{
    return htmlspecialchars( $message, ENT_QUOTES, 'UTF-8' );
}

final class WP_CLI
{
    public static array $commands = [];

    public static function add_command( string $name, mixed $command ): void
    {
        self::$commands[$name] = $command;
    }

    public static function error( string $message ): never
    {
        throw new RuntimeException( $message );
    }
}

$assert = static function ( bool $condition, string $message ): void {
    if ( ! $condition ) {
        throw new RuntimeException( $message );
    }
};

require __DIR__ . '/../makermaker.php';

$galaxyCommand = Maker\MakerMaker\Cli\MakerResourceGalaxyCommand::class;
$galaxyFilters = $filters['typerocket_galaxy_commands'] ?? [];
$assert( count( $galaxyFilters ) === 1, 'MakerMaker did not register the Galaxy command filter exactly once.' );
$assert( $galaxyFilters[0][0] === 'makermaker_register_galaxy_command', 'MakerMaker registered an unexpected Galaxy callback.' );
$assert( apply_filters( 'typerocket_galaxy_commands', [] ) === [ $galaxyCommand ], 'Empty Galaxy commands did not receive MakerMaker.' );
$existing = [ 'Existing\\FirstCommand', 'Existing\\SecondCommand' ];
$assert( apply_filters( 'typerocket_galaxy_commands', $existing ) === [ ...$existing, $galaxyCommand ], 'Existing Galaxy command order was not preserved.' );
$alreadyRegistered = [ 'Existing\\FirstCommand', $galaxyCommand ];
$assert( apply_filters( 'typerocket_galaxy_commands', $alreadyRegistered ) === $alreadyRegistered, 'An existing MakerMaker Galaxy command was duplicated.' );
$repeated = apply_filters( 'typerocket_galaxy_commands', apply_filters( 'typerocket_galaxy_commands', $existing ) );
$assert( array_count_values( $repeated )[$galaxyCommand] === 1, 'Repeated Galaxy filtering duplicated MakerMaker.' );

$assert( isset( $hooks['typerocket_loaded'], $hooks['admin_notices'], $hooks['plugins_loaded'] ), 'MakerMaker did not register its runtime hooks.' );
$assert( $activationHook !== null, 'MakerMaker did not register its activation hook.' );
$assert( makermaker_dependency_error() !== null, 'Missing TypeRocket should produce an actionable dependency error.' );

ob_start();
( $hooks['admin_notices'][0][0] )();
$notice = (string) ob_get_clean();
$assert( str_contains( $notice, 'Install and activate TypeRocket Pro' ), 'Admin dependency notice is not actionable.' );

( $hooks['plugins_loaded'][0][0] )();
$assert( isset( WP_CLI::$commands['makermaker'] ), 'Missing TypeRocket did not register the WP-CLI dependency command.' );
try {
    ( WP_CLI::$commands['makermaker'] )( [], [] );
    $assert( false, 'The dependency command should fail.' );
} catch ( RuntimeException $error ) {
    $assert( str_contains( $error->getMessage(), 'Install and activate TypeRocket Pro' ), 'WP-CLI dependency error is not actionable.' );
}

$boundary = file_get_contents( __DIR__ . '/../CORE-BOUNDARY.md' );
$readme = file_get_contents( __DIR__ . '/../README.md' );
$entry = file_get_contents( __DIR__ . '/../makermaker.php' );
$command = file_get_contents( __DIR__ . '/../app/Cli/MakerMakerCommand.php' );
$assert( ! is_file( __DIR__ . '/../app/Cli/GalaxyRegistrar.php' ), 'The obsolete Galaxy configuration mutator still exists.' );
$assert( ! str_contains( $command, 'function register_galaxy' ), 'The obsolete register-galaxy command still exists.' );
$assert( str_contains( $command, 'function register_plugin_galaxy' ), 'register-plugin-galaxy was removed.' );
$assert( str_contains( $boundary, 'FRAMEWORK CORE — DO NOT EDIT; update from playground releases' ), 'Core edit marker is missing.' );
$assert( str_contains( $boundary, 'Every tracked file beneath `wp-content/plugins/makermaker/` is core-owned' ), 'Core ownership is not documented.' );
$assert( str_contains( $boundary, '`plugins/<site>-app/`' ), 'Site application workspace is not documented.' );
$assert( str_contains( $readme, 'Existing generated plugins remain separate and continue to boot when MakerMaker is disabled' ), 'Independent generated-plugin boot is not documented.' );
$assert( preg_match( '/Requires at least:\s+6\.5/', $entry ) === 1, 'WordPress version contract is missing.' );
$assert( preg_match( '/Requires PHP:\s+8\.2/', $entry ) === 1, 'PHP version contract is missing.' );

echo "PASS: package boundary and dependency contract\n";
