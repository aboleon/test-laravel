<?php

namespace App\DataTables\View;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int        $temp_front_bookings_count
 * @property int        $temp_bookings_count
 * @property int        $total_bookings_count
 * @property string|int $available
 * @property int        $bookings_count
 */
class EventSellableServiceStockView extends Model
{
    protected $table = 'event_sellable_service_stock_view';
    public $timestamps = false;
}
