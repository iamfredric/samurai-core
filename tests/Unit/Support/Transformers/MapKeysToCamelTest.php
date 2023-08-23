<?php

namespace Tests\Unit\Support\Transformers\MapKeysToCamelTest;

use Boil\Support\Transformers\MapKeysToCamel;

it('transforms attribute keys to camel case', function () {
    $transformer = new MapKeysToCamel([
        'hello-world' => 'What is up',
        'up_is_down' => 'What is down',
    ]);

    expect($transformer->transform())->toBe([
        'helloWorld' => 'What is up',
        'upIsDown' => 'What is down',
    ]);
});
