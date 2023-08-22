<?php

namespace Boil\Support\Facades;

use Boil\PostTypes\PostTypeConfigurator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Boil\PostTypes\PostType register(PostType|string $type)
 * @method static \Boil\PostTypes\Taxonomy taxonomy(string $id)
 * @see PostTypeConfigurator
 */
class PostType extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PostTypeConfigurator::class;
    }
}
