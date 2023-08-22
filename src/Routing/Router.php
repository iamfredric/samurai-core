<?php

namespace Boil\Routing;

use Boil\Application;
use Boil\Database\Model;
use Boil\Support\Concerns\ConfigPath;
use Boil\Support\Concerns\ExtractModelArguments;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class Router
{
    protected $currentRoute;

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

        add_filter('theme_page_templates', function($templates) {
            foreach ($this->app['router']->getTemplates() as $name => $template) {
                $templates[$name] = $template->name;
            }

            return $templates;
        });

        foreach ($templateHooks as $hook) {
            add_filter($hook, function ($template, $type, $templates) {
                foreach ($templates as $t) {
//                    dd($key = get_post_meta(get_the_ID(), '_wp_page_template', true));
//                    if ($template != 'search' && $this->routeIsDefined($key = get_post_meta(get_the_ID(), '_wp_page_template', true))) {
//                        return $this->routeResponse($this->templates[$key]);
//                    }
                    if  ($x = $this->app['router']->isRegistered($t)) {
                        $this->currentRoute = $x;

                        return $t;
                    }
                }

                return $template;
            }, 1, 3);
        }
    }

    public function send(): void
    {
        add_action('template_include', function ($template) {
            if ($template === 'search.php') {
                $this->currentRoute = $this->app['router']->getSearchTemplate();
            }

            if (! $this->currentRoute) {
                return $template;
            }

            if ($this->currentRoute->getView()) {
                $attributes = [];

                if ($postType = get_post()->post_type) {
                    $modelName = Str::of($postType)->studly()->singular()->prepend('App\\Models\\')->toString();

                    $model = class_exists($modelName) ? $modelName : Model::class;

                    $attributes[$postType] = $model::current();
                }

                $response = new Response(view($this->currentRoute->getView(), $attributes));
                $response->send();

                return null;
            }

            $callable = $this->currentRoute->getCallable();

            if ($callable instanceof \Closure) {
                $response = $this->app->call($callable, ExtractModelArguments::fromCallable($callable));
            } else {
                $controller = $this->app->make(
                    $callable[0],
                    ExtractModelArguments::fromConstructor($callable[0])
                );

                $response = $this->app->call(
                    [$controller, $callable[1]],
                    ExtractModelArguments::fromMethod($controller, $callable[1])
                );
            }

            if (! $response instanceof \Illuminate\Http\Response) {
                $response = new Response($response, 200);
            }

            $response->send();

            return null;
        });
    }
}
