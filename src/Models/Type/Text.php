<?php

declare(strict_types=1);

namespace Rinvex\Attributes\Models\Type;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Attributes\Models\Attribute;
use Rinvex\Attributes\Models\Value;

/**
 * Rinvex\Attributes\Models\Type\Text.
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
 * @method static Builder|Text whereAttributeId($value)
 * @method static Builder|Text whereContent($value)
 * @method static Builder|Text whereCreatedAt($value)
 * @method static Builder|Text whereEntityId($value)
 * @method static Builder|Text whereEntityType($value)
 * @method static Builder|Text whereId($value)
 * @method static Builder|Text whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Text extends Value
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

        $this->setTable('attribute_text_values');
        $this->setRules([
            'content'      => 'required|string|max:10000',
            'attribute_id' => 'required|integer|exists:attributes,id',
            'entity_id'    => 'required|integer',
            'entity_type'  => 'required|string|strip_tags|max:150',
        ]);
    }
}
