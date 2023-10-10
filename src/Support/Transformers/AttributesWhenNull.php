<?php

namespace Samurai\Support\Transformers;

use Illuminate\Support\Str;

class AttributesWhenNull
{
    /**
     * @param  mixed[]  $attributes
     * @param  object  $instance
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
            if (! $item) {
                $methodName = $this->getMethodNameByKey($key);

                if (method_exists($this->instance, $methodName)) {
                    $this->attributes[$key] = $this->instance->{$methodName}();
                }
            }
        }

        return $this->attributes;
    }

    protected function getMethodNameByKey(string $key): string
    {
        return (string) Str::of($key)
            ->camel()
            ->ucfirst()
            ->prepend('when')
            ->append('IsNull');
    }
}
