<?php

namespace App\Models\EventManager\Transport;

use App\Casts\NullablePriceFloat;
use App\Casts\TimeCast;
use App\Models\DictionnaryEntry;
use App\Models\EventContact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFramework\Casts\PriceInteger;
use MetaFramework\Mediaclass\Interfaces\MediaclassInterface;
use MetaFramework\Mediaclass\Traits\Mediaclass;

/**
 * @property int         $ticket_price
 * @property string|null $management_history
 * @property string      $management_mail
 * @property string|null $transfer_shuttle_time_departure
 * @property string|null $transfer_shuttle_time_return
 * @property string|null $transfer_info_departure
 * @property string|null $transfer_info_return
 */
class EventTransport extends Model implements MediaclassInterface
{
    use HasFactory;
    use Mediaclass;

    protected $guarded = [];
    protected $table = 'event_transports';

    protected $casts
        = [
            'departure_start_date'            => 'date',
            'departure_start_time'            => TimeCast::class,
            'departure_end_time'              => TimeCast::class,
            'return_start_date'               => 'date',
            'return_start_time'               => TimeCast::class,
            'return_end_time'                 => TimeCast::class,
            'transfer_shuttle_time_departure' => TimeCast::class,
            'transfer_shuttle_time_return'    => TimeCast::class,
            'price_before_tax'                => NullablePriceFloat::class,
            'price_after_tax'                 => NullablePriceFloat::class,
            'ticket_price'                    => PriceInteger::class,
            'management_mail'                 => 'datetime',
        ];


    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, "events_contacts_id");
    }


    public function departureStep(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'departure_step');
    }

    public function departureTransportType(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'departure_transport_type');
    }

    public function returnStep(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'return_step');
    }

    public function returnTransportType(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'return_transport_type');
    }

    public function getUserAttribute(): User|null
    {
        return $this->eventContact?->user;
    }


}
