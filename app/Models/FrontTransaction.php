<?php

namespace App\Models;

use App\Enum\OrderOrigin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string    $num_trans
 * @property string    $num_appel
 * @property int $order_id
 */
class FrontTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'front_transactions';
    protected $guarded = [];

    protected $casts = [
      'details' => 'array'
    ];

    public function paymentCall(): BelongsTo
    {
        return $this->belongsTo(PaymentCall::class, 'payment_call_id');
    }

    public function storedPayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'transaction_id')->where('transaction_origin', OrderOrigin::FRONT->value);
    }
}
