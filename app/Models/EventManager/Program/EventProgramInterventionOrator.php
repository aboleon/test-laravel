<?php

namespace App\Models\EventManager\Program;

use App\Models\DictionnaryEntry;
use App\Models\EventContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventProgramInterventionOrator extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'event_program_intervention_orators';
    public $timestamps = false;

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'events_contacts_id');
    }

    public function intervention(): BelongsTo
    {
        return $this->belongsTo(EventProgramIntervention::class, 'event_program_intervention_id');
    }

    public function specificity()
    {
        return $this->belongsTo(DictionnaryEntry::class, 'specificity_id');
    }
}
