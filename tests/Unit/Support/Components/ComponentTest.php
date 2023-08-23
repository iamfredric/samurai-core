<?php

namespace Tests\Unit\Support\Components\ComponentTest;

use Boil\Support\Components\Component;

it('can be a component', function () {
    $component = new Component([
        'acf_fc_layout' => 'test-component',
        'test' => 'wow',
    ]);

    expect($component->data('test'))->toBe('wow');

    expect($component->data())->toBe([
        'test' => 'wow',
    ]);

    expect($component->attributes())->toBe([
        'test' => 'wow',
        'nextComponent' => null,
        'prevComponent' => null,
        'currentComponent' => $component->hash(),
    ]);

    expect($component->view())->toBe('test-component');

    expect($component->toArray())->toBe([
        'view' => 'test-component',
        'data' => [
            'test' => 'wow',
        ]
    ]);

    expect($component->hash())->toBe(md5('test-component'));

    $component->setPreviousComponent('wow');
    $component->setNextComponent('oy');

    expect($component->attributes())->toBe([
        'test' => 'wow',
        'nextComponent' => 'oy',
        'prevComponent' => 'wow',
        'currentComponent' => $component->hash(),
    ]);
});
