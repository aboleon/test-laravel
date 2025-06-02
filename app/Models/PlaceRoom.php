<?php

namespace App\Models;

use App\Models\EventManager\Program\EventProgramSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFramework\Polyglote\Traits\Translation;

class PlaceRoom extends Model
{
    use HasFactory;
    use Translation;

    protected $fillable = [
        'title',
        'capacity',
        'information',
    ];

    public array $fillables = [
        'name' => [
            'type' => 'input',
            'label' => 'Nom',
        ],
        'level' => [
            'type' => 'input',
            'label' => 'Niveau',
        ],
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function setup(): HasMany
    {
        return $this->hasMany(PlaceRoomSetup::class, 'place_room_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(EventProgramSession::class, 'place_room_id');
    }
}
