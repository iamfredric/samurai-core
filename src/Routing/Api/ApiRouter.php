<?php

namespace Boil\Routing\Api;

use Boil\Application;
use Boil\Support\Concerns\ConfigPath;
use Boil\Support\Concerns\ExtractModelArguments;
use Boil\Support\Wordpress\WpHelper;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiRouter
{
    /**
     * @var ApiRoute[]
     */
    protected array $routes = [];

    public function __construct(
        protected Application $app,
        protected string $namespace
    ) {
    }

    /**
     * @param  string|string[]|\Closure  $endpoint
     */
    public function get(string $uri, string|array|\Closure $endpoint): ApiRoute
    {
        return $this->routes[] = new ApiRoute('get', $uri, $endpoint, $this->namespace);
    }

    /**
     * @param  string|string[]|\Closure  $endpoint
     */
    public function post(string $uri, string|array|\Closure $endpoint): ApiRoute
    {
        return $this->routes[] = new ApiRoute('post', $uri, $endpoint, $this->namespace);
    }

    /**
     * @param  string|string[]|\Closure  $endpoint
     */
    public function delete(string $uri, string|array|\Closure $endpoint): ApiRoute
    {
        return $this->routes[] = new ApiRoute('delete', $uri, $endpoint, $this->namespace);
    }

    public function boot(): void
    {
        $config = new ConfigPath($this->app['config']->get('features.api.routes'));

        $config->include();

        WpHelper::add_action('rest_api_init', function () {
            foreach ($this->routes as $route) {
                WpHelper::register_rest_route($route->namespace, $route->getUri(), [
                    'methods' => $route->method,
                    'permission_callback' => '__return_true',
                    'callback' => function ($request) use ($route) {
                        $callable = $route->callback;

                        if ($callable instanceof \Closure) {
                            $response = $this->app->call($callable, ExtractModelArguments::fromCallable($callable, $request->get_params(), [
                                \WP_REST_Request::class => $request, // @phpstan-ignore-line
                            ]));
                        } else {
                            $controller = $this->app->make(
                                $callable[0],
                                ExtractModelArguments::fromConstructor($callable[0], [ // @phpstan-ignore-line
                                    \WP_REST_Request::class => $request, // @phpstan-ignore-line
                                ])
                            );

                            $response = $this->app->call(
                                [$controller, $callable[1]], // @phpstan-ignore-line
                                ExtractModelArguments::fromMethod($controller, $callable[1], $request->get_params(), [
                                    \WP_REST_Request::class => $request, // @phpstan-ignore-line
                                ])
                            );
                        }

                        if (! is_subclass_of($response, Response::class)) {
                            $response = new JsonResponse($response, 200);
                        }

                        /** @var JsonResponse $response */
                        $response->send();
                    },
                ]);
            }
        });
    }

    //    protected function routeResponse(ApiRoute $route, $request)
    //    {
    //        // Todo: Extract those arguments
    //        if (is_callable($route->callback)) {
    //            return call_user_func($route->callback);
    //        }
    //
    //        $class = $this->app->call($route->getClassName());
    //
    //        $method = new \ReflectionMethod($class, $route->getMethodName());
    //
    //        $params = $route->getUriParams();
    //
    //        $dependencies = [];
    //
    //        foreach ($method->getParameters() as $parameter) {
    //            $type = (string) $parameter->getType();
    //            if ($type === Request::class) {
    //                // Todo...
    //                $dependencies[] = new Request($request);
    //            } elseif ($type === 'WP_REST_Request') {
    //                $dependencies[] = $request;
    //            } elseif (in_array($parameter->getName(), $params)) {
    //                $dependencies[] = $request->get_param($parameter->getName());
    //            } elseif ($parameter->allowsNull()) {
    //                $dependencies[] = null;
    //            } elseif ($type) {
    //                $dependencies[] = $this->app->make($parameter->getClass()->name);
    //            } else {
    //                $dependencies[] = null;
    //            }
    //        }
    //
    //        $response = $method->invokeArgs(
    //            $class, $dependencies
    //        );
    //
    //        if ($response instanceof JsonResponse) {
    //            $response = new JsonResponse($response);
    //        }
    //
    //        return $response;
    //    }
}
