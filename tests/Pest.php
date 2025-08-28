<?php

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

// Use Mockery integration
uses(MockeryPHPUnitIntegration::class)->in('Unit', 'Integration', 'Feature');

// Global setup for all tests
uses()->beforeEach(function () {
    Monkey\setUp();
})->afterEach(function () {
    Monkey\tearDown();
    Mockery::close();
})->in('Unit', 'Integration', 'Feature');

// Custom expectations for WordPress functions
expect()->extend('toCallWordPressFunction', function (string $function) {
    Brain\Monkey\Functions\expect($function);
    return $this;
});

// Helper to mock WordPress actions
expect()->extend('toHaveWordPressAction', function (string $action) {
    Brain\Monkey\Actions\expectAdded($action);
    return $this;
});

// Helper to mock WordPress filters  
expect()->extend('toHaveWordPressFilter', function (string $filter) {
    Brain\Monkey\Filters\expectAdded($filter);
    return $this;
});