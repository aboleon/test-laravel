<?php

namespace App\Models\EventManager\Program;

use App\Models\DictionnaryEntry;
use App\Models\EventContact;
use App\Models\Group;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MetaFramework\Polyglote\Traits\Translation;

class EventProgramIntervention extends Model
{
    use HasFactory, Translation;

    protected $table = 'event_program_interventions';

    protected $guarded = [];

    public $timestamps = false;

    public array $fillables = [
        'name' => [
            'label' => 'Nom',
            "required" => true,
        ],
        'description' => [
            'type' => 'textarea',
            'label' => 'Description',
        ],
    ];

    protected $casts = [
        'preferred_start_time' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }

    public function specificity(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'specificity_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(EventProgramSession::class, 'event_program_session_id');
    }


    public function orators(): BelongsToMany
    {
        return $this->belongsToMany(
            EventContact::class,
            'event_program_intervention_orators',
            'event_program_intervention_id',
            'events_contacts_id'
        )->withPivot("status");
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'sponsor_id');
    }

}
