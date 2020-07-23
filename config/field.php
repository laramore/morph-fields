<?php

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
        'has_many_morph' => [
            'type' => 'reversed_relation',
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
        'many_morph_to_one' => [
            'type' => 'relation',
            'target_model' => Laramore\Contracts\Eloquent\LaramoreModel::class,
            'fields' => [
                'type' => Laramore\Fields\ModelEnum::class,
                'id' => Laramore\Fields\Integer::class,
                'reversed' => Laramore\Fields\Reversed\HasManyMorph::class,
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
