<?php

namespace App\DataTables\View;

use App\Enum\OrderStatus;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Order\Refund;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderView extends Model
{
    protected $table = 'orders_view';
    public $timestamps = false;


    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'order_id');
    }

    public function invoice(): ?Invoice
    {
        return $this->invoices->whereNull('proforma')->first();
    }

    public function proforma(): EloquentCollection
    {
        return $this->invoices->whereNotNull('proforma');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id');
    }

    public function isUnpaid(): bool
    {
        return $this->status != OrderStatus::PAID->value;
    }
}
