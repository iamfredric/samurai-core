<?php

namespace Tests\Unit\Support\Transformers\AttributeGettersTest;

use Boil\Support\Transformers\AttributeGetters;

it('transforms data with attribute getters', function () {
    $transformer = new AttributeGetters([
        'title' => 'hello world',
        'contents' => 'this is how to make things great',
    ], new ExampleClass());

    expect($transformer->transform())->toBe([
        'title' => 'hello world',
        'contents' => 'this-is-how-to-make-things-great',
    ]);
});

class ExampleClass
{
    public function getContentsAttribute($contents)
    {
        return str_replace(' ', '-', $contents);
    }
}
