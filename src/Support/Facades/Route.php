<?php

namespace Boil\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Boil\Routing\Template register(string $name, string|array|callable $callback)
 * @method static \Boil\Routing\Template view(string $name, string $view, array $options = [])
 * @method static \Boil\Routing\Template template(string $key, string $name, string|array|callable $callback, array $options = [])
 */
class Route extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'router';
    }
}
