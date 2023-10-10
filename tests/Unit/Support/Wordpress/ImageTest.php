<?php

namespace Tests\Unit\Support\Wordpress\ImageTest;

use Boil\Support\Wordpress\Image;
use Boil\Support\Wordpress\WpHelper;

it('can be an image', function () {
    $helper = WpHelper::fake([
        'wp_get_attachment_image_srcset' => function ($attachment_id, $size = 'medium', $image_meta = null) {
            if ($size == 'test-size') {
                return 'https://example.com/test-image-100x100.jpg 100w, https://example.com/test-image-200x200.jpg 200w';
            }

            return false;
        },
        'add_action' => fn () => true,
    ]);

    $image = new Image([
        'id' => 999,
        'title' => 'Test image',
        'url' => 'https://example.com/test-image.jpg',
        'alt' => 'A test image',
        'description' => 'An image for a testcase',
        'caption' => 'Look at the goofy test dummy',
        'sizes' => [
            'test-size' => 'https://example.com/test-image-100x100.jpg',
            'test-size-width' => 200,
            'test-size-height' => 100,
        ],
        'width' => 1080,
        'height' => 720,
    ]);

    expect($image->id())->toBe(999)
        ->and($image->identifier())->toBe('media-item-999')
        ->and($image->title())->toBe('Test image')
        ->and($image->url())->toBe('https://example.com/test-image.jpg')
        ->and($image->url('test-size'))->toBe('https://example.com/test-image-100x100.jpg')
        ->and($image->url('test-size-missing'))->toBe('https://example.com/test-image.jpg')
        ->and($image->getWidth())->toBe(1080)
        ->and($image->getWidth('test-size'))->toBe(200)
        ->and($image->getWidth('non-existant-image-size'))->toBe(1080)
        ->and($image->getHeight())->toBe(720)
        ->and($image->getHeight('test-size'))->toBe(100)
        ->and($image->getHeight('test-size-missing'))->toBe(720)
        ->and($image->alt())->toBe('A test image')
        ->and($image->description())->toBe('An image for a testcase')
        ->and($image->caption())->toBe('Look at the goofy test dummy')
        ->and($image->exists())->toBe(true)
        ->and($image->toArray())->toBe([
            'id' => 999,
            'title' => 'Test image',
            'url' => 'https://example.com/test-image.jpg',
            'alt' => 'A test image',
            'description' => 'An image for a testcase',
            'caption' => 'Look at the goofy test dummy',
            'sizes' => [
                'test-size' => 'https://example.com/test-image-100x100.jpg',
                'test-size-width' => 200,
                'test-size-height' => 100,
            ],
            'width' => 1080,
            'height' => 720,
        ])
        ->and($image->toJson())->toBe('{"id":999,"title":"Test image","url":"https:\/\/example.com\/test-image.jpg","alt":"A test image","description":"An image for a testcase","caption":"Look at the goofy test dummy","sizes":{"test-size":"https:\/\/example.com\/test-image-100x100.jpg","test-size-width":200,"test-size-height":100},"width":1080,"height":720}')
        ->and($image->render())->toBe('<img width="1080" height="720" src="https://example.com/test-image.jpg" loading="lazy" alt="A test image" title="Test image" decoding="async">')
        ->and($image->render('test-size'))
        ->toBe('<img width="200" height="100" src="https://example.com/test-image-100x100.jpg" loading="lazy" alt="A test image" title="Test image" decoding="async" srcset="https://example.com/test-image-100x100.jpg 100w, https://example.com/test-image-200x200.jpg 200w" sizes="100vw">');

    expect($image->styles())->toBe('id=media-item-999');

    $helper->assertCalled('add_action', fn ($action) => $action === 'wp_head');
});
