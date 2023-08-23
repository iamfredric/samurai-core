<?php

namespace Tests\Unit\Support\Transformers\CastsTest;

use Boil\Support\Transformers\Casts;

it('casts things', function () {
    $caster = new Casts('zip', 'array');

    expect($caster->transform())->toBe(['zip']);

    $caster = new Casts(['zip' => 'zup'], 'stdClass');

    expect($caster->transform())->toBeInstanceOf(\stdClass::class);

    $caster = new Casts('Whazzup?!', ExampleClass::class);

    expect($caster->transform())->toBeInstanceOf(ExampleClass::class);
});

class ExampleClass
{
    public function __construct(protected string $thing)
    {
    }
}
