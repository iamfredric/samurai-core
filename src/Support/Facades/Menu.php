<?php

namespace Samurai\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $name, string $label)
 * @method static string|null render(string $slug, array $args = [])
 */
class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Samurai\Menu\MenuConfigurator::class;
    }
}
