<?php

namespace Samurai\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Samurai\Hooks\HookConfigurator;

/**
 * @method static void action(string $name, callable|string|array $callable, int $priority = 10, int $acceptedArgs = 1)
 * @method static void filter(string $name, callable|string|array $callable, int $priority = 10, int $acceptedArgs = 1)
 *
 * @see HookConfigurator
 */
class Hook extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HookConfigurator::class;
    }
}
