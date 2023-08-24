<?php

use Illuminate\Contracts\Foundation\Application;

if (! function_exists('start_app')) {
    function start_app(string $dir): void
    {
        if (! defined('APP_START')) {
            define('APP_START', microtime(true));
        }

        $app = new \Boil\Application(dirname($dir));

        $app->singleton(
            Illuminate\Contracts\Http\Kernel::class,
            Boil\Http\Kernel::class
        );

        /** @var \Illuminate\Contracts\Http\Kernel $kernel */
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

        $response = $kernel->handle(
            $request = Illuminate\Http\Request::capture()
        );

        $response->send();

        $kernel->terminate($request, $response);
    }
}

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return mixed|Application
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function app(string $abstract = null, array $parameters = []): mixed
    {
        if (is_null($abstract)) {
            return \Boil\Application::getInstance();
        }

        return \Boil\Application::getInstance()->make($abstract, $parameters);
    }
}

if (! function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return app()->publicPath($path);
    }
}

if (! function_exists('theme_url')) {
    /**
     * Basic helper for getting the theme url
     *
     * @param  string  $url optional
     * @return string
     */
    function theme_url($url = '')
    {
        return (string) \Illuminate\Support\Str::of(get_bloginfo('stylesheet_directory'))
            ->append("/$url")
            ->rtrim('/');
    }
}

if (! function_exists('asset')) {
    /**
     * @param  string  $file
     * @return string
     */
    function asset($file)
    {
        $file = ltrim($file, '/');

        return (string) \Illuminate\Support\Str::of($file)
            ->ltrim('/')
            ->replace('//', '/')
            ->prepend(theme_url('public').'/');
    }
}

if (! function_exists('config')) {
    /**
     * @param  string  $key
     * @param  null  $default
     * @return mixed|null
     */
    function config($key, $default = null)
    {
        $config = \Boil\Application::getInstance()->make('config');

        return $config->get($key, $default);
    }
}

if (! function_exists('theme_path')) {
    /**
     * @param  string  $path
     * @return string
     */
    function theme_path($path = '')
    {
        if (function_exists('get_stylesheet_directory')) {
            return rtrim(get_stylesheet_directory(), '/').'/'.trim($path, '/');
        }

        return $path;
    }
}

if (! function_exists('uploads_path')) {
    /**
     * Basic helper for getting absoulte uploads path
     *
     * @param  string  $path
     * @return string
     */
    function uploads_path($path = '')
    {
        if (function_exists('wp_upload_dir')) {
            $directory = wp_upload_dir();

            return rtrim($directory['basedir'], '/').'/'.trim($path, '/');
        }

        return $path;
    }
}

if (! function_exists('view')) {
    function view($name = null, $args = [])
    {
        $blade = \Boil\Application::getInstance()->make('view');

        if ($name) {
            return $blade->make($name, $args);
        }

        return $blade;
    }
}
if (! function_exists('mix')) {
    /**
     * @param  string  $originalFilename
     * @return string
     */
    function mix($originalFilename)
    {
        $filename = '/'.ltrim($originalFilename, '/');

        $manifestFile = theme_path('/mix-manifest.json');

        if (! file_exists($manifestFile)) {
            return assets($originalFilename);
        }

        $manifest = json_decode(file_get_contents($manifestFile));

        return isset($manifest->{$filename})
            ? assets($manifest->{$filename})
            : assets($originalFilename);
    }
}

if (! function_exists('assets')) {
    /**
     * @param  string  $file
     * @return string
     */
    function assets($file)
    {
        $file = ltrim($file, '/');

        return (string) \Illuminate\Support\Str::of($file)
            ->ltrim('/')
            ->replace('//', '/')
            ->prepend(theme_url(config('app.assets_path')).'/');
    }
}

if (! function_exists('theme_url')) {
    /**
     * Basic helper for getting the theme url
     *
     * @param  string  $url optional
     * @return string
     */
    function theme_url($url = '')
    {
        return (string) \Illuminate\Support\Str::of(get_bloginfo('stylesheet_directory'))
            ->append("/$url")
            ->rtrim('/');
    }
}

