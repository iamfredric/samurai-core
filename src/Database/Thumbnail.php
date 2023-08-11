<?php

namespace Boil\Database;

use Boil\Support\Wordpress\Image;
use Boil\Support\Wordpress\WpImage;

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
        if (method_exists($this, 'getFieldsAttribute') && $this->fields->has('thumbnail')) {
            return new Image($this->fields->get('thumbnail'));
        }

        return new WpImage($this->id);
    }
}
