<?php

namespace Unit\Database;

use Boil\Database\Model;
use Boil\Database\Term;
use Boil\Support\Wordpress\WpHelper;

it('can fetch all terms', function () {
    WpHelper::fake([
        'get_terms' => fn () => [[
            'term_id' => 1,
            'name' => 'Example term',
            'slug' => 'example-term',
        ], [
            'term_id' => 2,
            'name' => 'Example term 2',
            'slug' => 'example-term-2',
        ]],
    ]);
    $terms = Term::all();

    expect($terms[0])
        ->toBeInstanceOf(Term::class)
        ->title->toBe('Example term');

    expect($terms[1])
        ->toBeInstanceOf(Term::class)
        ->title->toBe('Example term 2');
});

it('can find a term by id', function () {
    WpHelper::fake([
        'get_term' => fn ($id) => [
            'term_id' => $id,
            'name' => 'Example term',
            'slug' => 'example-term',
        ],
    ]);

    $term = Term::find(1);

    expect($term)
        ->toBeInstanceOf(Term::class)
        ->title->toBe('Example term');
});

it('can fetch terms for a given model', function () {
    WpHelper::fake([
        'get_the_terms' => fn ($id) => [[
            'term_id' => 1,
            'name' => 'Example term',
            'slug' => 'example-term',
        ], [
            'term_id' => 2,
            'name' => 'Example term 2',
            'slug' => 'example-term-2',
        ]],
    ]);

    $model = new Model(['id' => 123]);
    $terms = Term::forModel($model);

    expect($terms[0])
        ->toBeInstanceOf(Term::class)
        ->title->toBe('Example term');

    expect($terms[1])
        ->toBeInstanceOf(Term::class)
        ->title->toBe('Example term 2');
});

it('can determine if a term is active', function () {
    WpHelper::fake([
        'is_category' => fn () => true
    ]);

    $term = new Term(['id' => 1]);

    expect($term->isActive())->toBeTrue();
});

it('can get the url attribute', function () {
    WpHelper::fake([
        'get_term_link' => fn ($id) => 'http://example.com/' . $id
    ]);

    $term = new Term(['term_id' => 1]);

    expect($term->url)->toBe('http://example.com/1');
});


it('can get the type', function () {
    $term = new Term();

    expect($term->type())->toBe('term');
});
