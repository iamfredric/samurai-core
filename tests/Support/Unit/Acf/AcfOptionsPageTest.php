<?php

namespace tests\Support\Unit\Acf;

it('has public method id', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->id())->toBe('test');
});

it('has public method title', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->title())->toBe('Test');
});

it('has public method menuTitle', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->menuTitle())->toBe('Test menu title');
});

it('has public method parentSlug', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->parentSlug())->toBe('example.php');
});

it('has public method capability', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->capability())->toBe('edit_posts');
});

it('has public method iconUrl', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->iconUrl())->toBeEmpty();
});

it('has public method redirect', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->redirect())->toBeFalse();
});

it('has public method autoload', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->autoload())->toBeFalse();
});

it('has public method updateButtonLabel', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->updateButtonLabel())->toBeNull();
});

it('has public method updateMessage', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->updateMessage())->toBeNull();
});

it('has public method slug', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->slug())->toBeNull();
});

it('has public method position', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->position())->toBeNull();
});

it('has public method share', function () {
    $optionsPage = new TestOptionsPage();

    expect($optionsPage->share())->toBeNull();
});

class TestOptionsPage extends \Boil\Acf\AcfOptionsPage
{
    public function id(): string
    {
        return 'test';
    }

    public function title(): string
    {
        return 'Test';
    }

    public function menuTitle(): ?string
    {
        return 'Test menu title';
    }

    public function parentSlug(): ?string
    {
        return 'example.php';
    }
}
