<?php
use Maker\MakerMaker\Generator\GeneratorException;
use Maker\MakerMaker\Generator\PluginDefinition;
use Maker\MakerMaker\Generator\PluginGenerator;
use Maker\MakerMaker\Generator\ResourceDefinition;
use Maker\MakerMaker\Generator\ResourceGenerator;
use Maker\MakerMaker\Cli\GalaxyRegistrar;
use Maker\MakerMaker\Generator\ResourceGeneratorFactory;
use Maker\MakerMaker\Generator\GalaxyContextInstaller;
use Maker\MakerMaker\Generator\GalaxyContext;

require __DIR__ . '/../app/Generator/GeneratorException.php';
require __DIR__ . '/../app/Generator/PluginDefinition.php';
require __DIR__ . '/../app/Generator/GenerationResult.php';
require __DIR__ . '/../app/Generator/GalaxyContext.php';
require __DIR__ . '/../app/Generator/GalaxyContextInstaller.php';
require __DIR__ . '/../app/Generator/PluginGenerator.php';
require __DIR__ . '/../app/Generator/ResourceDefinition.php';
require __DIR__ . '/../app/Generator/ResourceGenerationResult.php';
require __DIR__ . '/../app/Generator/ResourceGenerator.php';
require __DIR__ . '/../app/Generator/ResourceGeneratorFactory.php';
require __DIR__ . '/../app/Cli/GalaxyRegistrar.php';

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
        mkdir( $root . '/wp-content/plugins', 0755, true );
        $plugins = $root . '/wp-content/plugins';
        $result = ( new PluginGenerator( __DIR__ . '/fixtures/plugin-template', $plugins, $root ) )->generate( $definition );
        $assert( is_file( $result->entryFile ) );
        $assert( is_file( $result->directory . '/app/ClientPortalTypeRocketPlugin.php' ) );
        $entry = file_get_contents( $result->entryFile );
        $composer = file_get_contents( $result->directory . '/composer.json' );
        $composerData = json_decode( $composer, true, 512, JSON_THROW_ON_ERROR );
        $assert( str_contains( $entry, 'Client Portal' ) && str_contains( $entry, 'Maker\\ClientPortal' ) );
        $assert( $composerData['name'] === 'prospect/client-portal' );
        $assert( isset( $composerData['autoload']['psr-4']['Maker\\ClientPortal\\'] ) );
        $assert( ! str_contains( $entry . $composer, 'MyNamespace' ) );
        $assert( is_file( $result->galaxyLauncher ) && is_executable( $result->galaxyLauncher ) );
        $assert( is_file( $result->galaxyConfig ) );
        $launcher = file_get_contents( $result->galaxyLauncher );
        $config = file_get_contents( $result->galaxyConfig );
        $assert( str_contains( $launcher, "galaxy-client-portal-config.php" ) );
        $assert( ! str_contains( $launcher, '/mu-plugins' ) && ! str_contains( GalaxyContext::siteLauncher(), '/mu-plugins' ) );
        $assert( str_contains( $config, "'client-portal'" ) && str_contains( $config, "'Maker\\\\ClientPortal'" ) );
        $assert( str_contains( $config, 'MAKERMAKER_GALAXY_TYPEROCKET_PATH' ) );
        $assert( str_contains( $config, 'MAKERMAKER_GALAXY_PLUGIN_PATH' ) );
        $assert( str_contains( $config, "TYPEROCKET_APP_ROOT_PATH" ) && ! str_contains( $config, $root ) );
        $assert( ! str_contains( $config, 'wp-content/plugins' ) && ! str_contains( $config, 'typerocket-pro-v6' ) );
        exec( escapeshellarg( PHP_BINARY ) . ' -l ' . escapeshellarg( $result->galaxyConfig ) . ' 2>&1', $output, $status );
        $assert( $status === 0, 'Generated Galaxy config failed lint.' );
    } finally {
        $remove( $root );
    }
} );

