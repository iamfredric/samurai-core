<?php

namespace Tests\Unit\Hooks\HookConfiguratorTest;

use Boil\Hooks\HookConfigurator;
use Boil\Support\Wordpress\WpHelper;

it('configures the hooks', function () {
    $hook = new HookConfigurator($this->app);

    $helper = WpHelper::fake([]);

    $hook->action('test-action', 'callable', 11, 2);

    $hook->filter('test-filter', 'other-callable', 11, 2);

    $helper->assertCalled('add_action', function ($hook, $callable, $priority, $acceptedArgs) {
        return $hook === 'test-action'
            && $priority === 11
            && $acceptedArgs === 2;
    });

    $helper->assertCalled('add_filter', function ($hook, $callable, $priority, $acceptedArgs) {
        return $hook === 'test-filter'
            && $priority === 11
            && $acceptedArgs === 2;
    });
});
