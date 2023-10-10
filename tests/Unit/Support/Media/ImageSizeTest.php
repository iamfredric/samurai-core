<?php

namespace Tests\Unit\Support\Media\ImageSizeTest;

use Samurai\Support\Media\ImageSize;

it('creates an image size', function () {
    $size = new ImageSize('me-size', 100, 200, true);

    expect($size)
        ->toBeInstanceOf(ImageSize::class)
        ->name->toBe('me-size')
        ->width->toBe(100)
        ->height->toBe(200)
        ->crop->toBeTrue();
});
