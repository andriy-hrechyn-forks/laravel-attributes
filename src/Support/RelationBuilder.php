<?php

declare(strict_types=1);

namespace Rinvex\Attributes\Support;

use Closure;
use Rinvex\Attributes\Models\Attribute;
use Illuminate\Database\Eloquent\Model as Entity;

class RelationBuilder
{
    /**
     * Build the relations for the entity attributes.
     *
     * @param Entity $entity
     *
     * @return void
     */
    public function build(Entity $entity): void
    {
        $attributes = $entity->getEntityAttributes();

        // We will manually add a relationship for every attribute registered
        // of this entity. Once we know the relation method we have to use,
        // we will just add it to the entityAttributeRelations property.
        foreach ($attributes as $attribute) {
            $relation = $this->getRelationClosure($entity, $attribute);

            $entity->setEntityAttributeRelation((string) ($attribute->getAttributes()['slug'] ?? null), $relation);
        }
    }

    /**
     * Generate the entity attribute relation closure.
     *
     * @param Entity    $entity
     * @param Attribute $attribute
     *
     * @return Closure
     */
    protected function getRelationClosure(Entity $entity, Attribute $attribute): Closure
    {
        $method = (bool) ($attribute->getAttributes()['is_collection'] ?? null) ? 'hasMany' : 'hasOne';

        // This will return a closure fully binded to the current entity instance,
        // which will help us to simulate any relation as if it was made in the
        // original entity class definition using a function statement.
        return Closure::bind(function () use ($entity, $attribute, $method) {
            $relation = $entity->{$method}(Attribute::getTypeModel($attribute->getAttribute('type')), 'entity_id', $entity->getKeyName());

            // Since an attribute could be attached to multiple entities, then values could have
            // same entity ID, but for different entity types, so we need to add type where
            // clause to fetch only values related to the given entity ID + entity type.
            $relation->where('entity_type', $entity->getMorphClass());

            // We add a where clause in order to fetch only the elements that are
            // related to the given attribute. If no condition is set, it will
            // fetch all the value rows related to the current entity.
            return $relation->where($attribute->getForeignKey(), $attribute->getKey());
        }, $entity, get_class($entity));
    }
}
