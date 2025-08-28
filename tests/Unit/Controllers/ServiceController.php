<?php

use MakerMaker\Controllers\ServiceController;
use Brain\Monkey\Functions;

describe('ServiceController', function () {
    beforeEach(function () {
        $this->controller = new ServiceController();
    });

    it('can be instantiated', function () {
        expect($this->controller)->toBeInstanceOf(ServiceController::class);
    });

    it('displays database items on index', function () {
        // Mock WordPress functions that might be used
        Functions\expect('get_posts')
            ->once()
            ->andReturn([
                (object) ['ID' => 1, 'post_title' => 'Item 1'],
                (object) ['ID' => 2, 'post_title' => 'Item 2']
            ]);

        // Test your index method
        $result = $this->controller->index();
        
        expect($result)->toBeString();
    });
})->group('unit');