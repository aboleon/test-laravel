<?php

namespace App\Models\EventManager\Sellable;

use App\Models\EventContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventContactSellableServiceChoosable extends Model
{
    use HasFactory;

    protected $table = 'event_contact_sellable_service_choosables';
    protected $guarded = [];

    public function choosable(): BelongsTo
    {
        return $this->belongsTo(Choosable::class);
    }

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class);
    }

}
