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
 * @method static void acf_add_options_page(array $settings)
 * @method static void register_extended_field_group(array $settings)
 * @method static string get_bloginfo(string $show = '', string $filter = 'raw')
 * @method static string get_the_post_thumbnail(int|\WP_Post $post = null, string|int[] $size = 'post-thumbnail', string|array $attr = '')
 * @method static array|null acf_get_attachment(int $attachment_id)
 * @method static \WP_Post|array|null get_post(int|\WP_Post|null $post = null, string $output = 'OBJECT', string $filter = 'raw')
 * @method static string|string[]|void paginate_links(string|array $args = '')
 * @method static mixed get_query_var(string $query_var, mixed $default_value = '')
 * @method static string|void get_previous_posts_link(string $label = null)
 * @method static string|void get_next_posts_link(string $label = null)
 * @method static string|void get_previous_posts_page_link()
 * @method static string|void get_next_posts_page_link()
 * @method static string|false get_permalink(int|\WP_Post $post, bool $leavename = false)
 * @method static \WP_Post[]|int[] get_posts(?array $args = null)
 * @method static void acf_register_block_type(array $args)
 * @method static array|false get_fields(int $post_id = false, bool $format_value = true, bool $load_value = true)
 */
class WpHelper
{
    protected static ?WpHelperFake $instance = null;

    /**
     * @param array<string, callable> $fakes
     * @return WpHelperFake
     */
    public static function fake(array $fakes = []): WpHelperFake
    {
        return static::$instance = new WpHelperFake($fakes);
    }

    public static function callFunction(string $function, mixed ...$args): mixed
    {
        if (static::$instance) {
            return static::$instance->callFunction($function, ...$args);
        }

        if (function_exists($function)) {
            return $function(...$args);
        }

        return null;
    }

    /**
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return static::callFunction($name, ...$arguments);
    }
}
