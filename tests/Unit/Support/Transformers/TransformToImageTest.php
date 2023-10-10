<?php

namespace Tests\Unit\Support\Transformers\TransformToImageTest;

use Samurai\Support\Transformers\TransformToImage;

it('transforms an array to an image if it has the correct keys', function () {
    $transformer = new TransformToImage([
        'sizes' => [],
        'width' => 100,
        'height' => 100,
    ]);

    expect($transformer->transform())->toBeInstanceOf(\Samurai\Support\Wordpress\Image::class);

    $transformer = new TransformToImage([
        'hello' => 'mate',
    ]);

    expect($transformer->transform())->toBe([
        'hello' => 'mate',
    ]);
});
