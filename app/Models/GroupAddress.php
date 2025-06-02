<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MetaFramework\Interfaces\GooglePlacesInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupAddress extends Model implements GooglePlacesInterface
{
    use HasFactory;
    protected $table = 'group_address';

    protected $fillable = [
        'group_id',
        'billing',
        'name',
        'street_number',
        'route',
        'locality',
        'postal_code',
        'country_code',
        'text_address',
        'lat',
        'lon',
        'cedex',
        'complementary',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
