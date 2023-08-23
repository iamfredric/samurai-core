<?php

namespace Boil\Support\Wordpress;

use Tests\Support\Wordpress\WpHelperFake;

/**
 * @method static \WP_Term[]|int[]|string[]|string|\WP_Error get_terms(array|string $args = [], array|string $deprecated = '')
 * @method static \WP_Term|array|\WP_Error|null get_term( int|\WP_Term|object $term, string $taxonomy = '', string $output = '', string $filter = 'raw' )
 * @method static \WP_Term[]|false|\WP_Error get_the_terms(int|\WP_Post $post, string $taxonomy)
 * @method static bool is_category(int|string|array $category = '')
 * @method static bool is_tax(string|array $taxonomy = '', string|array|int $term = '')
 * @method static string get_term_link(int|\WP_Term $term, string $taxonomy = '')
 * @method static true add_action(string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1)
 * @method static true add_filter(string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1)
 * @method static void register_nav_menu(string $location, string $description)
 * @method static void|string|false wp_nav_menu(array $args = [])
 * @method static \WP_Post_Type|\WP_Error register_post_type(string $post_type, array|string $args = [])
 * @method static string __(string $text, string $domain = 'default')
 * @method static \WP_Taxonomy|\WP_Error register_taxonomy(string $taxonomy, array|string $object_type, array|string $args = [])
 * @method static bool register_rest_route(string $route_namespace, string $route, array $args = [], bool $override = false)
 * @method static void|false add_theme_support(string $feature, mixed $args)
 * @method static void add_image_size(string $name, int $width, int $height, bool|array $crop = false)
 * @method static string|false wp_get_attachment_image_srcset(int $attachment_id, string|int[] $size = 'medium', array $image_meta = null)
 * @method static int|false get_post_thumbnail_id(int|\WP_Post $post_id)
 * @method static string get_the_title(int|\WP_Post $post = 0)
 * @method static string|false wp_get_attachment_image_url(int $attachment_id, string|array $size = 'thumbnail', bool $icon = false)
 * @method static bool has_post_thumbnail(int|\WP_Post $post = null)
 */
class WpHelper
{
    protected static $instance;

    public static function fake(array $fakes = [])
    {
        return static::$instance = new WpHelperFake($fakes);
    }

    public static function callFunction($function, ...$args)
    {
        if (static::$instance) {
            return static::$instance->callFunction($function, ...$args);
        }

        return $function(...$args);

//        if (isset(static::$fakes[$function])) {
//            return static::$fakes[$function](...$args);
//        }
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return static::callFunction($name, ...$arguments);
    }
}
