<?php

namespace Boil\Support\Facades;

use Boil\Support\Media\ImageConfigurator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ImageConfigurator support(string...$types)
 * @method static ImageConfigurator register(string $name, ?int $width = null, ?int $height = null, bool $crop = true)
 */
class Image extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ImageConfigurator::class;
    }
}
