<?php

namespace Boil\Support\Facades;

use Boil\Acf\Gutenberg\GutenbergConfigurator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static GutenbergConfigurator block(string $className)
 * @see \Boil\Acf\Gutenberg\GutenbergConfigurator
 */
class Gutenberg extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GutenbergConfigurator::class;
    }
}
