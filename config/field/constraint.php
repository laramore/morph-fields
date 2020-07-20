<?php

namespace Laramore\Fields\Constraint;

return [

    /*
    |--------------------------------------------------------------------------
    | Default constraints
    |--------------------------------------------------------------------------
    |
    | This option defines the default constraints used in fields.
    |
    */
    
    'classes' => [
        BaseIndexableConstraint::MORPH_INDEX => MorphIndex::class,
        BaseRelationalConstraint::MORPH => Morph::class,
    ],

    'configurations' => [
        'morph_index' => [
            'type' => BaseIndexableConstraint::MORPH_INDEX,
        ],
        'morph' => [
            'type' => BaseRelationalConstraint::MORPH,
        ],
    ],
];
