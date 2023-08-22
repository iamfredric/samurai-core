<?php

namespace Boil\Routing;

class Routes
{
    protected array $routes = [];

    protected array $templates = [];

    protected array $views = [];

    public function view(string $name, string $view, array $options = [])
    {
        $this->views[$name] = new Template($name, null, $options, $view);
    }

    public function register(string $name, string|array|callable $callback)
    {
        $this->routes[$name] = new Template($name, $callback); //$callback;
    }

    public function template($key, $name, $endpoint, $options = [])
    {
        $this->templates[$key] = new Template($name, $endpoint, $options);
    }

    /**
     * @return array<Template>
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function isRegistered(string $template)
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

    public function getCurrentRoute()
    {
        return 'A current route has emerged!';
    }

    public function resolve(string $route)
    {
        return $this->templates[$route] ?? $this->routes[$route];
    }

    public function getSearchTemplate()
    {
        return $this->routes['search'] ?? null;
    }
}
