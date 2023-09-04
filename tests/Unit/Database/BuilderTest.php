<?php

namespace Unit\Database;

use Boil\Database\Builder;
use Boil\Database\Model;

it('can use where', function () {
    $builder = new Builder();

    $builder->where('foo', 'bar');

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'foo' => 'bar',
    ]);

    $builder->where('bar', 'baz');

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'foo' => 'bar',
        'bar' => 'baz',
    ]);
});

it('can use whereMeta', function () {
    $builder = new Builder();

    $builder->whereMeta('foo', 'bar');

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'meta_query' => [[
            'key' => 'foo',
            'compare' => '=',
            'value' => 'bar',
        ]],
    ]);

    $builder->whereMeta('foo', '!=', 'hej');

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'meta_query' => [
            [
                'key' => 'foo',
                'compare' => '=',
                'value' => 'bar',
            ], [
                'key' => 'foo',
                'compare' => '!=',
                'value' => 'hej',
            ],
            'relation' => 'AND',
        ],
    ]);
});

it('can use orWhereMeta', function () {
    $builder = new Builder();

    $builder->whereMeta('foo', 'bar');

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'meta_query' => [[
            'key' => 'foo',
            'compare' => '=',
            'value' => 'bar',
        ]],
    ]);

    $builder->orWhereMeta('foo', '!=', 'hej');

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'meta_query' => [
            [
                'key' => 'foo',
                'compare' => '=',
                'value' => 'bar',
            ], [
                'key' => 'foo',
                'compare' => '!=',
                'value' => 'hej',
            ],
            'relation' => 'OR',
        ],
    ]);
});

it('can use whereTaxonomyIn', function () {
    $builder = new Builder();

    $builder->whereTaxonomyIn('taxonomy_name', [2, 4], 'tax_field');

    expect($builder->getArguments())
        ->toBe([
            'suppress_filters' => false,
            'tax_query' => [[
                'taxonomy' => 'taxonomy_name',
                'terms' => [
                    2, 4,
                ],
                'field' => 'tax_field',
            ]],
        ]);
});

it('can use orderBy', function () {
    $builder = new Builder();

    $builder->orderBy('foo', 'desc');

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'orderby' => 'foo',
        'order' => 'DESC',
    ]);
});

it('can use limit', function () {
    $builder = new Builder();

    $builder->limit(10);

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'posts_per_page' => 10,
    ]);
});

it('can use latest', function () {
    $builder = new Builder();

    $builder->latest();

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
});

it('can use oldest', function () {
    $builder = new Builder();

    $builder->oldest();

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'orderby' => 'date',
        'order' => 'ASC',
    ]);
});

it('can use when', function () {
    $builder = new Builder();

    $builder->when(true, function ($builder) {
        $builder->where('foo', 'bar');
    });

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'foo' => 'bar',
    ]);

    $builder->when(false, function ($builder) {
        $builder->where('bar', 'baz');
    });

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'foo' => 'bar',
    ]);
});

it('can be used with macro', function () {
    Builder::macro('thing', function ($builder) {
        $builder->where('thing', 'was-registered');
    });

    $builder = new Builder();

    $builder->thing();

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'thing' => 'was-registered',
    ]);
});

it('can be affected by given model', function () {
    $model = new class extends Model
    {
        public ?string $type = 'post';

        public function scopeFoo($builder)
        {
            $builder->where('foo', 'bar');
        }
    };

    $builder = new Builder($model);

    $builder->foo();

    expect($builder->getArguments())->toBe([
        'suppress_filters' => false,
        'post_type' => 'post',
        'foo' => 'bar',
    ]);
});
