<?php

namespace Boil\Database;

trait AdvancedCustomFields
{
    public function getFieldsAttribute($fields = null)
    {
        if (! $this->attributes->has('fields')) {
            $this->attributes->put('fields', collect(get_fields($this->attributes->get('id'))));
        }

        return $this->attributes->get('fields');
    }
}
