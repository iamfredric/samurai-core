<?php

if (! function_exists('config')) {
    /**
     * @param string $key
     * @param null $default
     *
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
     * @param string $path
     *
     * @return string
     */
    function theme_path($path = '')
    {
        if (function_exists('get_stylesheet_directory')) {
            return rtrim(get_stylesheet_directory(), '/') . '/' . trim($path, '/');
        }

        return $path;
    }
}

if (! function_exists('uploads_path')) {
    /**
     * Basic helper for getting absoulte uploads path
     *
     * @param string $path
     *
     * @return string
     */
    function uploads_path($path = '')
    {
        if (function_exists('wp_upload_dir')) {
            $directory = wp_upload_dir();

            return rtrim($directory['basedir'], '/') . '/' . trim($path, '/');
        }

        return $path;
    }
}

if (! function_exists('view')) {
    function view($name = null, $args = []) {
        $blade = \Boil\Application::getInstance()->make('view');

        if ($name) {
            return $blade->make($name, $args);
        }

        return $blade;
    }
}
if (! function_exists('mix')) {
    /**
     * @param string $originalFilename
     *
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
     * @param string $file
     *
     * @return string
     */
    function assets($file)
    {
        $file = ltrim($file, '/');

        return (string) \Illuminate\Support\Str::of($file)
            ->ltrim('/')
            ->replace('//', '/')
            ->prepend(theme_url(config('paths.assets')).'/');
    }
}

if (! function_exists('theme_url')) {
    /**
     * Basic helper for getting the theme url
     *
     * @param string $url optional
     *
     * @return string
     */
    function theme_url($url = '')
    {
        return (string) \Illuminate\Support\Str::of(get_bloginfo('stylesheet_directory'))
            ->append("/$url")
            ->rtrim('/');
    }
}

