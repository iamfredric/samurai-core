<?php

namespace Samurai\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Samurai\Support\Media\ImageConfigurator;
use Samurai\Support\Media\ImageSize;

/**
 * @method static ImageConfigurator support(string...$types)
 * @method static ImageSize register(string $name, ?int $width = null, ?int $height = null, bool $crop = true)
 * @method static void addResolution(int $resolution)
 */
class Image extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ImageConfigurator::class;
    }
}
