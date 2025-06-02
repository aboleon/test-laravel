<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $account_type
 */

class SavedSearch extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'saved_searches';

    protected $fillable = [
//        'user_id',
        'name',
        'type',
        'search_filter',
    ];
}