if (! function_exists('translate')) {
    function translate(?string $string, array $attributes = [])
    {
        return app(\Boil\Support\Translations\Translator::class)->translate($string, $attributes);
    }
}

function acf_get_attachment($attachment)
{

    // Allow filter to short-circuit load attachment logic.
    // Alternatively, this filter may be used to switch blogs for multisite media functionality.
    $response = apply_filters('acf/pre_load_attachment', null, $attachment);
    if ($response !== null) {
        return $response;
    }

    // Get the attachment post object.
    $attachment = get_post($attachment);
    if (! $attachment) {
        return false;
    }
    if ($attachment->post_type !== 'attachment') {
        return false;
    }

    // Load various attachment details.
    $meta = wp_get_attachment_metadata($attachment->ID);
    $attached_file = get_attached_file($attachment->ID);
    if (strpos($attachment->post_mime_type, '/') !== false) {
        [$type, $subtype] = explode('/', $attachment->post_mime_type);
    } else {
        [$type, $subtype] = [$attachment->post_mime_type, ''];
    }

    // Generate response.
    $response = [
        'ID' => $attachment->ID,
        'id' => $attachment->ID,
        'title' => $attachment->post_title,
        'filename' => wp_basename($attached_file),
        'filesize' => 0,
        'url' => wp_get_attachment_url($attachment->ID),
        'link' => get_attachment_link($attachment->ID),
        'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
        'author' => $attachment->post_author,
        'description' => $attachment->post_content,
        'caption' => $attachment->post_excerpt,
        'name' => $attachment->post_name,
        'status' => $attachment->post_status,
        'uploaded_to' => $attachment->post_parent,
        'date' => $attachment->post_date_gmt,
        'modified' => $attachment->post_modified_gmt,
        'menu_order' => $attachment->menu_order,
        'mime_type' => $attachment->post_mime_type,
        'type' => $type,
        'subtype' => $subtype,
        'icon' => wp_mime_type_icon($attachment->ID),
    ];

    // Append filesize data.
    if (isset($meta['filesize'])) {
        $response['filesize'] = $meta['filesize'];
    } elseif (file_exists($attached_file)) {
        $response['filesize'] = filesize($attached_file);
    }

    // Restrict the loading of image "sizes".
    $sizes_id = 0;

    // Type specific logic.
    switch ($type) {
        case 'image':
            $sizes_id = $attachment->ID;
            $src = wp_get_attachment_image_src($attachment->ID, 'full');
            if ($src) {
                $response['url'] = $src[0];
                $response['width'] = $src[1];
                $response['height'] = $src[2];
            }
            break;
        case 'video':
            $response['width'] = acf_maybe_get($meta, 'width', 0);
            $response['height'] = acf_maybe_get($meta, 'height', 0);
            if ($featured_id = get_post_thumbnail_id($attachment->ID)) {
                $sizes_id = $featured_id;
            }
            break;
        case 'audio':
            if ($featured_id = get_post_thumbnail_id($attachment->ID)) {
                $sizes_id = $featured_id;
            }
            break;
    }

    // Load array of image sizes.
    if ($sizes_id) {
        $sizes = get_intermediate_image_sizes();
        $sizes_data = [];
        foreach ($sizes as $size) {
            $src = wp_get_attachment_image_src($sizes_id, $size);
            if ($src) {
                $sizes_data[$size] = $src[0];
                $sizes_data[$size.'-width'] = $src[1];
                $sizes_data[$size.'-height'] = $src[2];
            }
        }
        $response['sizes'] = $sizes_data;
    }

    /**
     * Filters the attachment $response after it has been loaded.
     *
     * @date    16/06/2020
     *
     * @since   5.9.0
     *
     * @param  array  $response Array of loaded attachment data.
     * @param  WP_Post  $attachment Attachment object.
     * @param  array|false  $meta Array of attachment meta data, or false if there is none.
     */
    return apply_filters('acf/load_attachment', $response, $attachment, $meta);
}
