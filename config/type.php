<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Morph types
    |--------------------------------------------------------------------------
    |
    | This option defines the default types used by fields.
    | A field has a type. The type describes its default options,
    | its required type value. Also, other packages can define
    | the field factory type, its migration type, etc.
    |
    */

    'configurations' => [
        'morph_relation' => [
            'native' => 'morph_relation',
            'default_options' => [
                'visible', 'fillable', 'required',
            ],
        ],
        'reversed_morph_relation' => [
            'native' => 'reversed_morph_relation',
            'default_options' => [
                'visible', 'fillable',
            ],
        ],
    ],

];
