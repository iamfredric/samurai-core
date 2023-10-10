<?php

namespace Samurai\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Samurai\PostTypes\PostTypeConfigurator;

/**
 * @method static \Samurai\PostTypes\PostType register(PostType|string $type)
 * @method static \Samurai\PostTypes\Taxonomy taxonomy(string $id)
 *
 * @see PostTypeConfigurator
 */
class PostType extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PostTypeConfigurator::class;
    }
}
