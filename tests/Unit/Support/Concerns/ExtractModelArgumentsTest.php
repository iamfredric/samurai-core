<?php

namespace Tests\Unit\Support\Concerns\ExtractModelArgumentsTest;

use Boil\Database\Model;
use Boil\Support\Concerns\ExtractModelArguments;
use Boil\Support\Wordpress\WpHelper;

it('extracts from callable', function () {
    WpHelper::fake();

    $x = ExtractModelArguments::fromCallable(function (TestModel $model) {
        return $model;
    });

    expect($x['model'])->toBeInstanceOf(TestModel::class);
});

it('extracts from constructor', function () {
    $helper = WpHelper::fake();

    $x = ExtractModelArguments::fromConstructor(TestController::class);

    expect($x['model'])->toBeInstanceOf(TestModel::class);

    $helper->assertCalled('get_post');
});

it('extracts from method', function () {
    $helper = WpHelper::fake();

    $x = ExtractModelArguments::fromMethod(new TestController(new TestModel(), 'zup?'), 'hello');

    expect($x['model'])->toBeInstanceOf(TestModel::class);

    $helper->assertCalled('get_post');
});

class TestModel extends Model
{
}

class TestController
{
    public function __construct(
        protected TestModel $model,
        protected string $string,
    ) {
    }

    public function hello(TestModel $model)
    {

    }
}
