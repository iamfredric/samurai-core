<?php

namespace Samurai\Routing\Api;

class ApiRoute
{
    /**
     * @param  string|string[]|\Closure  $callback
     */
    public function __construct(
        public string $method,
        public string $uri,
        public string|array|\Closure $callback,
        public string $namespace
    ) {}

    public function getUri(): string
    {
        if (str_contains($this->uri, '{')) {
            return collect(explode('/', $this->uri))->map(function ($part) {
                if (str_contains($part, '{')) {
                    $name = str_replace(['{', '}'], '', $part);

                    return "(?P<{$name}>\w+)";
                }

                return $part;
            })->implode('/');
        }

        return $this->uri;
    }
}
