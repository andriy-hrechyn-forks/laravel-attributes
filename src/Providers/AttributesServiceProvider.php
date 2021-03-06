<?php

declare(strict_types=1);

namespace Rinvex\Attributes\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Rinvex\Attributes\Models\Attribute;
use Rinvex\Attributes\Models\AttributeEntity;
use Rinvex\Attributes\Console\Commands\MigrateCommand;
use Rinvex\Attributes\Console\Commands\PublishCommand;
use Rinvex\Attributes\Console\Commands\RollbackCommand;
use Rinvex\Attributes\Traits\ConsoleTools;

class AttributesServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.rinvex.attributes.migrate',
        PublishCommand::class => 'command.rinvex.attributes.publish',
        RollbackCommand::class => 'command.rinvex.attributes.rollback',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.attributes');

        // Bind eloquent models to IoC container
        $this->app->singleton('attributes.model', $attributeyModel = $this->app['config']['rinvex.attributes.models.attribute']);
        $attributeyModel === Attribute::class || $this->app->alias('attributes.model', Attribute::class);

        $this->app->singleton('attributes.entity', $attributeEntityModel = $this->app['config']['rinvex.attributes.models.attribute_entity']);
        $attributeEntityModel === AttributeEntity::class || $this->app->alias('attributes.entity', AttributeEntity::class);

        // Register attributes entities
        $this->app->singleton('attributes.entities', function ($app) {
            return collect();
        });

        // Register console commands
        $this->registerCommands($this->commands);
    }

    public function boot()
    {
        // Publish Resources
        $this->publishesConfig('andriy-hrechyn-forks/laravel-attributes');
        $this->publishesMigrations('andriy-hrechyn-forks/laravel-attributes');
        ! $this->autoloadMigrations('andriy-hrechyn-forks/laravel-attributes') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Add strip_tags validation rule
        Validator::extend('strip_tags', function ($attribute, $value) {
            return strip_tags($value) === $value;
        }, trans('validation.invalid_strip_tags'));

        // Add time offset validation rule
        Validator::extend('timeoffset', function ($attribute, $value) {
            return array_key_exists($value, timeoffsets());
        }, trans('validation.invalid_timeoffset'));
    }
}
