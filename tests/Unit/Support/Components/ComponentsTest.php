<?php

namespace Tests\Unit\Support\Components\ComponentsTest;

use Samurai\Support\Components\Component;
use Samurai\Support\Components\Components;

it('determines if components exists')
    ->expect(new Components([]))->exists()->toBeFalse();

it('retrieves all components', function () {
    $components = new Components([[
        'acf_fc_layout' => 'test-component',
    ]]);

    expect($components->exists())->toBeTrue();
    expect($allComponents = $components->all())
        ->toHaveCount(1)
        ->toBeArray();

    expect($allComponents[0])->toBeInstanceOf(Component::class);

    expect($components->toArray())->toBe([[
        'view' => 'test-component',
        'data' => [],
    ]]);

    expect($components->toJson())->toBe('[{"view":"test-component","data":[]}]');

    expect($components->count())->toBe(1);
});
