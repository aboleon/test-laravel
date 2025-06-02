<?php

namespace App\Models\EventManager\Accommodation;

use App\Models\EventManager\Accommodation;
use App\Models\EventManager\Traits\AccommodationTrait;
use App\Models\Order\Cart\AccommodationCart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFramework\Polyglote\Traits\Translation;
use MetaFramework\Traits\OnlineStatus;

class RoomGroup extends Model
{
    use HasFactory;
    use AccommodationTrait;
    use OnlineStatus;
    use Translation;

    public $timestamps = false;
    protected $table = 'event_accommodation_room_groups';
    protected $guarded = [];

    public array $fillables
        = [
            'name'        => [
                'label' => 'Nom catégorie de chambre',
            ],
            'description' => [
                'type'  => 'textarea',
                'label' => 'Description (visible front)',
            ],
        ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'room_group_id');
    }

    public function publishedRooms(): HasMany
    {
        return $this->hasMany(Room::class, 'room_group_id')->whereNotNull('published');
    }


    public function contingents(): HasMany
    {
        return $this->hasMany(Contingent::class, 'room_group_id');
    }

    public function contingent(int $contgent_id)
    {
        return $this->contingents->where('id', $contgent_id)->first();
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class, 'event_accommodation_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(AccommodationCart::class, 'room_group_id');
    }
}
