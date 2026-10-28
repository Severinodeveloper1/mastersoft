<?php

namespace App\Models;

use App\Enums\CustomerType;
use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentPreference;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Order extends Model implements Auditable
{
    /** @use HasFactory<OrderFactory> */
    use AuditableTrait, HasFactory;

    protected $fillable = [
        'status',
        'customer_type',
        'first_name',
        'last_name',
        'document_number',
        'ruc',
        'business_name',
        'contact_name',
        'phone',
        'email',
        'delivery_method',
        'department',
        'province',
        'district',
        'address',
        'reference',
        'payment_preference',
        'notes',
        'subtotal',
        'tax',
        'total',
        'privacy_accepted_at',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'customer_type' => CustomerType::class,
            'delivery_method' => DeliveryMethod::class,
            'payment_preference' => PaymentPreference::class,
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'privacy_accepted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // The public code derives from the id, so it is assigned right after insert.
        static::created(function (Order $order): void {
            $order->code = 'MS-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
            $order->saveQuietly();
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customerName(): string
    {
        return $this->customer_type === CustomerType::Company
            ? (string) $this->business_name
            : trim("{$this->first_name} {$this->last_name}");
    }
}
