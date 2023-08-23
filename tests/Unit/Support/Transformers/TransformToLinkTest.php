<?php

namespace Tests\Unit\Support\Transformers\TransformToLinkTest;

use Boil\Support\Transformers\TransformToLink;

it('transforms an array to a link if it has the correct keys', function () {
    $transformer = new TransformToLink([
        'url' => 'https://google.com',
        'title' => 'Google',
        'target' => '_blank',
    ]);

    expect($transformer->transform())->toBeInstanceOf(\Boil\Support\Wordpress\Link::class);

    $transformer = new TransformToLink([
        'hello' => 'mate'
    ]);

    expect($transformer->transform())->toBe([
        'hello' => 'mate'
    ]);
});
