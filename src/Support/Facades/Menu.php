<?php

namespace Boil\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Boil\Menu\MenuConfigurator::class;
    }
}
