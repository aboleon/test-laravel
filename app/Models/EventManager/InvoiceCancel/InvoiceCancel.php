<?php

namespace App\Models\EventManager\InvoiceCancel;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFramework\Casts\PriceInteger;

class InvoiceCancel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'order_invoices_cancels';

    protected $casts = [
        "date" => "date",
        "price_after_tax" => PriceInteger::class,
        "price_before_tax" => PriceInteger::class,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
