<?php

namespace Boil\Support\Media;

use Boil\Support\Concerns\ConfigPath;

class ImageConfigurator
{
    /** @var string[] */
    protected array $types = [];

    /** @var ImageSize[] */
    protected array $imageSizes = [];

    public function __construct(protected ConfigPath $routesPath)
    {
    }

    public function support(string...$types): static
    {
        $this->types = array_unique([...$this->types, ...$types]);

        return $this;
    }

    public function register(
        string $name,
        ?int $width = null,
        ?int $height = null,
        bool $crop = true
    ): ImageSize {
        return $this->imageSizes[] = new ImageSize($name, $width, $height, $crop);
    }

    public function boot(): void
    {
        if (! $this->routesPath->exists()) {
            return;
        }

        $this->routesPath->include();

        add_action('after_setup_theme', function () {
            if ($this->types) {
                add_theme_support('post-thumbnails', $this->types);
            }
            foreach ($this->imageSizes as $imageSize) {
                add_image_size($imageSize->name, $imageSize->width, $imageSize->height, $imageSize->crop);
            }
        });
    }
}
