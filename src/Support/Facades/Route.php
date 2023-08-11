<?php

namespace Boil\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Boil\Routing\Routes register(string $name, string|array|callable $callback)
 */
class Route extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'router';
    }
}