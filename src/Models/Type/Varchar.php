<?php

declare(strict_types=1);

namespace Rinvex\Attributes\Models\Type;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Attributes\Models\Attribute;
use Rinvex\Attributes\Models\Value;

/**
 * Rinvex\Attributes\Models\Type\Varchar.
 *
 * @property int                  $id
 * @property string               $content
 * @property int                  $attribute_id
 * @property int                  $entity_id
 * @property string               $entity_type
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property-read Attribute       $attribute
 * @property-read Model|\Eloquent $entity
 *
 * @method static Builder|Varchar whereAttributeId($value)
 * @method static Builder|Varchar whereContent($value)
 * @method static Builder|Varchar whereCreatedAt($value)
 * @method static Builder|Varchar whereEntityId($value)
 * @method static Builder|Varchar whereEntityType($value)
 * @method static Builder|Varchar whereId($value)
 * @method static Builder|Varchar whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Varchar extends Value
{
    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'content'      => 'string',
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

        $this->setTable('attribute_varchar_values');
        $this->setRules([
            'content'      => 'required|string|max:150',
            'attribute_id' => 'required|integer|exists:attributes,id',
            'entity_id'    => 'required|integer',
            'entity_type'  => 'required|string|strip_tags|max:150',
        ]);
    }
}
