<?php

namespace Boil\Support\Wordpress;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Image implements Arrayable, Jsonable
{
    /**
     * @var boolean
     */
    private $hasimage = false;

    /**
     * @var int
     */
    private $thumbnailId;

    /**
     * @var string
     */
    private $thumbnailTitle;

    /**
     * @var string
     */
    private $thumbnailUrl;

    /**
     * @var string
     */
    private $thumbnailAlt;

    /**
     * @var string
     */
    private $thumbnailDescription;

    /**
     * @var array
     */
    private $thumbnailSizes;

    /**
     * @var string
     */
    private $thumbnailCaption;

    /**
     * @var array
     */
    private $thumbnailDimensions = [];

    protected $attributes = [];

    protected $todoAttributes;

    public function __construct(?array $thumbnail = null)
    {
        $this->todoAttributes = new Collection(Arr::dot($thumbnail));
    }

    public function id(): ?int
    {
        return $this->todoAttributes->get('id');
    }

    public function identifier(): string
    {
        return "media-item-{$this->id()}";
    }

    public function title(): ?string
    {
        return $this->todoAttributes->get('title');
    }

    public function url(?string $size = null): ?string
    {
        if (empty($size)) {
            return $this->todoAttributes->get('url');
        }

        return $this->todoAttributes->get("sizes.{$size}.source-url") ?: $this->todoAttributes->get('url');
    }

    public function getWidth($size = null): ?int
    {
        if (empty($size)) {
            return $this->todoAttributes->get('width');
        }

        return $this->todoAttributes->get("sizes.{$size}-width") ?: $this->todoAttributes->get('width');
    }

    public function getHeight($size = null): ?int
    {
        if (empty($size)) {
            return $this->todoAttributes->get('height');
        }

        return $this->todoAttributes->get("sizes.{$size}-height") ?: $this->todoAttributes->get('height');
    }

    public function alt(): ?string
    {
        return $this->todoAttributes->get('alt');
    }

    public function description(): ?string
    {
        return $this->todoAttributes->get('description');
    }

    /**
     * @param string|null $size
     * @param array<string, string> $attributes
     * @return string
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
            'decoding' => 'async'
        ]))
            ->merge($attributes)
            ->map(fn ($value, $attribute) => "{$attribute}=\"{$value}\"")
            ->implode(' ');

        return "<img {$attributes}>";
    }

    public function getSrcSet(?string $size = null): ?string
    {
        $size ??= 'default';

        if (! $this->todoAttributes->has("src-sets.{$size}")) {
            $this->todoAttributes->put("src-sets.{$size}", WpHelper::wp_get_attachment_image_srcset($this->id(), $size));
        }

        return $this->todoAttributes->get("src-sets.{$size}");
    }

    public function caption(): ?string
    {
        return $this->todoAttributes->get('caption');
    }

    protected function generateStyleSheet(?string $size = null): string
    {
        if (! $srcset = WpHelper::wp_get_attachment_image_srcset($this->id(), $size)) {
            return "<style>#{$this->identifier()} {background-image: url(".$this->url($size).")}</style>";
        }

        $css = collect(explode(', ', $srcset))->map(function ($item) {
            [$url, $width] = explode(' ', $item);

            return (object) [
                'url' => $url,
                'width' => (int) str_replace("w", "", $width)
            ];
        })->sortByDesc('width')->map(function ($item) {
            return "@media only screen and (max-width: {$item->width}px) { #{$this->identifier()} {background-image: url({$item->url})} }";
        })->implode('');

        return "<style>#{$this->identifier()} {background-image: url(".$this->url($size).")}{$css}</style>";
    }

    public function styles(?string $size = null): ?string
    {
        if ($style = $this->generateStyleSheet($size)) {
            WpHelper::add_action('wp_head', function () use($style) {
                echo $style;
            });

            WpHelper::add_action('admin_footer', function () use($style) {
                echo $style;
            });

            return "id={$this->identifier()}";
        }

        return null;
    }

    public function exists(): bool
    {
        return $this->todoAttributes->has('id');
    }

    public function __toString(): string
    {
        return $this->url() ?: '';
    }

    public function toArray(): array
    {
        return Arr::undot($this->todoAttributes->toArray());
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray());
    }
}
