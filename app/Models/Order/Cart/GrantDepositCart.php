<?php

namespace App\Models\Order\Cart;

use App\Models\EventContact;
use App\Models\EventManager\Grant\Grant;
use App\Models\Vat;
use App\Traits\Orderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFramework\Casts\PriceInteger;

class GrantDepositCart extends Model
{
    use HasFactory;
    use Orderable;
    protected $table = 'order_cart_grant_deposit';

    protected $guarded = [];

    protected $casts = [
        'unit_price' => PriceInteger::class,
        'total_net' => PriceInteger::class,
        'total_vat' => PriceInteger::class,
    ];

    public function grant(): BelongsTo
    {
        return self::belongsTo(Grant::class, 'event_grant_id');
    }

    public function vat(): BelongsTo
    {
        return $this->belongsTo(Vat::class, 'vat_id');
    }

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'event_contact_id');
    }
}
