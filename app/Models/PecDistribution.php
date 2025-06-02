<?php

namespace App\Models;

use App\Models\EventManager\Grant\Grant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use MetaFramework\Casts\PriceInteger;

/**
 * @property Event $event
 * @property EventContact $eventContact
 */
class PecDistribution extends Model
{
    use HasFactory;

    public mixed $total;
    protected $table = 'pec_distribution';

    protected $fillable
        = [
            'grant_id',
            'event_contact_id',
            'front_cart_id',
            'type',
            'unit_price',
            'quantity',
            'total_net',
            'total_vat',
            'vat_id',
            'shoppable_id',
        ];

    protected $casts
        = [
            'unit_price' => PriceInteger::class,
            'total_net'  => PriceInteger::class,
            'total_vat'  => PriceInteger::class,
        ];

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'event_contact_id');
    }

    public function event(): HasOneThrough
    {
        return $this->hasOneThrough(
            Event::class,
            EventContact::class,
            'id',
            'id',
            'event_contact_id',
            'event_id',
        );
    }

    public function grant(): BelongsTo
    {
        return $this->belongsTo(Grant::class, 'grant_id');
    }
}

