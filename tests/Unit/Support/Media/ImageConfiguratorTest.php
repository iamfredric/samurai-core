<?php

namespace Tests\Unit\Support\Media\ImageConfiguratorTest;

use Samurai\Support\Concerns\ConfigPath;
use Samurai\Support\Media\ImageConfigurator;
use Samurai\Support\Wordpress\WpHelper;

it('registers image support and sizes', function () {
    $helper = WpHelper::fake([
        'add_action' => function ($action, $callback) {
            if ($action === 'init') {
                $callback();
            }
        },
        'add_theme_support' => fn ($type, $types) => null,
        'add_image_size' => fn ($name, $width, $height, $crop) => null,
    ]);

    $config = new ImageConfigurator(new ConfigPath([]));

    $config->support('post', 'page');

    $imageOne = $config->register('test', 100, 100)->crop();
    $imageTwo = $config->register('test2', 200, 300)->scale();

    $config->boot();

    $helper->assertCalled('add_theme_support', function ($type, $types) {
        return $type === 'post-thumbnails'
            && $types === ['post', 'page'];
    });

    $helper->assertCalled('add_image_size', function ($name, $width, $height, $crop) use ($imageOne) {
        return $name === $imageOne->name
            && $width === $imageOne->width
            && $height === $imageOne->height
            && $crop === $imageOne->crop;
    });

    $helper->assertCalled('add_image_size', function ($name, $width, $height, $crop) use ($imageTwo) {
        return $name === $imageTwo->name
            && $width === $imageTwo->width
            && $height === $imageTwo->height
            && $crop === $imageTwo->crop;
    });
});
