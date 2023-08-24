<?php

namespace Boil\Support\Wordpress;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class WpImage implements Arrayable, Jsonable
{
    /**
     * @var int
     */
    protected $postId;

    /**
     * @var int|false
     */
    protected $thumbnailId;

    /**
     * WpImage constructor.
     *
     * @param  int  $postId
     */
    public function __construct($postId)
    {
        $this->postId = $postId;
    }

    /**
     * @return int|false
     */
    public function id()
    {
        if (! $this->thumbnailId) {
            $this->thumbnailId = WpHelper::get_post_thumbnail_id($this->postId);
        }

        return $this->thumbnailId;
    }

    /**
     * @return string
     */
    public function identifier()
    {
        return "thumbnail-{$this->thumbnailId}";
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->id() ? WpHelper::get_the_title($this->id()) : '';
    }

    /**
     * @param  string|null  $size
     * @return string|false
     */
    public function url($size = null)
    {
        return $this->id() ? WpHelper::wp_get_attachment_image_url($this->id(), $size): false; // @phpstan-ignore-line
    }

    /**
     * @param  string  $size
     * @param  array<string, mixed> $attributes
     * @return string
     */
    public function render($size = null, $attributes = [])
    {
        return WpHelper::get_the_post_thumbnail($this->postId, $size, $attributes); // @phpstan-ignore-line
    }

    /**
     * @param  string|null  $size
     * @return string|null
     */
    public function style($size = null)
    {
        if (! $this->id()) {
            return null;
        }

        if (! $srcset = WpHelper::wp_get_attachment_image_srcset($this->id(), $size)) { // @phpstan-ignore-line
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

    public function styles(string $size = null): ?string
    {
        if ($style = $this->style($size)) {
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

    /**
     * @return bool
     */
    public function exists()
    {
        return WpHelper::has_post_thumbnail($this->postId);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->id() ? WpHelper::acf_get_attachment($this->id()) ?: [] : [];
    }

    public function toJson($options = 0): string|false
    {
        return json_encode($this->toArray(), $options);
    }
}
