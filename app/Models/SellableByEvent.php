<?php

namespace App\Models;

use MetaFramework\Casts\PriceInteger;
use MetaFramework\Polyglote\Traits\Translation;
use MetaFramework\Traits\OnlineStatus;
use App\Traits\Price;
use Illuminate\Database\Eloquent\{Factories\HasFactory, Model, Relations\BelongsTo};

class SellableByEvent extends Model
{
    use HasFactory;
    use Price;
    use OnlineStatus;
    use Translation;


    protected $guarded = [];
    protected $table = 'sellables_by_event';

    protected $casts = [
      'price' => PriceInteger::class,
      'price_buy' => PriceInteger::class,
    ];

    protected ?Client $client = null;

    public array $fillables = [
        'title' => [
            'type' => 'text',
            'label' => 'Intitulé',
        ],
        'description' => [
            'type' => 'textarea',
            'label' => 'Description',
        ],
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }
    public function vat(): BelongsToN
    {
        return $this->belongsTo(Vat::class, 'vat_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class,'event_id');
    }


}
