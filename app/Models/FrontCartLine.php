<?php

namespace App\Models;

use App\Models\Order\StockTemp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MetaFramework\Casts\PriceInteger;

/**
 * @property int $front_cart_id
 * @property string $shoppable_type
 * @property int $shoppable_id
 * @property int $unit_ttc
 * @property int $quantity
 * @property float|int $total_net
 * @property float|int $total_ttc
 * @property int $vat_id
 * @property array|mixed $meta_info
 */
class FrontCartLine extends Model
{
    use HasFactory;
    protected $table = 'front_cart_lines';
    protected $guarded = [];
    protected $casts = [
        'meta_info' => 'array',
        'total_net' => PriceInteger::class,
        'total_ttc' => PriceInteger::class,
        'total_pec' => PriceInteger::class,
        'unit_ttc' => PriceInteger::class,
    ];


    public function cart(): BelongsTo
    {
        return $this->belongsTo(FrontCart::class, 'front_cart_id');
    }

    public function vat(): BelongsTo
    {
        return $this->belongsTo(Vat::class);
    }

    public function shoppable(): MorphTo
    {
        return $this->morphTo();
    }

    public function tempStock(): HasOne
    {
        return $this->hasOne(StockTemp::class, 'frontcartline_id');
    }


}
