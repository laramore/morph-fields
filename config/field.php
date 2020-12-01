<?php

namespace Laramore\Fields;

return [

    /*
    |--------------------------------------------------------------------------
    | Default fields
    |--------------------------------------------------------------------------
    |
    | This option defines the default fields.
    |
    */

    'configurations' => [
        Reversed\HasManyMorph::class => [
            'options' => [
                'visible', 'fillable',
            ],
            'proxy' => [
                'configurations' => [
                    'attach' => [],
                    'detach' => [],
                    'sync' => [],
                    'update' => [],
                    'delete' => [],
                    'toggle' => [],
                    'sync_without_detaching' => [],
                    'update_existing_pivot' => [],
                ],
            ],
        ],
        ManyMorphToOne::class => [
            'options' => [
                'visible', 'fillable', 'required',
            ],
            'target_model' => \Laramore\Contracts\Eloquent\LaramoreModel::class,
            'fields' => [
                'type' => ModelEnum::class,
                'id' => Integer::class,
                'reversed' => Reversed\HasManyMorph::class,
            ],
            'templates' => [
                'type' => '${name}_${identifier}',
                'id' => '${name}_${identifier}',
                'reversed' => '+{modelname}',
                'self_reversed' => 'reversed_+{name}',
            ],
        ],
    ],

];
