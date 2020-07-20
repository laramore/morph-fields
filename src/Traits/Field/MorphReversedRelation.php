<?php
/**
 * Trait to add reversed relation methods.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Laramore\Contracts\Field\Constraint\Constraint;

trait MorphReversedRelation
{
    use ReversedRelation;

    /**
     * Return the source of the relation.
     *
     * @param string $className
     * @return Constraint
     */
    public function getSource(string $className=null): Constraint
    {
        return $this->getReversedField()->getTarget($className);
    }

    /**
     * Return the sources of the relation.
     *
     * @return array<Constraint>
     */
    public function getSources(): array
    {
        return $this->getReversedField()->getTargets();
    }

    /**
     * Models where the relation is set from.
     *
     * @return array<string>
     */
    public function getSourceModels(): array
    {
        return $this->getReversedField()->getTargetModels();
    }
}
