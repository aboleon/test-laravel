<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EventContactToken extends Model
{
    public $timestamps = false;

    protected $fillable
        = [
            'event_contact_id',
            'token',
            'generated_at',
            'validated_at',
        ];

    protected $casts
        = [
            'generated_at' => 'timestamp',
            'validated_at' => 'timestamp',
        ];

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model) {
            $model->token = Str::uuid();
        });
    }

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class);
    }
}
