<?php

namespace Boil\Database;

use Boil\Support\Wordpress\WpHelper;

trait FormatedContent
{
    public function getContentAttribute($content)
    {
        return WpHelper::apply_filters('the_content', $content);
    }
}