$test( 'rejects collisions and cleans partial output', static function () use ( $assert, $expectFailure, $remove ): void {
    $root = sys_get_temp_dir() . '/makermaker-test-' . bin2hex( random_bytes( 5 ) );
    mkdir( $root );
    mkdir( $root . '/wp-content/plugins', 0755, true );
    $plugins = $root . '/wp-content/plugins';
    mkdir( $plugins . '/existing-plugin' );
    try {
        $generator = new PluginGenerator( __DIR__ . '/fixtures/plugin-template', $plugins, $root );
        $expectFailure( static fn() => $generator->generate( new PluginDefinition( 'Existing', 'existing-plugin', 'Maker\\Existing' ) ) );

        if ( function_exists( 'symlink' ) && @symlink( sys_get_temp_dir(), $plugins . '/linked-plugin' ) ) {
            $expectFailure( static fn() => $generator->generate( new PluginDefinition( 'Linked', 'linked-plugin', 'Maker\\Linked' ) ) );
        }

        file_put_contents( $root . '/galaxy_collision', 'existing' );
        $collision = new PluginDefinition( 'Collision', 'collision', 'Maker\\Collision' );
        $expectFailure( static fn() => $generator->generate( $collision ) );
        $assert( ! is_dir( $plugins . '/collision' ) && ! is_file( $root . '/galaxy-collision-config.php' ) );

        $broken = $root . '/broken-template';
        mkdir( $broken );
        copy( __DIR__ . '/fixtures/plugin-template/plugin.php', $broken . '/plugin.php' );
        $expectFailure( static fn() => ( new PluginGenerator( $broken, $plugins, $root ) )->generate( new PluginDefinition( 'Broken', 'broken-plugin', 'Maker\\Broken' ) ) );
        $staging = glob( $plugins . '/.broken-plugin.makermaker-*' );
        $assert( $staging === [], 'Partial staging directory remained.' );
    } finally {
        $remove( $root );
    }
} );

$test( 'validates resource definitions strictly', static function () use ( $assert, $expectFailure ): void {
    $definition = new ResourceDefinition( 'LineItem', 'Maker\\Inventory', [ 'views' => true ] );
    $assert( $definition->key() === 'line_item' );
    $assert( $definition->pluralKey() === 'line_items' );
    $assert( ( new ResourceDefinition( 'Category', 'Maker\\Inventory' ) )->pluralKey() === 'categories' );
    $assert( ( new ResourceDefinition( 'Address', 'Maker\\Inventory' ) )->pluralKey() === 'addresses' );
    $assert( ( new ResourceDefinition( 'Person', 'Maker\\Inventory', [], 'people' ) )->pluralKey() === 'people' );
    $reserved = [ 'Class', 'Trait', 'Enum', 'Match', 'Self', 'Parent', 'True', 'False', 'Null', 'Int', 'Float', 'Bool', 'String', 'Void', 'Iterable', 'Object', 'Mixed', 'Never', 'Callable' ];
    foreach ( [ 'product', '../Product', 'Product/Child', '9Product', ...$reserved, str_repeat( 'A', 65 ) ] as $name ) {
        $expectFailure( static fn() => new ResourceDefinition( $name, 'Maker\\Inventory' ) );
    }
    foreach ( [ 'Single', 'Maker\\bad-name', ...array_map( static fn( string $identifier ): string => 'Maker\\' . $identifier, $reserved ) ] as $namespace ) {
        $expectFailure( static fn() => new ResourceDefinition( 'Product', $namespace ) );
    }
    foreach ( [ 'People', 'two--words', 'two words', '../people' ] as $plural ) {
        $expectFailure( static fn() => new ResourceDefinition( 'Person', 'Maker\\Inventory', [], $plural ) );
    }
    $expectFailure( static fn() => new ResourceDefinition( 'Product', 'Maker\\Inventory', [ 'api' => true ] ) );
} );

$test( 'generates a complete resource and explicit registry entry', static function () use ( $assert, $remove ): void {
    $root = sys_get_temp_dir() . '/makermaker-resource-' . bin2hex( random_bytes( 5 ) );
    mkdir( $root );
    try {
        $definition = new ResourceDefinition( 'Product', 'Maker\\Inventory', [
            'migration' => true,
            'views' => true,
            'factory' => true,
            'tests' => true,
        ] );
        $result = ( new ResourceGenerator( $root ) )->generate( $definition );
        $expected = [
            'app/Models/Product.php',
            'app/Controllers/ProductController.php',
            'app/Http/Fields/ProductFields.php',
            'app/Auth/ProductPolicy.php',
            'database/migrations/create_products_table.php',
            'resources/views/products/index.php',
            'resources/views/products/form.php',
            'tests/Factories/ProductFactory.php',
            'tests/Unit/ProductResourceTest.php',
            'config/makermaker-resources.php',
        ];
        foreach ( $expected as $relative ) {
            $path = $root . '/' . $relative;
            $assert( is_file( $path ), 'Missing generated file: ' . $relative );
            if ( str_ends_with( $relative, '.php' ) ) {
                exec( escapeshellarg( PHP_BINARY ) . ' -l ' . escapeshellarg( $path ) . ' 2>&1', $lintOutput, $lintStatus );
                $assert( $lintStatus === 0, 'Generated PHP failed lint: ' . $relative . "\n" . implode( "\n", $lintOutput ) );
                $lintOutput = [];
            }
        }
        $assert( $result->files === $expected, 'Generated file manifest did not match.' );
        $registry = file_get_contents( $root . '/config/makermaker-resources.php' );
        $assert( str_contains( $registry, "'product' => [") );
        $assert( str_contains( $registry, '\\Maker\\Inventory\\Models\\Product::class' ) );
        $assert( ! str_contains( $registry, 'glob(' ) && ! str_contains( $registry, 'Reflection' ) );
        $policy = file_get_contents( $root . '/app/Auth/ProductPolicy.php' );
        $assert( substr_count( $policy, 'return false;' ) === 4, 'Generated policy must deny by default.' );
    } finally {
        $remove( $root );
    }
} );

