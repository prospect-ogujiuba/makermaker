<?php

it('can run basic arithmetic', function () {
    expect(2 + 2)->toBe(4);
});

it('can test string operations', function () {
    expect('Hello World')->toContain('World');
});

test('basic PHP functionality', function () {
    $array = ['a', 'b', 'c'];
    expect(count($array))->toBe(3);
});