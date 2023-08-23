<?php

namespace Tests\Unit\Routing\TemplateTest;

use Boil\Routing\Template;

it('can be a template with class method call')
    ->expect(new Template('index', ['PostController', 'index']))
    ->name->toBe('index')
    ->getView()->toBeNull()
    ->options->toBe([])
    ->getCallable()->toBe(['PostController', 'index']);

it('can be a template with class method call with @ separator')
    ->expect(new Template('index', 'PostController@show'))
    ->name->toBe('index')
    ->getView()->toBeNull()
    ->options->toBe([])
    ->getCallable()->toBe(['PostController', 'show']);

it('can be a template with a invokable class')
    ->expect(new Template('index', 'PostController'))
    ->name->toBe('index')
    ->getView()->toBeNull()
    ->options->toBe([])
    ->getCallable()->toBe(['PostController', '__invoke']);

it('can be a template with a callable')
    ->expect(new Template('index', fn () => 'test'))
    ->name->toBe('index')
    ->getView()->toBeNull()
    ->options->toBe([])
    ->getCallable()->toBeInstanceOf(\Closure::class);

it('can be a template with a view')
    ->expect(new Template('index', 'Waevva', [], 'index-view'))
    ->name->toBe('index')
    ->getView()->toBe('index-view')
    ->options->toBe([])
    ->getCallable()->toBeInstanceOf(\Closure::class);
