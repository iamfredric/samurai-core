<?php

namespace Samurai\Support\Wordpress;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Image implements Arrayable, Jsonable
{
    protected Collection $attributes;

    /** @param  array<string, mixed>|null  $thumbnail */
    public function __construct(?array $thumbnail = null)
    {
        $this->attributes = new Collection(Arr::dot($thumbnail ?: []));
    }

    public function id(): ?int
    {
        return $this->attributes->get('id');
    }

    public function identifier(): string
    {
        return "media-item-{$this->id()}";
    }

    public function title(): ?string
    {
        return $this->attributes->get('title');
    }

    public function url(?string $size = null): ?string
    {
        if (empty($size)) {
            return $this->attributes->get('url');
        }

        return $this->attributes->get("sizes.{$size}") ?: $this->attributes->get('url');
    }

    public function getWidth(?string $size = null): ?int
    {
        if (empty($size)) {
            return $this->attributes->get('width');
        }

        return $this->attributes->get("sizes.{$size}-width") ?: $this->attributes->get('width');
    }

    public function getHeight(?string $size = null): ?int
    {
        if (empty($size)) {
            return $this->attributes->get('height');
        }

        return $this->attributes->get("sizes.{$size}-height") ?: $this->attributes->get('height');
    }

    public function alt(): ?string
    {
        return $this->attributes->get('alt');
    }

    public function description(): ?string
    {
        return $this->attributes->get('description');
    }

    /**
     * @param  array<string, string>  $attributes
     */
    public function render(?string $size = null, $attributes = []): string
    {
        if ($srcset = $this->getSrcSet($size)) {
            $attributes['srcset'] = $srcset;
            $attributes['sizes'] = '100vw';
        }

        $attributes = (new Collection([
            'width' => $this->getWidth($size),
            'height' => $this->getHeight($size),
            'src' => $this->url($size),
            'loading' => 'lazy',
            'alt' => $this->alt(),
            'title' => $this->title(),
            'decoding' => 'async',
        ]))
            ->merge($attributes)
            ->map(fn ($value, $attribute) => "{$attribute}=\"{$value}\"")
            ->implode(' ');

        return "<img {$attributes}>";
    }

    public function getSrcSet(?string $size = null): ?string
    {
        $size ??= 'default';

        if (! $this->attributes->has("src-sets.{$size}")) {
            $this->attributes->put("src-sets.{$size}", WpHelper::wp_get_attachment_image_srcset($this->id(), $size)); // @phpstan-ignore-line
        }

        return $this->attributes->get("src-sets.{$size}");
    }

    public function caption(): ?string
    {
        return $this->attributes->get('caption');
    }

    protected function generateStyleSheet(?string $size = null): string
    {
        if (! $srcset = WpHelper::wp_get_attachment_image_srcset($this->id(), '1920x880')) { // @phpstan-ignore-line
            return "<style>#{$this->identifier()} {background-image: url(".$this->url($size).')}</style>';
        }

        $css = collect(explode(', ', $srcset))->map(function ($item) {
            [$url, $width] = explode(' ', $item);

            return (object) [
                'url' => $url,
                'width' => (int) str_replace('w', '', $width),
            ];
        })->sortByDesc('width')->map(function ($item) {
            return "@media only screen and (max-width: {$item->width}px) { #{$this->identifier()} {background-image: url({$item->url})} }";
        })->implode('');

        return "<style>#{$this->identifier()} {background-image: url(".$this->url($size).")}{$css}</style>";
    }

    public function styles(?string $size = null): ?string
    {
        if ($style = $this->generateStyleSheet($size)) {
            WpHelper::add_action('wp_head', function () use ($style) {
                echo $style;
            });

            WpHelper::add_action('admin_footer', function () use ($style) {
                echo $style;
            });

            return "id={$this->identifier()}";
        }

        return null;
    }

    public function exists(): bool
    {
        return $this->attributes->has('id');
    }

    public function __toString(): string
    {
        return $this->url() ?: '';
    }

    public function toArray(): array
    {
        return Arr::undot($this->attributes->toArray());
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray());
    }
}
