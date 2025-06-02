<?php

namespace App\Models\EventManager\Sellable;

use App\Casts\SellablePriceDateTime;
use App\Models\EventManager\Sellable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFramework\Casts\PriceInteger;

class Price extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'event_sellable_service_prices';
    protected $casts = [
        'price' => PriceInteger::class,
        'ends' => SellablePriceDateTime::class,
    ];

    protected $fillable = [
        'event_sellable_service_id',
        'ends',
        'price'
    ];

    public function sellable(): BelongsTo
    {
        return $this->belongsTo(Sellable::class, 'event_sellable_service_id');
    }

    public function shoppableVat()
    {
        return $this->sellable->vat_id;
    }

    public function shoppablePrice(): float|int
    {
        return $this->price;
    }

    public function shoppableTitle(): string
    {
        return $this->sellable->title;
    }
}
