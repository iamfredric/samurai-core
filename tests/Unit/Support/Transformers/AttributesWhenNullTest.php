<?php

namespace Tests\Unit\Support\Transformers\AttributesWhenNullTest;

use Boil\Support\Transformers\AttributesWhenNull;

it('transforms value to fallback method when is null', function () {
    $transformer = new AttributesWhenNull([
        'title' => null,
        'contents' => 'Zup mate',
    ], new ExampleClass());

    expect($transformer->transform())->toBe([
        'title' => 'hello world',
        'contents' => 'Zup mate',
    ]);
});

class ExampleClass
{
    public function whenTitleIsNull()
    {
        return 'hello world';
    }
}
