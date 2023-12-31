<?php

namespace Samurai\Database;

trait Components
{
    public function getComponentsAttribute($components)
    {
        return $this->components('components');
    }

    public function components($fieldname = 'components', $prefix = null)
    {
        $key = $prefix ? "{$prefix}-components" : 'components';

        if (! $this->attributes->has($key)) {
            $this->attributes->put($key, new \Samurai\Support\Components\Components($this->fields->get($fieldname) ?: [], $prefix));
        }

        return $this->attributes->get($key);
    }
}
