<?php

namespace App\Models\Concerns;

/**
 * Models stored as a single row (site-wide settings edited from the admin).
 */
trait IsSingleton
{
    public static function current(): static
    {
        $model = static::query()->firstOrCreate([]);

        // A fresh row needs a reload to expose the column defaults set by the database.
        return $model->wasRecentlyCreated ? $model->refresh() : $model;
    }
}
