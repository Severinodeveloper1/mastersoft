<?php

namespace App\Models;

use App\Models\Concerns\IsSingleton;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CompanySetting extends Model implements Auditable, HasMedia
{
    use AuditableTrait, InteractsWithMedia, IsSingleton;

    protected $fillable = [
        'name',
        'legal_name',
        'ruc',
        'description',
        'phone',
        'whatsapp_number',
        'email',
        'claims_email',
        'address',
        'opening_hours',
        'map_embed_url',
        'youtube_url',
        'facebook_url',
        'linkedin_url',
        'instagram_url',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    /**
     * Digits-only number for wa.me links (e.g. "51942123456").
     */
    public function whatsappDigits(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->whatsapp_number);

        return $digits !== '' ? $digits : null;
    }

    /**
     * wa.me link to the store's WhatsApp, optionally with a prefilled message; null when no number is set.
     */
    public function whatsappUrl(?string $message = null): ?string
    {
        $digits = $this->whatsappDigits();

        if ($digits === null) {
            return null;
        }

        return "https://wa.me/{$digits}".($message !== null ? '?text='.rawurlencode($message) : '');
    }
}
