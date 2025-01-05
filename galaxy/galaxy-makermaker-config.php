<?php
$typerocket = '/var/www/html/compasscareerscanada/public/wp-content/plugins/typerocket-pro-v6/typerocket';
$overrides = '/var/www/html/compasscareerscanada/public/wp-content/plugins/makermaker';
define('TYPEROCKET_GALAXY_MAKE_NAMESPACE', 'MakerMaker');
define('TYPEROCKET_GALAXY_PATH', $typerocket);
define('TYPEROCKET_CORE_CONFIG_PATH', $typerocket . '/config' );
define('TYPEROCKET_ROOT_WP', '/var/www/html/compasscareerscanada/public/');

define('TYPEROCKET_APP_ROOT_PATH', $overrides);
define('TYPEROCKET_ALT_PATH', $overrides);