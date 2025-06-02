<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPec extends Model
{
    use HasFactory;

    protected $table = 'events_pec';
    protected $guarded = [];
    public  $timestamps = false;

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    public function grantAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'grant_admin_id');
    }
}
