<?php

namespace Unit;

use Samurai\Application;

it('displays the version', function () {
    $app = new Application(__DIR__);

    expect($app->version())->toEqual('0.0.1');
});

it('displays the base path', function () {
    $app = new Application(__DIR__);

    expect($app->basePath())->toEqual(__DIR__)
        ->and($app->basePath('/test/more-things'))->toEqual(__DIR__.'/test/more-things');
});

it('joins paths', function () {
    $app = new Application(__DIR__);

    expect($app->joinPaths(__DIR__, 'test'))->toEqual(__DIR__.'/test');
});

it('displays the bootstrap path', function () {
    $app = new Application(__DIR__);

    expect($app->bootstrapPath())->toEqual('');
})->todo();

it('displays the config path', function () {
    $app = new Application(__DIR__);

    expect($app->configPath())->toEqual($app->basePath('config'));
});

it('displays the database path', function () {
    $app = new Application(__DIR__);

    expect($app->databasePath())->toEqual($app->basePath(''));
})->todo();

it('displays the language path', function () {
    $app = new Application(__DIR__);

    expect($app->langPath())->toEqual($app->basePath(''));
})->todo();

it('displays the public path', function () {
    $app = new Application(__DIR__);

    expect($app->publicPath())->toEqual($app->basePath('public'));
});

it('displays the resources path', function () {
    $app = new Application(__DIR__);

    expect($app->resourcePath())->toEqual($app->basePath('resources'));
});

it('displays the storage path', function () {
    $app = new Application(__DIR__);

    expect($app->storagePath())->toEqual($app->basePath('resources'));
})->todo();

it('gets current environment', function () {
    $app = new Application(__DIR__);

    expect($app->environment())->toBe('testing');
})->todo();

it('checks current environment', function () {
    $app = new Application(__DIR__);

    expect($app->environment('testing'))->toBeTrue()
        ->and($app->environment('production'))->toBeFalse();
})->todo();

it('does not run in console', function () {
    $app = new Application(__DIR__);

    expect($app->runningInConsole())->toBeFalse();
});

it('does run in unit test', function () {
    $app = new Application(__DIR__);

    $app->detectEnvironment(fn () => 'testing');
    expect($app->runningUnitTests())->toBeTrue();
});

it('has debug mode enabled', function () {
    $app = new Application(__DIR__);

    expect($app->hasDebugModeEnabled())->toBeFalse();

    define('WP_DEBUG', true);
    expect($app->hasDebugModeEnabled())->toBeTrue();
});

it('detects maintenance mode', function () {
    $app = new Application(__DIR__);

    define('ABSPATH', __DIR__);

    file_put_contents($app->baseInstallationPath('.maintenance'), '');

    expect($app->maintenanceMode()->active())->toBeTrue();

    if (file_exists($app->baseInstallationPath('.maintenance'))) {
        unlink($app->baseInstallationPath('.maintenance'));
    }

    expect($app->maintenanceMode()->active())->toBeFalse();
});
