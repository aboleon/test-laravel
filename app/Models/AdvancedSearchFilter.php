<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvancedSearchFilter extends Model
{
    protected $table = 'advanced_searches_filters';

    protected $primaryKey = ['auth_id', 'type'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable
        = [
            'auth_id',
            'type',
            'filters',
        ];

    protected $casts
        = [
            'filters' => 'string', // Keep as string since it's JSON
        ];

    /**
     * Get the user that owns this search filter
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auth_id');
    }

    /**
     * Get filters for a specific user and type
     */
    public static function getFilters(string $type): ?string
    {
        $filter = static::where('auth_id', auth()->id())
            ->where('type', $type)
            ->first();

        return $filter?->filters;
    }

    /**
     * Store or update filters for a specific user and type
     */
    public static function storeFilters(string $type, string $filters): void
    {
        static::removeFilters($type);
        // Create new filter record for current user
        static::create([
            'auth_id' => auth()->id(),
            'type'    => $type,
            'filters' => $filters,
        ]);
    }

    public static function removeFilters(string $type): void
    {
        static::where([
            'auth_id' => auth()->id(),
            'type'    => $type,
        ])->delete();
    }
}
