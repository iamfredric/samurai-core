<?php

namespace Samurai\Routing;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Samurai\Application;
use Samurai\Database\Model;
use Samurai\Support\Concerns\ConfigPath;
use Samurai\Support\Concerns\ExtractModelArguments;
use Samurai\Support\Wordpress\WpHelper;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Router
{
    protected ?Template $currentRoute = null;

    public function __construct(protected Application $app)
    {
    }

    public function capture(): void
    {
        $configPaths = new ConfigPath($this->app['config']->get('features.web.routes'));

        if (! $configPaths->exists()) {
            return;
        }

        $configPaths->include();

        $templateHooks = [
            '404_template',
            'archive_template',
            'attachment_template',
            'author_template',
            'category_template',
            'date_template',
            'embed_template',
            'frontpage_template',
            'home_template',
            'index_template',
            'page_template',
            'paged_template',
            'privacypolicy_template',
            'search_template',
            'single_template',
            'singular_template',
            'tag_template',
            'taxonomy_template',
        ];

        WpHelper::add_filter('theme_page_templates', function ($templates) {
            foreach ($this->app['router']->getTemplates() as $name => $template) {
                $templates[$name] = $template->name;
            }

            return $templates;
        });

        foreach ($templateHooks as $hook) {
            WpHelper::add_filter($hook, function ($template, $type, $templates) {
                foreach ($templates as $t) {
                    //                    dd($key = get_post_meta(get_the_ID(), '_wp_page_template', true));
                    //                    if ($template != 'search' && $this->routeIsDefined($key = get_post_meta(get_the_ID(), '_wp_page_template', true))) {
                    //                        return $this->routeResponse($this->templates[$key]);
                    //                    }
                    if ($x = $this->app['router']->isRegistered($t)) {
                        $this->currentRoute = $x;

                        return $t;
                    }
                }

                return $template;
            }, 1, 3);
        }

        foreach ($this->app['router']->getCustomRoutes() as $route) {
            WpHelper::add_action('init', function () use ($route) {
                WpHelper::add_rewrite_rule($route->getRegex(), $route->getQuery(), 'top');
            });

            WpHelper::add_action('query_vars', function ($vars) use ($route) {
                foreach ($route->getQueryVars() as $var) {
                    array_push($vars, $var);
                }

                return $vars;
            });
        }
    }

    public function send(): void
    {
        WpHelper::add_action('template_include', function ($template) {
            if ($pageName = WpHelper::get_query_var('pagename')) {
                if ($route = $this->app['router']->getCustomRoutes()[$pageName] ?? null) {
                    $callable = $route->getCallable();

                    if ($callable instanceof \Closure) {
                        $response = $this->app->call($callable, ExtractModelArguments::fromCallable($callable, $route->getQueryVars()->mapWithKeys(fn ($name) => [
                            $name => WpHelper::get_query_var($name),
                        ])->toArray()));
                    } else {

                        $controller = $this->app->make(
                            $callable[0],
                            ExtractModelArguments::fromConstructor($callable[0])
                        );

                        $response = $this->app->call(
                            [$controller, $callable[1]], // @phpstan-ignore-line
                            ExtractModelArguments::fromMethod($controller, $callable[1], $route->getQueryVars()->mapWithKeys(fn ($name) => [
                                $name => WpHelper::get_query_var($name),
                            ])->toArray())
                        );
                    }

                    if (! is_subclass_of($response, BaseResponse::class)) {
                        $response = new Response($response, 200);
                    }

                    /** @var BaseResponse $response */
                    $response->send();
                }
            }

            if ($template === 'search.php') {
                $this->currentRoute = $this->app['router']->getSearchTemplate();
            }

            if (! $this->currentRoute) {
                return $template;
            }

            if ($this->currentRoute->getView()) {
                $attributes = [];

                if ($postType = WpHelper::get_post()?->post_type) {
                    $modelName = Str::of($postType)->studly()->singular()->prepend('App\\Models\\')->toString();

                    $model = class_exists($modelName) ? $modelName : Model::class;

                    $attributes[$postType] = $model::current();
                }

                $status = in_array($template, ['404', '404.php']) ? 404 : 200;

                $response = new Response(view($this->currentRoute->getView(), $attributes), $status);
                $response->send();

                return null;
            }

            $callable = $this->currentRoute->getCallable();

            if ($callable instanceof \Closure) {
                $response = $this->app->call($callable, ExtractModelArguments::fromCallable($callable));
            } else {
                $controller = $this->app->make(
                    $callable[0],
                    ExtractModelArguments::fromConstructor($callable[0]) // @phpstan-ignore-line
                );

                $response = $this->app->call(
                    [$controller, $callable[1]], // @phpstan-ignore-line
                    ExtractModelArguments::fromMethod($controller, $callable[1])
                );
            }

            if (! is_subclass_of($response, BaseResponse::class)) {
                $response = new Response($response, 200);
            }

            /** @var BaseResponse $response */
            $response->send();

            return null;
        });
    }
}
