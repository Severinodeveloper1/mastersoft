<?php

namespace App\Models;

use Database\Factories\LegalPageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class LegalPage extends Model implements Auditable
{
    /** @use HasFactory<LegalPageFactory> */
    use AuditableTrait, HasFactory;

    public const PRIVACY = 'politicas-de-privacidad';

    public const TERMS = 'terminos-y-condiciones';

    protected $fillable = [
        'slug',
        'title',
        'content',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
