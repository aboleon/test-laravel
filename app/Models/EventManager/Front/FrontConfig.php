<?php

namespace App\Models\EventManager\Front;

use App\Models\DictionnaryEntry;
use App\Models\Event;
use App\Models\EventManager\Sellable\Deposit;
use App\Models\EventManager\Sellable\Option;
use App\Models\EventManager\Sellable\Price;
use App\Models\ParticipationType;
use App\Models\Place;
use App\Models\Vat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany,
    HasMany,
    HasOne
};
use Illuminate\Database\Eloquent\SoftDeletes;
use MetaFramework\Casts\PriceInteger;
use MetaFramework\Polyglote\Traits\Translation;
use MetaFramework\Traits\OnlineStatus;

class FrontConfig extends Model
{
    use HasFactory;
    protected $table = 'event_front_config';
    protected $guarded = [];
}
