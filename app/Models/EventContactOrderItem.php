<?php

namespace App\Models;

use App\Actions\EventManager\SellableService;
use App\Models\EventManager\Sellable\Price;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventContactOrderItem extends Model
{
    use HasFactory;

    protected $table = 'events_contacts_orders_items';

    protected $fillable = [
        'order_id',
        'event_sellable_service_id',
        'quantity',
        'unit_price',
        'total_price',
        'total_price_without_tax',
        'tax_amount',
        'event_sellable_service_price_id',
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(EventContactOrder::class, 'order_id');
    }

    /**
     * Get the service associated with the order item.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(SellableService::class, 'event_sellable_service_id');
    }

    /**
     * Get the specific service price used for this item.
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class, 'event_sellable_service_price_id');
    }
}
