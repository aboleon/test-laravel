<?php

namespace App\DataTables\View;

use App\Models\EventContact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventContactView extends Model
{
    protected $table = 'event_contact_view';
    public $timestamps = false;

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'id', 'id');
    }
}
