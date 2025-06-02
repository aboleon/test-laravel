<?php

namespace App\Models\EventManager\Program;

use App\Models\DictionnaryEntry;
use App\Models\EventContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventProgramSessionModerator extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'event_program_session_moderators';
    public $timestamps = false;

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'events_contacts_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(EventProgramSession::class, 'event_program_session_id');
    }

    public function moderatorType()
    {
        return $this->belongsTo(DictionnaryEntry::class, 'moderator_type_id');
    }
}
