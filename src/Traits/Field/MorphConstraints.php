<?php
/**
 * Add management for field constraints.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Laramore\Contracts\Field\{
    Field, Constraint\IndexableConstraint
};
use Laramore\Fields\Constraint\{
    BaseIndexableConstraint, BaseRelationalConstraint, MorphIndex
};

trait MorphConstraints
{
    /**
     * Target constraint name.
     *
     * @var string
     */
    protected $sourceConstraintName;

    /**
     * Source constraint name.
     *
     * @var string
     */
    protected $targetConstraintName;

    /**
     * Define a primary constraint.
     *
     * @param  string                                         $name
     * @param  IndexableConstraint|array<IndexableConstraint> $indexes
     * @return self
     */
    public function morphIndex(string $name=null, array $indexes=[])
    {
        $this->needsToBeUnlocked();

        $constraint = $this->getConstraintHandler()->create(BaseIndexableConstraint::MORPH_INDEX, $name, $indexes);
        $this->targetConstraintName = $constraint->getName();

        return $this;
    }

    /**
     * Define a foreign constraint.
     *
     * @param  string             $name
     * @param MorphIndex         $target
     * @param  Field|array<Field> $fields
     * @return self
     */
    public function morph(string $name=null, MorphIndex $target, $fields=[])
    {
        $this->needsToBeUnlocked();

        $fields = \is_array($fields) ? [$this, ...$fields] : [$this, $fields];

        $constraint = $this->getConstraintHandler()->create(BaseRelationalConstraint::MORPH, $name, $fields);
        $constraint->setTarget($target);
        $this->sourceConstraintName = $constraint->getName();

        return $this;
    }
}
