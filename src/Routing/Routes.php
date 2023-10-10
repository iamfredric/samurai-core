<?php

namespace Samurai\Routing;

class Routes
{
    /** @var array<string, Template> */
    protected array $routes = [];

    /** @var array<string, Template> */
    protected array $templates = [];

    /** @var array<string, Template> */
    protected array $views = [];

    /**
     * @param  array<string, mixed>  $options
     */
    public function view(string $name, string $view, array $options = []): Template
    {
        return $this->views[$name] = new Template($name, null, $options, $view);
    }

    /**
     * @param  string|string[]|callable  $callback
     */
    public function register(string $name, string|array|callable $callback): Template
    {
        return $this->routes[$name] = new Template($name, $callback);
    }

    /**
     * @param  string|string[]|callable  $callback
     * @param  array<string, mixed>  $options
     */
    public function template(string $key, string $name, string|array|callable $callback, array $options = []): Template
    {
        return $this->templates[$key] = new Template($name, $callback, $options);
    }

    /**
     * @return Template[]
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function isRegistered(string $template): bool|Template
    {
        if (isset($this->templates[$template])) {
            return $this->templates[$template];
        }

        if (isset($this->routes[$template])) {
            return $this->routes[$template];
        }

        if (isset($this->views[$template])) {
            return $this->views[$template];
        }

        $template = str_replace('.php', '', $template);

        if (isset($this->routes[$template])) {
            return $this->routes[$template];
        }

        if (isset($this->views[$template])) {
            return $this->views[$template];
        }

        return false;
    }

    public function resolve(string $route): Template
    {
        return $this->templates[$route] ?? $this->routes[$route];
    }

    public function getSearchTemplate(): ?Template
    {
        return $this->routes['search'] ?? null;
    }
}
