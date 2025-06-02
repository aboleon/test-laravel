<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionParticipant extends Model
{
    use HasFactory;
    protected $table = 'interventions_participants';
    protected $guarded = [];
    public $timestamps = false;
}
