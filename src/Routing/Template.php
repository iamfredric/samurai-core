<?php

namespace Samurai\Routing;

use Closure;
use Illuminate\Http\Response;
use Samurai\Application;

class Template
{
    /**
     * @var string|string[]|Closure|null
     */
    protected string|array|Closure|null $endpoint;

    /**
     * @param  string|string[]|Closure|callable|null  $endpoint
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        public readonly string $name,
        string|array|callable|null $endpoint,
        public readonly array $options = [],
        protected ?string $view = null,
    ) {
        $this->endpoint = is_callable($endpoint) ? Closure::fromCallable($endpoint) : $endpoint;
        if ($view) {
            $this->endpoint = (fn () => view($view));
        }
    }

    /**
     * @return string[]|string|Closure
     */
    public function getCallable(): array|string|Closure
    {
        if ($this->endpoint instanceof Closure) {
            return $this->endpoint;
        }

        $endpoint = is_array($this->endpoint) ? $this->endpoint : explode('@', $this->endpoint ?: '');

        return [$endpoint[0], $endpoint[1] ?? '__invoke'];
    }

    public function call(Application $app): Response
    {
        if (is_callable($this->endpoint)) {
            $response = $app->call($this->endpoint);
        } elseif (is_string($this->endpoint)) {
            if (str_contains($this->endpoint, '@')) {
                [$callable, $method] = explode('@', $this->endpoint);
            } else {
                $callable = $this->endpoint;
                $method = '__invoke';
            }
            $response = $app->call([$app->make($callable), $method]); // @phpstan-ignore-line
        } else {
            $response = $app->call([$app->make($this->endpoint[0]), $this->endpoint[1]]); // @phpstan-ignore-line
        }

        if (! $response instanceof \Illuminate\Http\Response) {
            $response = new Response($response, 200);
        }

        return $response;
    }

    public function getView(): ?string
    {
        return $this->view;
    }
}
