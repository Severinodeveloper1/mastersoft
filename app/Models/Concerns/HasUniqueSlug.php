<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Fills the `slug` attribute from `name` when empty, appending -2, -3... on collisions.
 */
trait HasUniqueSlug
{
    protected static function bootHasUniqueSlug(): void
    {
        static::saving(function ($model): void {
            if (blank($model->slug)) {
                $model->slug = static::uniqueSlugFor($model->name, $model->getKey());
            }
        });
    }

    protected static function uniqueSlugFor(string $name, mixed $ignoreKey = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreKey, fn ($query) => $query->whereKeyNot($ignoreKey))
            ->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
