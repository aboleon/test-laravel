<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Sage extends Model
{
    protected $table = 'sage';
    public $timestamps = false;
    protected $fillable
        = [
            'name',
            'value',
            'model_type',
            'model_id',
        ];


    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
