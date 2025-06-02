<?php

namespace App\Models\EventManager\Accommodation;

use App\Models\EventManager\Traits\AccommodationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFramework\Casts\PriceInteger;
use MetaFramework\Polyglote\Traits\Translation;

class Service extends Model
{
    use HasFactory;
    use AccommodationTrait;
    use Translation;

    public $timestamps = false;
    protected $table = 'event_accommodation_service';
    protected $guarded = [];

    public array $fillables = [
        'name' =>[
            'label' => 'Intitulé',
        ],
    ];

    protected $casts = [
      'price' => PriceInteger::class,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }
}
