<?php

namespace Tests\Support\Wordpress;

use PHPUnit\Framework\Assert;

class WpHelperFake
{
    protected array $called = [];

    public function __construct(protected array $fakes = []) {}

    public function assertCalled($function, ?callable $callable = null)
    {
        Assert::assertTrue(isset($this->called[$function]));

        if ($callable && $this->called[$function]) {
            $result = false;

            foreach ($this->called[$function] as $call) {

                if ($callable(...$call['args'])) {
                    $result = true;
                }
            }

            Assert::assertTrue($result, 'The callable did not return true for any of the calls');
        }

        return $this;
    }

    public function callFunction($function, ...$args)
    {
        if (! isset($this->called[$function])) {
            $this->called[$function] = [];
        }

        $this->called[$function][] = [
            'args' => $args,
        ];

        if (isset($this->fakes[$function])) {
            return $this->fakes[$function](...$args);
        }
    }
}