$test( 'portable Galaxy resolver supports overrides and rejects missing paths', static function () use ( $assert, $remove ): void {
    $root = sys_get_temp_dir() . '/makermaker-layout-' . bin2hex( random_bytes( 5 ) );
    mkdir( $root . '/custom-content/extensions/inventory-tools', 0755, true );
    mkdir( $root . '/framework/vendor/typerocket/core', 0755, true );
    file_put_contents( $root . '/wp-config.php', '<?php' );
    file_put_contents( $root . '/framework/init.php', '<?php' );
    $definition = new PluginDefinition( 'Inventory', 'inventory-tools', 'Maker\\Inventory' );
    $config = $root . '/galaxy-inventory-tools-config.php';
    file_put_contents( $config, Maker\MakerMaker\Generator\GalaxyContext::config( $definition ) );
    try {
        $command = 'MAKERMAKER_GALAXY_WORDPRESS_ROOT=' . escapeshellarg( $root )
            . ' MAKERMAKER_GALAXY_PLUGIN_PATH=' . escapeshellarg( $root . '/custom-content/extensions/inventory-tools' )
            . ' MAKERMAKER_GALAXY_TYPEROCKET_PATH=' . escapeshellarg( $root . '/framework' )
            . ' ' . escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $config );
        exec( $command . ' 2>&1', $output, $status );
        $assert( $status === 0, 'Portable override resolver failed: ' . implode( "\n", $output ) );
        $relativeCommand = 'cd / && MAKERMAKER_GALAXY_WORDPRESS_ROOT=' . escapeshellarg( $root )
            . ' MAKERMAKER_GALAXY_PLUGIN_PATH=' . escapeshellarg( 'custom-content/extensions/inventory-tools' )
            . ' MAKERMAKER_GALAXY_TYPEROCKET_PATH=' . escapeshellarg( 'framework' )
            . ' ' . escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $config );
        exec( $relativeCommand . ' 2>&1', $relative, $relativeStatus );
        $assert( $relativeStatus === 0, 'Relative overrides did not resolve against WordPress root.' );
        exec( 'MAKERMAKER_GALAXY_WORDPRESS_ROOT=' . escapeshellarg( $root ) . ' ' . escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $config ) . ' 2>&1', $missing, $missingStatus );
        $assert( $missingStatus !== 0 && str_contains( implode( "\n", $missing ), 'set MAKERMAKER_GALAXY_PLUGIN_PATH' ) );
        mkdir( $root . '/content-one/plugins/inventory-tools', 0755, true );
        mkdir( $root . '/content-two/plugins/inventory-tools', 0755, true );
        mkdir( $root . '/content-one/plugins/tr/vendor/typerocket/core', 0755, true );
        mkdir( $root . '/content-two/mu-plugins/tr/vendor/typerocket/core', 0755, true );
        file_put_contents( $root . '/content-one/plugins/tr/init.php', '<?php' );
        file_put_contents( $root . '/content-two/mu-plugins/tr/init.php', '<?php' );
        exec( 'MAKERMAKER_GALAXY_WORDPRESS_ROOT=' . escapeshellarg( $root ) . ' ' . escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $config ) . ' 2>&1', $ambiguous, $ambiguousStatus );
        $assert( $ambiguousStatus !== 0 && str_contains( implode( "\n", $ambiguous ), 'Plugin discovery is ambiguous' ) );
        $siteConfig = $root . '/galaxy-config.php';
        file_put_contents( $siteConfig, GalaxyContext::siteConfig() );
        exec( 'MAKERMAKER_GALAXY_WORDPRESS_ROOT=' . escapeshellarg( $root )
            . ' MAKERMAKER_GALAXY_PLUGIN_PATH=/definitely/irrelevant'
            . ' MAKERMAKER_GALAXY_TYPEROCKET_PATH=' . escapeshellarg( 'framework' )
            . ' ' . escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $siteConfig ) . ' 2>&1', $siteOutput, $siteStatus );
        $assert( $siteStatus === 0, 'Site context should ignore plugin path environment variable.' );
        $outside = sys_get_temp_dir() . '/makermaker-outside-' . bin2hex( random_bytes( 4 ) );
        mkdir( $outside . '/inventory-tools', 0755, true );
        @symlink( $outside, $root . '/escaped-content' );
        exec( 'MAKERMAKER_GALAXY_WORDPRESS_ROOT=' . escapeshellarg( $root )
            . ' MAKERMAKER_GALAXY_TYPEROCKET_PATH=' . escapeshellarg( 'framework' )
            . ' ' . escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $config ) . ' 2>&1', $escapedOutput, $escapedStatus );
        $assert( $escapedStatus !== 0, 'Automatic discovery must not accept symlink-parent paths outside WordPress root.' );
        $remove( $outside );
    } finally {
        $remove( $root );
    }
} );

