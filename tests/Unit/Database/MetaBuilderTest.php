<?php

namespace Unit\Database;

use Samurai\Database\MetaBuilder;

it('builds meta queries', function () {
    $builder = new MetaBuilder();

    $builder->where('thing', 'is-this')
        ->where('other-thing', '!=', 'that')
        ->whereNotNull('third-thing')
        ->where(function ($query) {
            $query->where('subthing', '=', 'that')
                ->orWhere('subthing', '=', 'this');
        });

    expect($builder->toArray())
        ->toBe([
            [
                [
                    'key' => 'subthing',
                    'compare' => '=',
                    'value' => 'that',
                ],
                [
                    'key' => 'subthing',
                    'compare' => '=',
                    'value' => 'this',
                ],
                'relation' => 'OR',
            ],
            [
                'key' => 'thing',
                'compare' => '=',
                'value' => 'is-this',
            ],
            [
                'key' => 'other-thing',
                'compare' => '!=',
                'value' => 'that',
            ],
            [
                'key' => 'third-thing',
                'compare' => '!=',
                'value' => '',
            ],
            'relation' => 'AND',
        ]);
});
