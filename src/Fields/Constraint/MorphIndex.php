<?php
/**
 * Define a morph index constraint.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Laramore\Contracts\Field\Constraint\IndexableConstraint;
use Laramore\Exceptions\LockException;

class MorphIndex extends BaseIndexableConstraint
{
    /**
     * All indexes used for the morph.
     *
     * @var array
     */
    protected $indexes;

    /**
     * Return the index of a specific model class.
     *
     * @param string $modelClass
     * @return IndexableConstraint
     */
    public function getIndex(string $modelClass): IndexableConstraint
    {
        return $this->indexes[$modelClass];
    }

    /**
     * Return all indexes.
     *
     * @return array<IndexableConstraint>
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * Add one or more fields to observe.
     *
     * @param  IndexableConstraint|array $indexes
     * @return self
     */
    public function on($indexes)
    {
        $indexes = \is_array($indexes) ? $indexes : [$indexes->getModelClass() => $indexes];
        $this->indexes = $indexes;

        return parent::on(\array_merge(...\array_values(\array_map(function (IndexableConstraint $index) {
            return $index->getFields();
        }, $indexes))));
    }

    /**
     * Check that this constraints works only on one model.
     *
     * @return void
     */
    protected function locking()
    {
        $count = (\count($this->getAttributes()) / \count($this->getIndexes()));

        foreach ($this->indexes as $index) {
            if (\count($index->getAttributes()) !== $count) {
                throw new LockException('The number of attributes in morph indexes must the same', 'attributes');
            }
        }
    }
}
