<?php

namespace App\Models;

use App\Enum\OrderOrigin;
use App\Models\Order\Refund;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use MetaFramework\Casts\PriceInteger;

/**
 * @property int|mixed $order_id
 * @property string    $reimbursed_at //timestamp
 * @property array     $log           //json
 */
class Payment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "order_payments";
    protected $fillable
        = [
            'order_id',
            'transaction_id',
            'transaction_origin',
            'date',
            'amount',
            'payment_method',
            'authorization_number',
            'card_number',
            'bank',
            'issuer',
            'check_number',
            'reimbursed_at',
            'log',
        ];
    protected $casts
        = [
            'date'   => 'date',
            'amount' => PriceInteger::class,
            'log'    => 'array',
        ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }


    public function frontTransaction(): HasOne
    {
        return $this->hasOne(FrontTransaction::class, 'id', 'transaction_id');
    }

    public function paymentTransaction(): HasOne
    {
        return $this->hasOne(PaymentTransaction::class, 'id', 'transaction_id');
    }

    public function getTransactionAttribute()
    {
        if ($this->transaction_origin == OrderOrigin::FRONT->value) {
            return $this->frontTransaction;
        }

        return $this->paymentTransaction;
    }

    /**
     * @return MorphMany
     * Refund requests to Paybox
     */
    public function refundRequests(): MorphMany
    {
        return $this->morphMany(PayboxReimbursementRequest::class, 'shoppable');
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class, 'payment_id');
    }

}
