<?php

namespace Samurai\Database;

use Samurai\Support\Wordpress\WpHelper;

trait FormatedContent
{
    public function getContentAttribute($content)
    {
        return WpHelper::apply_filters('the_content', $content);
    }
}
