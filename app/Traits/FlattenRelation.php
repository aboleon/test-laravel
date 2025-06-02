<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait FlattenRelation{
    public function flattenRelation(Collection $parent, Collection $child, string $foreignId): Collection
    {
        $flatten = collect();
        $parent->each(function($item) use ($child, $flatten, $foreignId) {
            $flatten->push($item);
            $child->where($foreignId, $item->id)->each(function($item) use( $flatten ){
                $flatten->push($item);
            });
        });
        return $flatten;
    }
}
