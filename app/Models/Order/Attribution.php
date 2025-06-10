<?php

namespace App\Models\Order;

use App\Accessors\Front\Sellable\Accommodation;
use App\Enum\OrderCartType;
use App\Models\EventContact;
use App\Models\EventManager\Sellable;
use App\Models\Order;
use App\Models\Order\Cart\AccommodationCart;
use App\Models\Order\Cart\ServiceCart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribution extends Model
{
    use HasFactory;

    protected $table = 'order_attributions';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'event_contact_id',
        'shoppable_type',
        'shoppable_id',
        'quantity',
        'assigned_by',
        'configs',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'configs' => 'array',
    ];

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'event_contact_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function shoppable(): BelongsTo
    {
        return match($this->shoppable_type) {
            OrderCartType::ACCOMMODATION->value => $this->belongsTo(Sellable::class, 'shoppable_id'),
            default => $this->belongsTo(Sellable::class, 'shoppable_id')
        };
    }

    /**
     * Get the service cart item for this attribution
     * This finds the ServiceCart record that matches the same order and service
     */
    public function serviceCart(): BelongsTo
    {
        return $this->belongsTo(ServiceCart::class, 'shoppable_id', 'service_id')
            ->where('order_cart_service.order_id', $this->order_id);
    }

    /**
     * Get the accommodation cart item for this attribution
     * This finds the ServiceCart record that matches the same order and accommodation
     */
    public function accommodationCart(): BelongsTo
    {
        return $this->belongsTo(AccommodationCart::class, 'shoppable_id', 'room_id')
            ->where('order_cart_accommodation.order_id', $this->order_id);
    }

    public function cart(): BelongsTo
    {
        return match($this->shoppable_type) {
            OrderCartType::ACCOMMODATION->value => $this->accommodationCart(),
            default => $this->serviceCart()
        };

    }

}
