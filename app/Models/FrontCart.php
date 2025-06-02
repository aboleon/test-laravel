<?php

namespace App\Models;

use App\Actions\Order\PecActionsFront;
use App\Actions\Order\StockActions;
use App\Models\EventManager\Sellable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property Collection       $lines (hasMany FrontCartLine)
 * @property int|null         $group_manager_event_contact_id
 * @property int              $event_contact_id
 * @property int              $id
 * @property string           $updated_at
 * @property int|null         $is_group_order
 * @property int|null         $pec_eligible
 * @property string           $pec
 * @property FrontTransaction $transaction
 * @property int|null         $order_id
 * @property PaymentCall      $paymentCall
 * @property EventContact     $groupManager
 */
class FrontCart extends Model
{
    use HasFactory;

    protected $table = 'front_carts';
    protected $fillable
        = [
            'event_contact_id',
            'order_id',
            'group_manager_event_contact_id',
            'is_group_order',
            'pec_eligible',
            'pec',
        ];

    public static $shouldReplenishStock = true;


    protected static function boot()
    {
        parent::boot();

        static::deleting(function (FrontCart $cart) {
            if (self::$shouldReplenishStock) {
                self::replenishStock($cart);
            }
        });
    }

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'event_contact_id');
    }

    public function groupManager(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'group_manager_event_contact_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(FrontCartLine::class);
    }


    public static function setReplenishStock(bool $value): void
    {
        self::$shouldReplenishStock = $value;
    }

    public static function resetReplenishStock(): void
    {
        self::$shouldReplenishStock = true;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected static function replenishStock(FrontCart $cart)
    {
        $cart->lines()->where('shoppable_type', Sellable::class)->get()->each(function ($line) {
            $line->shoppable->stock += $line->quantity;
            $line->shoppable->save();
        });

        $cart->lines()->each(function (FrontCartLine $line) {
            StockActions::clearFrontTempStock($line);
        });
    }

    public function getPecPackage(): null|PecActionsFront
    {
        if (is_null($this->pec)) {
            return null;
        }

        return unserialize($this->pec);
    }

    public function isPecEligible(): bool
    {
        return $this->pec_eligible;
    }

    public function paymentCall(): HasOne
    {
        return $this->hasOne(PaymentCall::class, 'cart_id');
    }

    public function transaction(): HasOneThrough
    {
        return $this->hasOneThrough(
            FrontTransaction::class,  // The final model
            PaymentCall::class,       // The intermediate model
            'cart_id',                // Foreign key on PaymentCall (cart_id)
            'payment_call_id',        // Foreign key on FrontTransaction (payment_call_id)
            'id',                     // Local key on FrontCart (id)
            'id',                      // Local key on PaymentCall (id)
        );
    }


}
