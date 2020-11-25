<?php

declare(strict_types=1);

namespace Rinvex\Attributes\Models\Type;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Attributes\Models\Attribute;
use Rinvex\Attributes\Models\Value;

/**
 * Rinvex\Attributes\Models\Type\Integer.
 *
 * @property int                  $id
 * @property int                  $content
 * @property int                  $attribute_id
 * @property int                  $entity_id
 * @property string               $entity_type
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property-read Attribute       $attribute
 * @property-read Model|\Eloquent $entity
 *
 * @method static Builder|Integer whereAttributeId($value)
 * @method static Builder|Integer whereContent($value)
 * @method static Builder|Integer whereCreatedAt($value)
 * @method static Builder|Integer whereEntityId($value)
 * @method static Builder|Integer whereEntityType($value)
 * @method static Builder|Integer whereId($value)
 * @method static Builder|Integer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Integer extends Value
{
    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'content'      => 'integer',
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

        $this->setTable('attribute_integer_values');
        $this->setRules([
            'content'      => 'required|integer',
            'attribute_id' => 'required|integer|exists:attributes,id',
            'entity_id'    => 'required|integer',
            'entity_type'  => 'required|string|strip_tags|max:150',
        ]);
    }
}
