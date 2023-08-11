<?php

namespace Boil\Routing;

use Boil\Application;
use Boil\Database\Model;
use Illuminate\Http\Response;

class Router
{
    protected $currentRoute;

    public function __construct(protected Application $app)
    {
    }

    public function capture(): void
    {
        $path = $this->app['config']->get('app.paths.routes.web');

        $app = $this->app;

        include_once $path;

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

        foreach ($templateHooks as $hook) {
            add_filter($hook, function ($template, $type, $templates) {
                foreach ($templates as $t) {
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
            if ($this->currentRoute) {
                $callable = $this->app['router']->resolve($this->currentRoute);

                if (is_string($callable)) {
                    if (str_contains($callable, '@')) {
                        [$callable, $method] = explode('@', $callable);
                    } else {
                        $method = '__invoke';
                    }

                    $response = $this->app->call([$this->app->make($callable), $method]);

                    if (! $response instanceof \Illuminate\Http\Response) {
                        $response = new Response($response, 200);
                    }

                    $response->send();

                    return null;
                } elseif (is_callable($callable)) {
                    // Todo: Things...
                    $thing = $this->app->call($callable);

                    die(var_dump($thing));
                } elseif (is_array($callable)) {
                    [$controller, $method] = $callable;

                    $controller = $this->app->make($controller);
                    $reflector = new \ReflectionMethod($controller, $method);

                    $args = [];

                    foreach ($reflector->getParameters() as $param) {
                        if (! $param->isOptional()) {
                            $reflector = new \ReflectionClass($param->getType()->getName());

                            if ($reflector?->getParentClass()?->getName() === Model::class) {
                                $args[$param->getName()] = $reflector->getName()::current();
                            }
                        }
                    }

                    $response = $this->app->call([$controller, $method], $args);

                    if (! $response instanceof \Illuminate\Http\Response) {
                        $response = new Response($response, 200);
                    }

                    $response->send();

                    return null;
                    die('Correct');
                }


                die(var_dump($callable));
                return null;
            }

            return $template;
        });
    }
}
