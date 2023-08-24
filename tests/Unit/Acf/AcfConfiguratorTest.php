<?php

namespace Tests\Support\Unit\Acf;

use Boil\Acf\AcfConfigurator;
use Boil\Acf\AcfOptionsPage;
use Boil\Acf\Group;
use Boil\Support\Concerns\ConfigPath;
use Boil\Support\Wordpress\WpHelper;

test('add options page', function () {
    $config = new AcfConfigurator(new ConfigPath([]));

    $config->addOptionsPage(
        id: 'me-options-page',
        title: 'Me Options Page',
    );

    $optionsPages = invade($config)->optionsPages;
    expect($optionsPages)->toHaveCount(1);
});

it('can add option pages via config', function () {
    $config = new AcfConfigurator(new ConfigPath([]), [], [TestOptionPage::class]);

    $config->addOptionsPage(
        id: 'me-options-page',
        title: 'Me Options Page',
    );

    $optionsPages = invade($config)->optionsPages;

    expect($optionsPages)->toHaveCount(2)
        ->and($optionsPages[0])
        ->toBeString()
        ->and($optionsPages[1]->toArray())->toBe([
            'page_title' => 'Me Options Page',
            'menu_title' => 'Me Options Page',
            'menu_slug' => 'me-options-page',
            'capability' => 'edit_posts',
            'position' => null,
            'parent_slug' => null,
            'icon_url' => '',
            'redirect' => false,
            'post_id' => 'me-options-page',
            'autoload' => false,
            'update_button' => null,
            'updated_message' => null,
        ]);
});

it('can add one or many groups', function () {
    $config = new AcfConfigurator(new ConfigPath());

    $config->addGroup('me-group');

    $config->addGroups(['one-group', 'another-group']);

    expect(invade($config)->groups)->toBe(['me-group', 'one-group', 'another-group']);
});

test('can register all configurations', function () {
    $helper = WpHelper::fake([
        'add_action' => fn ($hook, $callable) => $hook === 'acf/init' && $callable(),
    ]);

    $config = new AcfConfigurator(new ConfigPath([]), [
        ExampleGroupTwo::class,
    ], [
        TestOptionPage::class,
    ]);

    $config->addGroup(ExampleGroup::class);
    $config->addOptionsPage('identifier', 'title');

    $config->boot();

    $helper->assertCalled('add_action', fn ($hook) => $hook === 'acf/init');

    $helper->assertCalled('acf_add_options_page', fn ($attributes) => $attributes['page_title'] === 'title');
    $helper->assertCalled('acf_add_options_page', fn ($attributes) => $attributes['page_title'] === 'Test From Config');
    $helper->assertCalled('register_extended_field_group', fn ($attributes) => $attributes['title'] === 'Example group');
    $helper->assertCalled('register_extended_field_group', fn ($attributes) => $attributes['title'] === 'Example group two');
});

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

class ExampleGroup extends Group
{
    public function title(): string
    {
        return 'Example group';
    }

    public function key(): string
    {
        return 'example_group';
    }

    public function fields(): array
    {
        return [];
    }

    public function location(): array
    {
        return [];
    }
}

class ExampleGroupTwo extends Group
{
    public function title(): string
    {
        return 'Example group two';
    }

    public function key(): string
    {
        return 'example_group_two';
    }

    public function fields(): array
    {
        return [];
    }

    public function location(): array
    {
        return [];
    }
}
