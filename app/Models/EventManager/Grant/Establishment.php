<?php

namespace App\Models\EventManager\Grant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Establishment extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'event_grant_establishments';
    protected $guarded = [];

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Establishment::class, 'establishment_id');
    }

}
