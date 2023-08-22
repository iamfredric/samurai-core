<?php

namespace Tests\Unit\Menu\MenuConfiguratorTest;

use Boil\Menu\MenuConfigurator;
use Boil\Support\Wordpress\WpHelper;

it('can be registered', function () {
    $helper = WpHelper::fake();

    $configurator = new MenuConfigurator($this->app);

    $configurator->register('test-menu', 'Test menu');

    $helper->assertCalled('register_nav_menu', fn (string $location, string $description) => $location === 'test-menu' && $description === 'Test menu');
});

it('can be rendered', function () {
    $helper = WpHelper::fake();

    $configurator = new MenuConfigurator($this->app);

    $configurator->render('test-menu');

    $helper->assertCalled('wp_nav_menu', fn (array $args) => $args['theme_location'] === 'test-menu' && $args['container'] === null && $args['items_wrap'] === '%3$s');
});
