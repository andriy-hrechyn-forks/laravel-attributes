<?php

declare(strict_types=1);

namespace Rinvex\Attributes\Models\Type;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Attributes\Models\Attribute;
use Rinvex\Attributes\Models\Value;

/**
 * Rinvex\Attributes\Models\Type\Datetime.
 *
 * @property int                  $id
 * @property Carbon               $content
 * @property int                  $attribute_id
 * @property int                  $entity_id
 * @property string               $entity_type
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property-read Attribute       $attribute
 * @property-read Model|\Eloquent $entity
 *
 * @method static Builder|Datetime whereAttributeId($value)
 * @method static Builder|Datetime whereContent($value)
 * @method static Builder|Datetime whereCreatedAt($value)
 * @method static Builder|Datetime whereEntityId($value)
 * @method static Builder|Datetime whereEntityType($value)
 * @method static Builder|Datetime whereId($value)
 * @method static Builder|Datetime whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Datetime extends Value
{
    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'content'      => 'datetime',
        'attribute_id' => 'integer',
        'entity_id'    => 'integer',
        'entity_type'  => 'string',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable('attribute_datetime_values');
        $this->setRules([
            'content'      => 'required|date',
            'attribute_id' => 'required|integer|exists:attributes,id',
            'entity_id'    => 'required|integer',
            'entity_type'  => 'required|string|strip_tags|max:150',
        ]);
    }
}
