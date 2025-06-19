<?php

namespace App\Models\EventManager\Program;

use App\Models\DictionnaryEntry;
use App\Models\EventContact;
use App\Models\PlaceRoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFramework\Polyglote\Traits\Translation;

class EventProgramSession extends Model
{
    use HasFactory, Translation;

    protected $guarded = [];

    public array $fillables = [
        'name' => [
            'label' => 'Nom',
            'required' => true,
        ],
        'description' => [
            'type' => 'textarea',
            'label' => 'Description',
        ],
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }

    public function programDay(): BelongsTo
    {
        return $this->belongsTo(EventProgramDayRoom::class, 'event_program_day_room_id');
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(EventProgramIntervention::class, 'event_program_session_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(PlaceRoom::class, 'place_room_id');
    }

    public function moderators(): BelongsToMany
    {
        return $this->belongsToMany(
            EventContact::class,
            'event_program_session_moderators',
            'event_program_session_id',
            'events_contacts_id'
        )->withPivot("status");
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'sponsor_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'session_type_id');
    }

}
