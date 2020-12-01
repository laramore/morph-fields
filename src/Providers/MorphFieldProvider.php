<?php
/**
 * Load and prepare morph fields.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Providers;

use Illuminate\Support\ServiceProvider;
use Laramore\Traits\Provider\MergesConfig;

class MorphFieldProvider extends ServiceProvider
{
    use MergesConfig;

    /**
     * Before booting, create our definition for migrations.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/field.php', 'field',
        );

        $this->mergeConfigFrom(
            __DIR__.'/../../config/field/constraint.php', 'field.constraint',
        );
    }
}
