<?php

namespace Samurai\Database;

use Samurai\Support\Wordpress\Image;
use Samurai\Support\Wordpress\WpImage;

/**
 * @property Image|WpImage $thumbnail
 */
trait Thumbnail
{
    /**
     * @return Image | WpImage
     */
    public function getThumbnailAttribute()
    {
        if (! $this->attributes->has('thumbnail')) {
            $this->attributes->put('thumbnail', $this->localizeThumbnail());
        }

        return $this->attributes->get('thumbnail');
    }

    /**
     * @return WpImage|Image
     */
    protected function localizeThumbnail()
    {
        if (method_exists($this, 'getFieldsAttribute') && $this->fields->has($this->getThumbnailFieldName())) {
            return new Image($this->fields->get($this->getThumbnailFieldName()));
        }

        return new WpImage($this->id);
    }

    protected function getThumbnailFieldName(): string
    {
        return 'thumbnail';
    }
}
