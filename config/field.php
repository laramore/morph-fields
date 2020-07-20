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
        'morph_has_many' => [
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
        'morph_many_to_one' => [
            'type' => 'relation',
            'target_model' => Laramore\Contracts\Eloquent\LaramoreModel::class,
            'fields' => [
                'type' => Laramore\Fields\ModelEnum::class,
                'id' => Laramore\Fields\Integer::class,
                'reversed' => Laramore\Fields\Reversed\MorphHasMany::class,
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
