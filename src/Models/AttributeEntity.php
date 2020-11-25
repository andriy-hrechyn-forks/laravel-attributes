<?php

declare(strict_types=1);

namespace Rinvex\Attributes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Rinvex\Attributes\Models\AttributeEntity.
 *
 * @property int         $attribute_id
 * @property string      $entity_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|AttributeEntity whereAttributeId($value)
 * @method static Builder|AttributeEntity whereCreatedAt($value)
 * @method static Builder|AttributeEntity whereEntityType($value)
 * @method static Builder|AttributeEntity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributeEntity extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'attribute_entity';

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'entity_type',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'entity_type' => 'string',
    ];
}
