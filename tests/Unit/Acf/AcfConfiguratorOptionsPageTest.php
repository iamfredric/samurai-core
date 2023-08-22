<?php

namespace Unit\Acf;

use Boil\Acf\AcfConfiguratorOptionsPage;

it('can be casted to an array', function () {
    $optionsPage = new AcfConfiguratorOptionsPage(
        id: 'test-options-page',
        title: 'Test Options Page',
        menuTitle: 'Test option menu title',
        parentSlug: 'example.php',
        position: 123,
        capability: 'test_cases',
        iconUrl: 'test-icon',
        redirect: false,
        autoload: false,
        updateButtonLabel: 'Update this thing',
        updateMessage: 'You have updated, great!',
        slug: 'test-slug-for-options-page',
    );

    expect($optionsPage->toArray())
        ->toBe([
            'page_title' => 'Test Options Page',
            'menu_title' => 'Test option menu title',
            'menu_slug' => 'test-slug-for-options-page',
            'capability' => 'test_cases',
            'position' => 123,
            'parent_slug' => 'example.php',
            'icon_url' => 'test-icon',
            'redirect' => false,
            'post_id' => 'test-options-page',
            'autoload' => false,
            'update_button' => 'Update this thing',
            'updated_message' => 'You have updated, great!',
        ]);
});

it('can be shared via constructor', function () {
    $optionsPage = new AcfConfiguratorOptionsPage(
        id: 'test-options-page',
        title: 'Test Options Page',
        menuTitle: 'Test option menu title',
        parentSlug: 'example.php',
        position: 123,
        capability: 'test_cases',
        iconUrl: 'test-icon',
        redirect: false,
        autoload: false,
        updateButtonLabel: 'Update this thing',
        updateMessage: 'You have updated, great!',
        slug: 'test-slug-for-options-page',
        share: fn () => 'test',
    );

    expect($optionsPage->toArray())
        ->toBe([
            'page_title' => 'Test Options Page',
            'menu_title' => 'Test option menu title',
            'menu_slug' => 'test-slug-for-options-page',
            'capability' => 'test_cases',
            'position' => 123,
            'parent_slug' => 'example.php',
            'icon_url' => 'test-icon',
            'redirect' => false,
            'post_id' => 'test-options-page',
            'autoload' => true,
            'update_button' => 'Update this thing',
            'updated_message' => 'You have updated, great!',
        ]);
});

it('can be shared via method call', function () {
    $optionsPage = new AcfConfiguratorOptionsPage(
        id: 'test-options-page',
        title: 'Test Options Page',
        menuTitle: 'Test option menu title',
        parentSlug: 'example.php',
        position: 123,
        capability: 'test_cases',
        iconUrl: 'test-icon',
        redirect: false,
        autoload: false,
        updateButtonLabel: 'Update this thing',
        updateMessage: 'You have updated, great!',
        slug: 'test-slug-for-options-page',
    );

    $optionsPage->share(fn () => 'test');

    expect($optionsPage->toArray())
        ->toBe([
            'page_title' => 'Test Options Page',
            'menu_title' => 'Test option menu title',
            'menu_slug' => 'test-slug-for-options-page',
            'capability' => 'test_cases',
            'position' => 123,
            'parent_slug' => 'example.php',
            'icon_url' => 'test-icon',
            'redirect' => false,
            'post_id' => 'test-options-page',
            'autoload' => true,
            'update_button' => 'Update this thing',
            'updated_message' => 'You have updated, great!',
        ]);
});
