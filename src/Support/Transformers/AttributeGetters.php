<?php

namespace Boil\Support\Transformers;

use Illuminate\Support\Str;

class AttributeGetters
{
    /**
     * @param mixed[] $attributes
     * @param object $instance
     */
    public function __construct(
        protected array $attributes,
        protected $instance
    ) {
    }

    /**
     * @return mixed[]
     */
    public function transform(): array
    {
        foreach ($this->attributes as $key => $item) {
            $methodName = $this->translateKeyToMethodName($key);

            if (method_exists($this->instance, $methodName)) {
                $this->attributes[$key] = $this->instance->{$methodName}($item);
            }
        }

        return $this->attributes;
    }

    protected function translateKeyToMethodName(string $key): string
    {
        return (string) Str::of($key)
            ->camel()
            ->ucfirst()
            ->prepend('get')
            ->append('Attribute');
    }
}
