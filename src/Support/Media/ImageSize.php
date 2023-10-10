<?php

namespace Samurai\Support\Media;

class ImageSize
{
    public function __construct(
        public string $name,
        public ?int $width = null,
        public ?int $height = null,
        public bool $crop = true,
    ) {
        $this->setSizeFromName();
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function crop(): static
    {
        $this->crop = true;

        return $this;
    }

    public function scale(): static
    {
        $this->crop = false;

        return $this;
    }

    protected function setSizeFromName(): void
    {
        if ((bool) $this->width || (bool) $this->height) {
            return;
        }

        if (preg_match('/^([0-9]+)x([0-9]+)$/', $this->name)) {
            [$width, $height] = explode('x', $this->name);

            $this->width = (int) $width;
            $this->height = (int) $height;
        }
    }
}
