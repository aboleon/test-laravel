<?php

namespace App\Models\EventManager\Sellable;

use App\Interfaces\ShoppableInterface;
use App\Models\EventManager\Grant\GrantDepositLocation;
use App\Models\EventManager\Sellable;
use App\Models\ParticipationType;
use App\Models\Vat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MetaFramework\Accessors\VatAccessor;
use MetaFramework\Casts\PriceInteger;

class Deposit extends Model implements ShoppableInterface
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'event_sellable_service_deposits';
    protected $casts = [
        'amount' => PriceInteger::class
    ];

    protected $fillable = [
        'amount',
        'vat_id',
    ];

    public function sellable(): BelongsTo
    {
        return $this->belongsTo(Sellable::class, 'event_sellable_service_id');
    }

    public function shoppableVat()
    {
        return VatAccessor::defaultRate();
    }

    public function vat(): BelongsTo
    {
        return $this->belongsTo(Vat::class);
    }

    public function shoppablePrice()
    {
        return $this->amount;
    }

    public function shoppableTitle(): string
    {
        return 'Caution pour ' . $this->sellable->title;
    }
}
