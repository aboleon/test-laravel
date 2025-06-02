<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The EventContact class, aka participant.
 */
class EventContactOrder extends Model
{
    use HasFactory;

    protected $table = 'events_contacts_orders';
    protected $fillable = [
        'events_contacts_id',
        'order_number',
        'order_date',
        'total_price',
        'tax_amount',
        'total_without_tax',
        'amount_paid',
    ];

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(EventContactOrderItem::class, 'order_id');
    }

    /**
     * Get the contact (or buyer) associated with the order.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'events_contacts_id');
    }
}
