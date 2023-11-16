<?php

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Samurai\Support\Translations\Translator;
use Samurai\Support\Wordpress\WpHelper;

if (! function_exists('start_app')) {
    function start_app(string $dir): void
    {
        if (! defined('APP_START')) {
            define('APP_START', microtime(true));
        }

        $app = new \Samurai\Application(dirname($dir));

        if (function_exists('get_locale')) {
            $app->setLocale(get_locale());
        }

        $app->singleton(
            Illuminate\Contracts\Http\Kernel::class,
            Samurai\Http\Kernel::class
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
     * @template T
     *
     * @param  class-string<T>|null  $abstract
     * @param  array<string,mixed>  $parameters
     * @return ($abstract is null ? Application : T)
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function app(string $abstract = null, array $parameters = []): mixed
    {
        if (is_null($abstract)) {
            return \Samurai\Application::getInstance();
        }

        return \Samurai\Application::getInstance()->make($abstract, $parameters);
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
        return (string) Str::of(WpHelper::get_bloginfo('stylesheet_directory'))
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

        return (string) Str::of($file)
            ->ltrim('/')
            ->replace('//', '/')
            ->prepend(theme_url('public').'/');
    }
}

if (! function_exists('config')) {
    /**
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed|null
     */
    function config($key, $default = null)
    {
        /** @var Repository $config */
        $config = \Samurai\Application::getInstance()->make('config');

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
    /**
     * @param  array<string, mixed>  $args
     * @return mixed (as $name is null ? \Illuminate\View\Factory : \Illuminate\Contracts\View\View)
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function view(string $name = null, array $args = [])
    {
        /** @var \Illuminate\View\Factory $blade */
        $blade = \Samurai\Application::getInstance()->make('view');

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

        $manifest = json_decode(file_get_contents($manifestFile) ?: '');

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

        return (string) Str::of($file)
            ->ltrim('/')
            ->replace('//', '/')
            ->prepend(theme_url(config('app.assets_path')).'/');
    }
}

if (! function_exists('theme_url')) {
    function theme_url(string $url = ''): string
    {
        return (string) Str::of(WpHelper::get_bloginfo('stylesheet_directory'))
            ->append("/$url")
            ->rtrim('/');
    }
}

if (! function_exists('string_translate')) {
    /**
     * @param  string[]  $attributes
     * @return ($string is null ? Translator : string)
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function string_translate(string $string = null, array $attributes = []): string|Translator
    {
        $translator = app(Translator::class);

        if ($string) {
            return $translator->translate($string, $attributes);
        }

        return $translator;
    }
}
