<?php

namespace App\Models\Order\Cart;

use App\Models\EventManager\Sellable;
use App\Models\Order\Attribution;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAttribution extends Attribution
{
    use HasFactory;


    public function service(): BelongsTo
    {
        return $this->belongsTo(Sellable::class, 'shoppable_id');
    }

    public function shoppable(): BelongsTo
    {
        return $this->service();
    }
}
