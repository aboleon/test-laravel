<?php

namespace App\DataTables\View;

use App\Models\EventContact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserView extends Model
{
    protected $table = 'user_view';
    public $timestamps = false;

    public function scopeSearchByName(Builder $query, ?string $keyword = null): Builder
    {
        if ($keyword) {
            $query->where(fn($where)
                => $where
                ->where('first_name', 'like', '%'.$keyword.'%')
                ->orWhere('last_name', 'like', '%'.$keyword.'%')
                ->orWhere('email', 'like', '%'.$keyword.'%')
            );
        }

        return $query;
    }

    public function scopeShowTrashed(Builder $query, $showTrashed = false): Builder
    {
        return $showTrashed ? $query : $query->whereNull('deleted_at');
    }

    public function scopeExcludeEvent(Builder $query, ?int $event_id = null): Builder
    {
        return $event_id ? $query->whereNotIn('id', EventContact::where('event_id', $event_id)->pluck('user_id')) : $query;
    }
}
