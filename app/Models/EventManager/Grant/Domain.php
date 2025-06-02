<?php

namespace App\Models\EventManager\Grant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'event_grant_domain';

    protected $guarded = [];
}
