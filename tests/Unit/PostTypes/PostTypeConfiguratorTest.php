<?php

namespace Tests\Unit\PostTypes\PostTypeConfiguratorTest;

use Boil\PostTypes\PostType;
use Boil\PostTypes\PostTypeConfigurator;
use Boil\Support\Concerns\ConfigPath;
use Boil\Support\Wordpress\WpHelper;

it('can register post types', function () {
    $faker = WpHelper::fake([
        'add_action' => function ($hook, $callable) {
            if ($hook === 'init' && $callable instanceof \Closure) {
                $callable();
            }
        },
    ]);

    $configurator = new PostTypeConfigurator(new ConfigPath([]), [
        new PostType(id: 'test-post-type'),
    ]);

    expect($otherPostType = $configurator->register('other-post-type'))
        ->toBeInstanceOf(PostType::class)
        ->toHaveProperty('id', 'other-post-type');

    $otherPostType->taxonomy($taxonomy = $configurator->taxonomy('test-taxonomy'))->supports('images');

    expect($taxonomy)->toBeInstanceOf(\Boil\PostTypes\Taxonomy::class);

    $configurator->boot();

    $faker->assertCalled('add_action', fn ($hook, $callable) => $hook === 'init' && $callable instanceof \Closure);

    $faker->assertCalled('register_post_type', fn ($id, $args) => $id === 'other-post-type');
    $faker->assertCalled('register_post_type', fn ($id, $args) => $id === 'test-post-type');

    $faker->assertCalled('register_taxonomy');
});
