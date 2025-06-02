<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MetaFramework\Interfaces\GooglePlacesInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaceAddress extends Model implements GooglePlacesInterface
{
    use HasFactory;

    protected $guarded = [];

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
