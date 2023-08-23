<?php

namespace Tests\Unit\Support\Translations;

use Boil\Support\Translations\Translator;

it('translates a string', function () {
    $translator = new Translator();

    $translator->setTranslation('sv_SE', 'Hello world', 'Hej världen');
    $translator->setTranslation('sv_SE', 'Hello :name', 'Hej :name');

    $translator->setLocale('sv_SE');
    expect($translator->translate('Hello world'))->toBe('Hej världen');

    expect($translator->translate('Hello :name', ['name' => 'Jane']))->toBe('Hej Jane');
});

it('loads json file and php file of translations by given path', function () {
    $translator = new Translator();

    $translator->loadFromPath(dirname(__DIR__, 3) . '/boilerplate/lang');

    $translator->setLocale('sv');

    expect($translator->translate('Hello my friend!'))->toBe('Hej kompis!')
        ->and($translator->translate('What is up :name', ['name' => 'Jane']))->toBe('Hur är läget Jane')
        ->and($translator->translate('How many :attributes do you want to create?', ['attributes' => 'languages']))
        ->toBe('Hur många språk vill du skapa?');

    expect($translator('Hello my friend!'))->toBe('Hej kompis!');
});