$test( 'backfills existing plugin Galaxy context safely and idempotently', static function () use ( $assert, $expectFailure, $remove ): void {
    $root = sys_get_temp_dir() . '/makermaker-backfill-' . bin2hex( random_bytes( 5 ) );
    mkdir( $root . '/wp-content/plugins/makermaker', 0755, true );
    $plugins = $root . '/wp-content/plugins';
    $plugin = $plugins . '/makermaker';
    mkdir( $plugin . '/galaxy' );
    copy( __DIR__ . '/../galaxy/galaxy_makermaker', $plugin . '/galaxy/galaxy_makermaker' );
    chmod( $plugin . '/galaxy/galaxy_makermaker', 0755 );
    copy( __DIR__ . '/../galaxy/galaxy-makermaker-config.php', $plugin . '/galaxy/galaxy-makermaker-config.php' );
    chmod( $plugin . '/galaxy/galaxy-makermaker-config.php', 0644 );
    $definition = new PluginDefinition( 'makermaker', 'makermaker', 'Maker\\MakerMaker' );
    try {
        $installer = new GalaxyContextInstaller();
        $previousUmask = umask( 0077 );
        try {
            $result = $installer->install( $definition, $plugins, $root );
        } finally {
            umask( $previousUmask );
        }
        $assert( $result['changed'] === true && is_executable( $result['launcher'] ) );
        $assert( file_get_contents( $result['launcher'] ) === file_get_contents( $plugin . '/galaxy/galaxy_makermaker' ) );
        $assert( file_get_contents( $result['config'] ) === file_get_contents( $plugin . '/galaxy/galaxy-makermaker-config.php' ) );
        $assert( ( fileperms( $result['launcher'] ) & 0777 ) === 0755 );
        $assert( ( fileperms( $result['config'] ) & 0777 ) === 0644 );
        $assert( $installer->install( $definition, $plugins, $root )['changed'] === false );
        chmod( $result['launcher'], 0644 );
        $assert( $installer->install( $definition, $plugins, $root )['changed'] === true, 'Mode repair should report a change.' );
        clearstatcache( true, $result['launcher'] );
        $assert( ( fileperms( $result['launcher'] ) & 0777 ) === 0755, 'Launcher mode was not repaired.' );
        $assert( $installer->install( $definition, $plugins, $root )['changed'] === false );
        unlink( $result['launcher'] );
        file_put_contents( $result['launcher'], 'different' );
        $expectFailure( static fn() => $installer->install( $definition, $plugins, $root ) );
        $assert( file_get_contents( $result['launcher'] ) === 'different', 'Collision was overwritten.' );
        unlink( $result['launcher'] );
        copy( $plugin . '/galaxy/galaxy_makermaker', $result['launcher'] );
        unlink( $result['config'] );
        unlink( $plugin . '/galaxy/galaxy-makermaker-config.php' );
        if ( function_exists( 'symlink' ) && @symlink( __FILE__, $plugin . '/galaxy/galaxy-makermaker-config.php' ) ) {
            $expectFailure( static fn() => $installer->install( $definition, $plugins, $root ) );
        }
    } finally {
        $remove( $root );
    }
} );

