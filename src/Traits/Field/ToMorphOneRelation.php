<?php
/**
 * Add multiple methods for many/one to multiple relations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Laramore\Elements\OperatorElement;
use Laramore\Facades\{
    Meta, Operator, Option
};
use Laramore\Contracts\Eloquent\{
    LaramoreModel, LaramoreBuilder, LaramoreCollection
};
use Laramore\Contracts\Field\{
    Field, RelationField, Constraint\Constraint
};

trait ToMorphOneRelation
{
    use ModelRelation, IndexableConstraints, MorphConstraints;

    /**
     * Model the relation is on.
     *
     * @var string
     */
    protected $targetModel;

    /**
     * Return the reversed field.
     *
     * @param string $class
     * @return RelationField
     */
    public function getReversedField(string $class=null): RelationField
    {
        if (\func_num_args() !== 1) {
            throw new \Exception('Morph fields require the name class');
        }

        $classElement = $this->getField('type')->getElement($class);

        return $this->getField("reversed_{$classElement->getNative()}");
    }

    /**
     * Return all reversed fields.
     *
     * @return array<RelationField>
     */
    public function getReversedFields(): array
    {
        return \array_map(function ($class) {
            return $this->getReversedField($class);
        }, $this->getTargetModels());
    }

    /**
     * Define the model on which to point.
     *
     * @param string $model
     * @param string $reversedName
     * @param string $relationName
     * @return self
     */
    public function on(string $model, string $reversedName=null, string $relationName=null)
    {
        $this->needsToBeUnowned();

        $this->defineProperty('targetModel', $model);

        if ($model === 'self') {
            $this->addOption(Option::nullable());
        } else {
            foreach ($this->getReversedFields() as $field) {
                $field->setMeta($model::getMeta());
            }
        }

        if (!\is_null($reversedName)) {
            $this->reversedName($reversedName);
        }

        if (!\is_null($relationName)) {
            $this->relationName($relationName);
        }

        return $this;
    }

    /**
     * Define on self.
     *
     * @return self
     */
    public function onSelf()
    {
        $this->needsToBeUnowned();

        return $this->on('self');
    }

    /**
     * Indicate if it is a relation on itself.
     *
     * @return boolean
     */
    public function isOnSelf()
    {
        $model = $this->getTargetModel();

        return $model === $this->getMeta()->getModelClass() || $model === 'self';
    }

    /**
     * Define the reversed name of the relation.
     *
     * @param string $reversedName
     * @return self
     */
    public function reversedName(string $reversedName)
    {
        $this->needsToBeUnlocked();

        $this->templates['reversed'] = $reversedName;

        return $this;
    }

    /**
     * Define the relation name of the relation.
     *
     * @param string $relationName
     * @return self
     */
    public function relationName(string $relationName)
    {
        $this->needsToBeUnlocked();

        $this->templates['relation'] = $relationName;

        return $this;
    }

    /**
     * Indicate if the relation is head on or not.
     * Usefull to know which to use between source and target.
     *
     * @return boolean
     */
    public function isRelationHeadOn(): bool
    {
        return true;
    }

    /**
     * Return the source of the relation.
     *
     * @param string $className
     * @return Constraint
     */
    public function getSource(): Constraint
    {
        $this->needsToBeOwned();

        return $this->getConstraintHandler()->get($this->sourceConstraintName);
    }

    /**
     * Model where the relation is set from.
     *
     * @return string
     */
    public function getSourceModel(): string
    {
        $this->needsToBeOwned();

        return $this->getMeta()->getModelClass();
    }

    /**
     * Model where the relation is set to.
     *
     * @return string
     */
    public function getTargetModel(): string
    {
        $this->needsToBeOwned();

        return $this->targetModel;
    }

    /**
     * Models where the relation is set to.
     *
     * @return array<string>
     */
    public function getTargetModels(): array
    {
        $this->needsToBeOwned();

        return \array_map(function ($classElement) {
            return $classElement->getName();
        }, $this->getField('type')->getElements()->all());
    }

    /**
     * Return all targets of the relation.
     *
     * @param string $className
     * @return Constraint
     */
    public function getTarget(string $className=null): Constraint
    {
        $this->needsToBeOwned();

        /** @var Morph $target */
        $target = $this->getConstraintHandler()->get($this->targetConstraintName);

        if (\is_null($className)) {
            return $target;
        }

        return $target->getIndex($className);
    }

    /**
     * Return all targets of the relation.
     *
     * @return array<Constraint>
     */
    public function getTargets(): array
    {
        $this->needsToBeOwned();

        return \array_map(function ($class) {
            return $class::getMeta()->getPrimary();
        }, $this->getTargetModels());
    }

    /**
     * Define target models from the target model.
     *
     * @return void
     */
    protected function setTargetModels()
    {
        $targetModel = $this->getTargetModel();
        $callback = \interface_exists($targetModel) ? function ($model) use ($targetModel) {
            return (new \ReflectionClass($model))->implementsInterface($targetModel);
        } : function ($model) use ($targetModel) {
            return $model instanceof $targetModel;
        };

        $elements = \array_filter(\array_keys(Meta::all()), function ($model) use ($callback) {
            return !\is_subclass_of($model, Pivot::class) && $callback($model);
        });

        $this->getField('type')->elements($elements);
    }

    /**
     * Generate all reversed fields for each fields.
     *
     * @return void
     */
    protected function generateMorphFields()
    {
        $reversedField = $this->getField('reversed');
        unset($this->fields['reversed']);

        foreach ($this->getField('type')->getElements()->all() as $classElement) {
            $key = "reversed_{$classElement->getNative()}";
            $field = clone $reversedField;

            $field->setMeta($classElement->getMeta());
            $this->setField($key, $field);

            if ($classElement->hasReversedField()) {
                $this->templates[$key] = $classElement->getReversedField();
            } else {
                $this->templates[$key] = $this->templates['reversed'];
            }
        }
    }

    /**
     * Define on, off and from variables after being owned.
     *
     * @return void
     */
    protected function owned()
    {
        if (\is_null($this->getTargetModel())) {
            throw new \Exception('Related model settings needed. Set it by calling `on` method');
        } else if ($this->getTargetModel() === 'self') {
            $this->on($this->getSourceModel());
        }

        $this->setTargetModels();
        $this->generateMorphFields();

        parent::owned();

        $this->morphIndex(($this->templates['index'] ?? null), $this->getTargets());
        $this->morph(
            ($this->templates['relation'] ?? null),
            $this->getTarget(), [$this->getField('type'),
                $this->getField('id')
            ]
        );
    }

    /**
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        $model = $this->getTargetModel();

        $name = $this->getTarget(\get_class($value))->getAttribute()->getName();

        if (\is_null($value) || $value instanceof $model || \is_array($value) || $value instanceof LaramoreCollection) {
            return $value;
        }

        $model = new $model;
        $model->setAttributeValue($name, $value);

        return $model;
    }

    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * Reverbate the relation into database or other fields.
     * It should be called by the set method.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return mixed
     */
    public function reverbate(LaramoreModel $model, $value)
    {
        $this->getField('type')->set(
            $model,
            \is_null($value) ? null : $this->getField('type')->cast(\get_class($value))
        );

        $this->getField('id')->set(
            $model,
            \is_null($value) ? null : $this->getTarget(\get_class($value))->getAttribute()->get($value)
        );

        return $value;
    }

    /**
     * Return the query with this field as condition.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function relate(LaramoreModel $model)
    {
        $modelElement = $this->getField('type')->get($model);
        [$typeField, $idField] = $this->getSource()->getAttributes();

        $relation = $model->morphTo(
            $this->getName(),
            $typeField->getNative(),
            $idField->getNative(),
            \is_null($modelElement) ? null : $this->getTarget($modelElement->getClass())->getAttribute()->getNative()
        );

        if ($this->hasProperty('when')) {
            return (\call_user_func($this->when, $relation, $model) ?? $relation);
        }

        return $relation;
    }

    /**
     * Add a where null condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @param  boolean         $not
     * @return LaramoreBuilder
     */
    public function whereNull(LaramoreBuilder $builder, $value=null, string $boolean='and', bool $not=false): LaramoreBuilder
    {
        $builder = $this->getField('type')->addBuilderOperation($builder, 'whereNull', $boolean, $not);

        return $this->getField('id')->addBuilderOperation($builder, 'whereNull', $boolean, $not);
    }

    /**
     * Add a where not null condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @return LaramoreBuilder
     */
    public function whereNotNull(LaramoreBuilder $builder, $value=null, string $boolean='and'): LaramoreBuilder
    {
        return $this->whereNull($builder, $value, $boolean, true);
    }

    /**
     * Add a where in condition from this field.
     *
     * @param  LaramoreBuilder    $builder
     * @param  LaramoreCollection $value
     * @param  string             $boolean
     * @param  boolean            $notIn
     * @return LaramoreBuilder
     */
    public function whereIn(LaramoreBuilder $builder, LaramoreCollection $value=null,
                            string $boolean='and', bool $notIn=false): LaramoreBuilder
    {
        $builder = $this->getField('type')->addBuilderOperation($builder, 'whereNull', $boolean, $not);

        return $this->getField('id')->addBuilderOperation($builder, 'whereIn', $value, $boolean, $notIn);
    }

    /**
     * Add a where not in condition from this field.
     *
     * @param  LaramoreBuilder    $builder
     * @param  LaramoreCollection $value
     * @param  string             $boolean
     * @return LaramoreBuilder
     */
    public function whereNotIn(LaramoreBuilder $builder, LaramoreCollection $value=null, string $boolean='and'): LaramoreBuilder
    {
        return $this->whereIn($builder, $value, $boolean, true);
    }

    /**
     * Add a where condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  OperatorElement $operator
     * @param  mixed           $value
     * @param  string          $boolean
     * @return LaramoreBuilder
     */
    public function where(LaramoreBuilder $builder, OperatorElement $operator,
                          $value=null, string $boolean='and'): LaramoreBuilder
    {
        if ($operator->needs === 'collection') {
            return $this->whereIn($builder, $value, $boolean, ($operator === Operator::notIn()));
        }

        return $this->getField('type')->addBuilderOperation($builder, 'where', $operator, $this->getValue('type', $value), $boolean);
        return $this->getField('id')->addBuilderOperation($builder, 'where', $operator, $this->getValue('id', $value), $boolean);
    }

    /**
     * Return the set value for a specific field.
     *
     * @param Field         $field
     * @param LaramoreModel|array|\ArrayAccess $model
     * @param mixed         $value
     * @return mixed
     */
    public function setFieldValue(Field $field, $model, $value)
    {
        if ($field->has($model) && $field->get($model) !== $value) {
            $this->reset($model);
        }

        return parent::setFieldValue($field, $model, $value);
    }
}
