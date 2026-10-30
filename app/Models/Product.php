<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements Auditable, HasMedia
{
    /** @use HasFactory<ProductFactory> */
    use AuditableTrait, HasFactory, HasUniqueSlug, InteractsWithMedia;

    protected $fillable = [
        'external_id',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'summary',
        'description',
        'information',
        'price',
        'regular_price',
        'stock',
        'unit',
        'is_active',
        'is_featured',
        'external_image_urls',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'regular_price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'deactivated_by_sync' => 'boolean',
            'is_featured' => 'boolean',
            'external_image_urls' => 'array',
        ];
    }

    /**
     * ERP image URLs first, then images uploaded in the admin.
     *
     * @return list<string>
     */
    protected static function booted(): void
    {
        // Any visibility change saved through the model (admin toggle or form, or the sync itself) is a
        // deliberate choice; only the sync's bulk deactivation, which skips model events, sets the mark.
        static::saving(function (Product $product): void {
            if ($product->exists && $product->isDirty('is_active')) {
                $product->deactivated_by_sync = false;
            }
        });
    }

    public function imageUrls(): array
    {
        return [
            ...($this->external_image_urls ?? []),
            ...$this->getMedia('images')->map(fn ($media) => $media->getUrl())->all(),
        ];
    }

    public function primaryImageUrl(): ?string
    {
        return $this->external_image_urls[0] ?? ($this->getFirstMediaUrl('images') ?: null);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Products a visitor can see and order: active and with stock.
     */
    public function scopeVisibleInStore(Builder $query): void
    {
        $query->where('is_active', true)->where('stock', '>', 0);
    }

    /**
     * An offer exists only when the manual regular price is greater than the current price.
     */
    public function isOnSale(): bool
    {
        return $this->regular_price !== null
            && self::toCents($this->regular_price) > self::toCents($this->price);
    }

    public function savings(): ?string
    {
        if (! $this->isOnSale()) {
            return null;
        }

        $cents = self::toCents($this->regular_price) - self::toCents($this->price);

        return number_format($cents / 100, 2, '.', '');
    }

    public function discountPercentage(): ?int
    {
        if (! $this->isOnSale()) {
            return null;
        }

        $regular = self::toCents($this->regular_price);

        return (int) round(($regular - self::toCents($this->price)) * 100 / $regular);
    }

    private static function toCents(string|int|float $amount): int
    {
        return (int) round((float) $amount * 100);
    }
}
