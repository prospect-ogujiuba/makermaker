<?php
use Maker\MakerMaker\Generator\GeneratorException;
use Maker\MakerMaker\Generator\PluginDefinition;
use Maker\MakerMaker\Generator\PluginGenerator;

require __DIR__ . '/../app/Generator/GeneratorException.php';
require __DIR__ . '/../app/Generator/PluginDefinition.php';
require __DIR__ . '/../app/Generator/GenerationResult.php';
require __DIR__ . '/../app/Generator/PluginGenerator.php';

$tests = [];
$test = static function ( string $name, callable $callback ) use ( &$tests ): void { $tests[] = [ $name, $callback ]; };
$assert = static function ( bool $condition, string $message = 'Assertion failed' ): void {
    if ( ! $condition ) {
        throw new RuntimeException( $message );
    }
};
$expectFailure = static function ( callable $callback ) use ( $assert ): void {
    try {
        $callback();
    } catch ( GeneratorException ) {
        return;
    }
    $assert( false, 'Expected GeneratorException.' );
};
$remove = static function ( string $path ) use ( &$remove ): void {
    if ( is_link( $path ) || is_file( $path ) ) { @unlink( $path ); return; }
    if ( ! is_dir( $path ) ) { return; }
    foreach ( new FilesystemIterator( $path, FilesystemIterator::SKIP_DOTS ) as $item ) { $remove( $item->getPathname() ); }
    @rmdir( $path );
};

$test( 'validates definitions', static function () use ( $assert, $expectFailure ): void {
    $definition = new PluginDefinition( 'Client Portal', 'client-portal', 'Maker\\ClientPortal', 'prospect' );
    $assert( $definition->key() === 'client_portal' );
    $assert( $definition->className() === 'ClientPortal' );
    $assert( $definition->packageName() === 'prospect/client-portal' );
    foreach ( [ '../escape', 'Uppercase', 'two--dashes', '9first' ] as $slug ) {
        $expectFailure( static fn() => new PluginDefinition( 'Bad', $slug, 'Maker\\Bad' ) );
    }
    foreach ( [ 'Single', 'Bad-Name\\Plugin', '..\\Escape' ] as $namespace ) {
        $expectFailure( static fn() => new PluginDefinition( 'Bad', 'bad-plugin', $namespace ) );
    }
} );

$test( 'generates and substitutes the official-shaped scaffold', static function () use ( $assert, $remove ): void {
    $root = sys_get_temp_dir() . '/makermaker-test-' . bin2hex( random_bytes( 5 ) );
    mkdir( $root );
    try {
        $definition = new PluginDefinition( 'Client Portal', 'client-portal', 'Maker\\ClientPortal', 'prospect' );
        $result = ( new PluginGenerator( __DIR__ . '/fixtures/plugin-template', $root ) )->generate( $definition );
        $assert( is_file( $result->entryFile ) );
        $assert( is_file( $result->directory . '/app/ClientPortalTypeRocketPlugin.php' ) );
        $entry = file_get_contents( $result->entryFile );
        $composer = file_get_contents( $result->directory . '/composer.json' );
        $assert( str_contains( $entry, 'Client Portal' ) && str_contains( $entry, 'Maker\\ClientPortal' ) );
        $assert( str_contains( $composer, 'prospect/client-portal' ) );
        $assert( ! str_contains( $entry . $composer, 'MyNamespace' ) );
    } finally {
        $remove( $root );
    }
} );

$test( 'rejects collisions and cleans partial output', static function () use ( $assert, $expectFailure, $remove ): void {
    $root = sys_get_temp_dir() . '/makermaker-test-' . bin2hex( random_bytes( 5 ) );
    mkdir( $root );
    mkdir( $root . '/existing-plugin' );
    try {
        $generator = new PluginGenerator( __DIR__ . '/fixtures/plugin-template', $root );
        $expectFailure( static fn() => $generator->generate( new PluginDefinition( 'Existing', 'existing-plugin', 'Maker\\Existing' ) ) );

        $broken = $root . '/broken-template';
        mkdir( $broken );
        copy( __DIR__ . '/fixtures/plugin-template/plugin.php', $broken . '/plugin.php' );
        $expectFailure( static fn() => ( new PluginGenerator( $broken, $root ) )->generate( new PluginDefinition( 'Broken', 'broken-plugin', 'Maker\\Broken' ) ) );
        $staging = glob( $root . '/.broken-plugin.makermaker-*' );
        $assert( $staging === [], 'Partial staging directory remained.' );
    } finally {
        $remove( $root );
    }
} );

$failed = 0;
foreach ( $tests as [ $name, $callback ] ) {
    try { $callback(); echo "PASS: {$name}\n"; }
    catch ( Throwable $error ) { $failed++; fwrite( STDERR, "FAIL: {$name}: {$error->getMessage()}\n" ); }
}
echo count( $tests ) . " tests, {$failed} failures\n";
exit( $failed === 0 ? 0 : 1 );
