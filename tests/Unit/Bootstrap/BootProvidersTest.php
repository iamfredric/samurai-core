<?php

namespace Tests\Unit\Bootstrap\BootProvidersTest;

use Samurai\Acf\Bootstrap\BootProviders;

it('boots providers', function () {
    expect($this->app->isBooted())->toBeFalse();

    (new BootProviders())->bootstrap($this->app);

    expect($this->app->isBooted())->toBeTrue();
});
