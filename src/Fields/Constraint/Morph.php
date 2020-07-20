<?php
/**
 * Define a morph constraint.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Laramore\Contracts\Field\Constraint\IndexableConstraint;

class Morph extends BaseRelationalConstraint
{
    /**
     * Indicate if this constraint is composed of multiple fields.
     *
     * @return boolean
     */
    public function isComposed(): bool
    {
        return \count($this->getAttributes()) > 2;
    }

    /**
     * Return indexable constraint.
     *
     * @param IndexableConstraint $target
     * @return self
     */
    public function setTarget(IndexableConstraint $target)
    {
        if (!($target instanceof MorphIndex)) {
            throw new \Exception('Must target a morph index');
        }

        return parent::setTarget($target);
    }
}
