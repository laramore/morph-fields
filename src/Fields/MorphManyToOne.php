<?php
/**
 * Define a foreign field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Contracts\{
    Field\MorphRelationField, Eloquent\LaramoreModel
};
use Laramore\Traits\Field\ToMorphOneRelation;

class MorphManyToOne extends BaseComposed implements MorphRelationField
{
    use ToMorphOneRelation {
        ToMorphOneRelation::reset as protected resetRelation;
    }

    /**
     * Reet the value for the field.
     *
     * @param LaramoreModel|array|\ArrayAccess $model
     * @return mixed
     */
    public function reset($model)
    {
        if ($model instanceof LaramoreModel) {
            $this->resetRelation($model);
        }

        $this->getField('id')->reset($model);
    }
}
