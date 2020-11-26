<?php

declare(strict_types=1);

namespace Rinvex\Attributes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Rinvex\Attributes\Support\ValueCollection;
use Rinvex\Attributes\Traits\HasSlug;
use Rinvex\Attributes\Traits\HasTranslations;
use Rinvex\Attributes\Traits\ValidatingTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\SlugOptions;

/**
 * Rinvex\Attributes\Models\Attribute.
 *
 * @property int                          $id
 * @property string                       $slug
 * @property array                        $name
 * @property array                        $description
 * @property int                          $sort_order
 * @property string                       $group
 * @property string                       $type
 * @property bool                         $is_required
 * @property bool                         $is_collection
 * @property string                       $default
 * @property Carbon|null                  $created_at
 * @property Carbon|null                  $updated_at
 * @property array                        $entities
 * @property-read ValueCollection|Value[] $values
 *
 * @method static Builder|Attribute ordered($direction = 'asc')
 * @method static Builder|Attribute whereCreatedAt($value)
 * @method static Builder|Attribute whereDefault($value)
 * @method static Builder|Attribute whereDescription($value)
 * @method static Builder|Attribute whereGroup($value)
 * @method static Builder|Attribute whereId($value)
 * @method static Builder|Attribute whereIsCollection($value)
 * @method static Builder|Attribute whereIsRequired($value)
 * @method static Builder|Attribute whereSlug($value)
 * @method static Builder|Attribute whereSortOrder($value)
 * @method static Builder|Attribute whereName($value)
 * @method static Builder|Attribute whereType($value)
 * @method static Builder|Attribute whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Attribute extends Model implements Sortable
{
    use HasSlug;
    use SortableTrait;
    use HasTranslations;
    use ValidatingTrait;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'group',
        'type',
        'is_required',
        'is_collection',
        'default',
        'entities',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'slug'          => 'string',
        'sort_order'    => 'integer',
        'group'         => 'string',
        'type'          => 'string',
        'is_required'   => 'boolean',
        'is_collection' => 'boolean',
        'default'       => 'string',
    ];

    /**
     * {@inheritdoc}
     */
    protected $observables = [
        'validating',
        'validated',
    ];

    /**
     * {@inheritdoc}
     */
    public $translatable = [
        'name',
        'description',
    ];

    /**
     * {@inheritdoc}
     */
    public $sortable = [
        'order_column_name' => 'sort_order',
    ];

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Whether the model should throw a
     * ValidationException if it fails validation.
     *
     * @var bool
     */
    protected $throwValidationExceptions = true;

    /**
     * An array to map class names to their type names in database.
     *
     * @var array
     */
    protected static $typeMap = [];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable('attributes');
        $this->setRules([
            'name'          => 'required|string|strip_tags|max:150',
            'description'   => 'nullable|string|max:10000',
            'slug'          => 'required|alpha_dash|max:150|unique:attributes,slug',
            'sort_order'    => 'nullable|integer|max:10000',
            'group'         => 'nullable|string|strip_tags|max:150',
            'type'          => 'required|string|strip_tags|max:150',
            'is_required'   => 'sometimes|boolean',
            'is_collection' => 'sometimes|boolean',
            'default'       => 'nullable|string|strip_tags|max:10000',
        ]);
    }

    /**
     * Enforce clean slugs.
     *
     * @param string $value
     *
     * @return void
     */
    public function setSlugAttribute($value): void
    {
        $this->attributes['slug'] = Str::slug($value, $this->getSlugOptions()->slugSeparator,
            $this->getSlugOptions()->slugLanguage);
    }

    /**
     * Set or get the type map for attribute types.
     *
     * @param array|null $map
     * @param bool       $merge
     *
     * @return array
     */
    public static function typeMap(array $map = null, $merge = true)
    {
        if (is_array($map)) {
            static::$typeMap = $merge && static::$typeMap
                ? $map + static::$typeMap : $map;
        }

        return static::$typeMap;
    }

    /**
     * Get the model associated with a custom attribute type.
     *
     * @param string $alias
     *
     * @return string|null
     */
    public static function getTypeModel($alias)
    {
        return self::$typeMap[$alias] ?? null;
    }

    /**
     * Access entities relation and retrieve entity types as an array,
     * Accessors/Mutators preceeds relation value when called dynamically.
     *
     * @return array
     */
    public function getEntitiesAttribute(): array
    {
        return $this->entities()->pluck('entity_type')->toArray();
    }

    /**
     * Set the attribute attached entities.
     *
     * @param mixed $entities
     *
     * @return void
     */
    public function setEntitiesAttribute($entities): void
    {
        static::saved(function ($model) use ($entities) {
            $this->entities()->delete();
            !$entities || $this->entities()->createMany(array_map(function ($entity) {
                return ['entity_type' => $entity];
            }, $entities));
        });
    }

    /**
     * Get the options for generating the slug.
     *
     * @return SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->usingSeparator('_')
            ->doNotGenerateSlugsOnUpdate()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the entities attached to this attribute.
     *
     * @return HasMany
     */
    public function entities(): HasMany
    {
        return $this->hasMany(AttributeEntity::class, 'attribute_id', 'id');
    }

    /**
     * Get the entities attached to this attribute.
     *
     * @param string $value
     *
     * @return HasMany
     */
    public function values(string $value): HasMany
    {
        return $this->hasMany($value, 'attribute_id', 'id');
    }
}
