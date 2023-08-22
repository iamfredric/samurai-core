<?php

namespace Unit\Acf;

use Boil\Acf\AcfConfigurator;
use Boil\Acf\AcfConfiguratorOptionsPage;
use Boil\Acf\AcfOptionsPage;
use Boil\Application;
use Illuminate\Support\Collection;

test('add options page', function () {
    $app = new Application(__DIR__);
    $app['config'] = new Collection();

    $config = new AcfConfigurator($app);
    $config->addOptionsPage(
        id: 'me-options-page',
        title: 'Me Options Page',
    );

    $optionsPages = invade($config)->optionsPages;
    expect($optionsPages)->toHaveCount(1);
});

it('can add option pages via config', function () {
    $app = new Application(__DIR__);
    $app['config'] = new Collection([
        'features' => [
            'acf' => [
                'options_pages' => [
                    TestOptionPage::class
                ],
            ]
        ]
    ]);

    $config = new AcfConfigurator($app);
    $config->addOptionsPage(
        id: 'me-options-page',
        title: 'Me Options Page',
    );

    $optionsPages = invade($config)->optionsPages;

    expect($optionsPages)->toHaveCount(1)
        ->and($optionsPages[0])
        ->toBeInstanceOf(AcfConfiguratorOptionsPage::class)
        ->and($optionsPages[0]->toArray())->toBe([
            "page_title" => "Me Options Page",
            "menu_title" => "Me Options Page",
            "menu_slug" => "me-options-page",
            "capability" => "edit_posts",
            "position" => null,
            "parent_slug" => null,
            "icon_url" => "",
            "redirect" => false,
            "post_id" => "me-options-page",
            "autoload" => false,
            "update_button" => null,
            "updated_message" => null,
        ]);
});

test('addGroup', function () {
    // addGroup
})->todo();

test('addGroups', function () {
    // addGroups
})->todo();

test('boot', function () {
    // boot
})->todo();

class TestOptionPage extends AcfOptionsPage
{
    public function id(): string
    {
        return 'test-from-config';
    }

    public function title(): string
    {
        return 'Test From Config';
    }
}
