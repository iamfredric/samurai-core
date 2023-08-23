<?php

namespace Tests\Unit\Support\Wordpress\LinkTest;

use Boil\Support\Wordpress\Link;

it('can be an link', function () {
    $link = new Link([
        'url' => 'https://google.com',
        'title' => 'Google',
        'target' => '_blank',
    ]);

    expect($link->exists())->toBeTrue();
    expect($link->get('url'))->toBe('https://google.com');
    expect($link->get('title'))->toBe('Google');
    expect($link->get('target'))->toBe('_blank');
    expect($link->render())->toBe('<a href="https://google.com" target="_blank" rel="noopener">Google</a>');
    expect($link->render('mt-12'))->toBe('<a href="https://google.com" target="_blank" rel="noopener" class="mt-12">Google</a>');
    expect($link->render(['class' => 'mx-4', 'aria-label' => 'zup']))->toBe('<a href="https://google.com" target="_blank" rel="noopener" class="mx-4" aria-label="zup">Google</a>');
});
