<?php

namespace App\Models\EventManager\Accommodation;

use App\Models\EventManager\Traits\AccommodationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFramework\Casts\Datepicker;
use MetaFramework\Casts\ForceInteger;

/**
 * Responsable du nombre de chambres grant assignées dans
 * Event Manager > Hébergements > Hébergement X > Tab Chambres Grant
 * panel/manager/event/{event_id}/accommodation/{event_accommodation_id}/rooms/grant
 */

/**
 * @property string $group_id
 * @property int    $event_accommodation_id
 * @property int    $room_group_id
 * @property int    $total
 * @property string $date
 */
class Grant extends Model
{
    use HasFactory;
    use AccommodationTrait;

    public $timestamps = false;
    protected $table = 'event_accommodation_grant';
    protected $fillable
        = [
            'event_accommodation_id',
            'group_id',
            'date',
            'total',
        ];

    protected $casts
        = [
            'date'  => Datepicker::class,
            'total' => ForceInteger::class,
        ];

}
