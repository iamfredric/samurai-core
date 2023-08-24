<?php

namespace Boil\Support\Wordpress;

class Link
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * Link constructor.
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->has('url');
    }

    /**
     * @param  string  $class
     * @return string
     */
    public function render($class = '')
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

        return '<a href="'.$this->get('url').'" class="'.$class.'">'.$this->get('title').'</a>';
    }

    /**
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->attributes[$key] : $default;
    }

    /**
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->attributes[$key]);
    }
}
