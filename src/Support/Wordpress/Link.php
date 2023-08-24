<?php

namespace Boil\Support\Wordpress;

class Link
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(protected array $attributes)
    {
    }

    public function exists(): bool
    {
        return $this->has('url');
    }

    /**
     * @param string|array<string, mixed> $class
     * @return string
     */
    public function render(string|array $class = ''): string
    {
        $attributes = [
            'href' => $this->get('url'),
        ];

        if ($this->get('target')) {
            $attributes['target'] = $this->get('target');
            $attributes['rel'] = 'noopener';
        }

        if ($class) {
            if (is_string($class)) {
                $attributes['class'] = $class;
            } elseif (is_array($class)) {
                $attributes = array_merge($attributes, $class);
            }
        }

        $parsedAttributes = [];

        foreach ($attributes as $key => $value) {
            $parsedAttributes[] = $key.'="'.$value.'"';
        }

        $parsedAttributes = implode(' ', $parsedAttributes);

        return "<a {$parsedAttributes}>{$this->get('title')}</a>";
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->attributes[$key] : $default;
    }

    public function has(string $key): bool
    {
        return isset($this->attributes[$key]);
    }
}
