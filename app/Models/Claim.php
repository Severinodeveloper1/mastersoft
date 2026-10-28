<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use App\Enums\DocumentType;
use App\Enums\GoodType;
use Database\Factories\ClaimFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * A sheet of the virtual Libro de Reclamaciones.
 */
class Claim extends Model implements Auditable
{
    /** @use HasFactory<ClaimFactory> */
    use AuditableTrait, HasFactory;

    public const RESPONSE_BUSINESS_DAYS = 15;

    protected $fillable = [
        'document_type',
        'document_number',
        'first_name',
        'paternal_surname',
        'maternal_surname',
        'phone',
        'email',
        'department',
        'province',
        'district',
        'address',
        'is_minor',
        'guardian_name',
        'guardian_document',
        'good_type',
        'order_reference',
        'claimed_amount',
        'good_description',
        'claim_type',
        'detail',
        'consumer_request',
        'privacy_accepted_at',
        'email_notification_accepted',
        'status',
        'provider_response',
        'responded_at',
    ];

    protected $attributes = [
        'status' => 'received',
    ];

    protected function casts(): array
    {
        return [
            'status' => ClaimStatus::class,
            'document_type' => DocumentType::class,
            'good_type' => GoodType::class,
            'claim_type' => ClaimType::class,
            'is_minor' => 'boolean',
            'claimed_amount' => 'decimal:2',
            'privacy_accepted_at' => 'datetime',
            'email_notification_accepted' => 'boolean',
            'response_due_on' => 'date',
            'responded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Claim $claim): void {
            $filedAt = Carbon::now();

            $claim->year = $filedAt->year;
            $claim->number = static::nextNumberFor($filedAt->year);
            $claim->code = str_pad((string) $claim->number, 6, '0', STR_PAD_LEFT).'-'.$filedAt->year;
            $claim->response_due_on = static::addBusinessDays($filedAt, self::RESPONSE_BUSINESS_DAYS);
        });
    }

    /**
     * Correlative per year. The unique (year, number) index rejects a concurrent duplicate.
     */
    protected static function nextNumberFor(int $year): int
    {
        return (int) DB::table('claims')->where('year', $year)->max('number') + 1;
    }

    /**
     * Counts Monday to Friday only; national holidays are not modeled.
     */
    public static function addBusinessDays(Carbon $from, int $days): Carbon
    {
        $date = $from->copy()->startOfDay();

        while ($days > 0) {
            $date->addDay();

            if (! $date->isWeekend()) {
                $days--;
            }
        }

        return $date;
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->paternal_surname} {$this->maternal_surname}");
    }
}
