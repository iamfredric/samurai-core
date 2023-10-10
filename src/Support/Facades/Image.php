<?php

namespace Boil\Support\Facades;

use Boil\Support\Media\ImageConfigurator;
use Boil\Support\Media\ImageSize;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ImageConfigurator support(string...$types)
 * @method static ImageSize register(string $name, ?int $width = null, ?int $height = null, bool $crop = true)
 */
class Image extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ImageConfigurator::class;
    }
}
