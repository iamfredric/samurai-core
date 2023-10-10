<?php

namespace Samurai\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Samurai\Acf\Gutenberg\GutenbergConfigurator;

/**
 * @method static GutenbergConfigurator block(string $className)
 *
 * @see \Samurai\Acf\Gutenberg\GutenbergConfigurator
 */
class Gutenberg extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GutenbergConfigurator::class;
    }
}