$test( 'registers Galaxy command compositionally and idempotently', static function () use ( $assert, $remove ): void {
    foreach ( [
        "<?php\nreturn [\n    'commands' => [\n    ]\n];\n",
        "<?php\nreturn [\n    'commands' => [\n        // Keep this command and comment.\n        Existing\\Command::class,\n    ]\n];\n",
        "<?php\nreturn [\n    'commands' => [Existing\\Command::class]\n];\n",
    ] as $fixture ) {
        $root = sys_get_temp_dir() . '/makermaker-galaxy-' . bin2hex( random_bytes( 5 ) );
        mkdir( $root . '/config', 0755, true );
        $config = $root . '/config/galaxy.php';
        file_put_contents( $config, $fixture );
        try {
            $registrar = new GalaxyRegistrar();
            $assert( $registrar->register( $root ) === true );
            $first = file_get_contents( $config );
            $assert( substr_count( $first, 'MakerResourceGalaxyCommand::class' ) === 1 );
            if ( str_contains( $fixture, 'Existing' ) ) {
                $assert( str_contains( $first, 'Existing\\Command::class' ), 'Existing Galaxy command was not preserved.' );
            }
            if ( str_contains( $fixture, 'Keep this' ) ) {
                $assert( str_contains( $first, '// Keep this command and comment.' ), 'Existing Galaxy comment was not preserved.' );
            }
            $assert( $registrar->register( $root ) === false );
            $assert( file_get_contents( $config ) === $first, 'Idempotent registration changed config.' );
            exec( escapeshellarg( PHP_BINARY ) . ' -l ' . escapeshellarg( $config ) . ' 2>&1', $output, $status );
            $assert( $status === 0, 'Registered Galaxy config failed lint.' );
        } finally {
            $remove( $root );
        }
    }
} );

$test( 'resource context retains explicit global arguments', static function () use ( $assert ): void {
    $context = ( new ResourceGeneratorFactory() )->resolveContext( 'inventory-tools', 'Maker\\InventoryTools' );
    $assert( $context === [ 'slug' => 'inventory-tools', 'namespace' => 'Maker\\InventoryTools' ] );
} );

$test( 'Galaxy command normalizes slash-separated namespaces', static function () use ( $assert ): void {
    $source = file_get_contents( __DIR__ . '/../app/Cli/MakerResourceGalaxyCommand.php' );
    $assert( str_contains( $source, "str_replace( '/', '\\\\'" ) );
    $assert( str_contains( $source, "getOption( 'namespace' )" ) );
    $assert( str_contains( $source, "getOption( 'plural' )" ) );
} );

$test( 'appends resources without overwrite and cleans failed staging', static function () use ( $assert, $expectFailure, $remove ): void {
    $root = sys_get_temp_dir() . '/makermaker-resource-' . bin2hex( random_bytes( 5 ) );
    mkdir( $root );
    try {
        $generator = new ResourceGenerator( $root );
        $generator->generate( new ResourceDefinition( 'Product', 'Maker\\Inventory' ) );
        $product = file_get_contents( $root . '/app/Models/Product.php' );
        $registry = file_get_contents( $root . '/config/makermaker-resources.php' );
        $expectFailure( static fn() => $generator->generate( new ResourceDefinition( 'Product', 'Maker\\Inventory' ) ) );
        $assert( file_get_contents( $root . '/app/Models/Product.php' ) === $product, 'Collision changed an existing resource.' );
        $assert( file_get_contents( $root . '/config/makermaker-resources.php' ) === $registry, 'Collision changed the registry.' );

        file_put_contents( $root . '/app/Models/Order.php', 'existing' );
        $expectFailure( static fn() => $generator->generate( new ResourceDefinition( 'Order', 'Maker\\Inventory' ) ) );
        $assert( file_get_contents( $root . '/app/Models/Order.php' ) === 'existing' );
        $assert( ! is_file( $root . '/app/Controllers/OrderController.php' ) );

        if ( function_exists( 'symlink' ) && @symlink( sys_get_temp_dir(), $root . '/database' ) ) {
            $expectFailure( static fn() => $generator->generate( new ResourceDefinition( 'Invoice', 'Maker\\Inventory', [ 'migration' => true ] ) ) );
            $assert( ! is_file( $root . '/app/Models/Invoice.php' ), 'Symbolic-link path failure published partial files.' );
        }
        $assert( glob( $root . '/.makermaker-resource-*' ) === [], 'Failed staging directory remained.' );
        $assert( ! is_file( $root . '/.makermaker-resource.lock' ), 'Generation lock remained.' );
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
