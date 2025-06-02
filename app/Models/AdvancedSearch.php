<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvancedSearch extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advanced_searches';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = ['auth_id', 'type', 'id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'auth_id',
        'type',
        'id',
    ];

    /**
     * Get the user that owns the search.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auth_id');
    }

    public static function removeStored(string $type): void
    {
        static::where([
            'auth_id' => auth()->id(),
            'type'    => $type,
        ])->delete();
    }
}
