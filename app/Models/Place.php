<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MetaFramework\Mediaclass\Interfaces\MediaclassInterface;
use MetaFramework\Mediaclass\Traits\Mediaclass;
use MetaFramework\Polyglote\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Place extends Model implements MediaclassInterface
{
    use HasFactory;
    use Translation;
    use Mediaclass;

    protected $fillable = [
        'name',
        'place_type_id',
        'email',
        'website',
        'phone',
    ];



    public array $fillables = [
        'description' =>[
            'type' => 'textarea',
            'label' => 'Description',
        ],
        'access' => [
          'type' => 'textarea',
          'label' => 'Accès',
        ],
        'more_title' => [
            'type' => 'input',
            'label' => 'Intitulé libre titre',
        ],
        'more_description' => [
            'type' => 'textarea',
            'label' => 'Intitulé libre contenu',
        ],
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }

    public function address(): HasOne
    {
        return $this->hasOne(PlaceAddress::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(PlaceRoom::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DictionnaryEntry::class, 'place_type_id');
    }

    public function mediaSettings(): array
    {
        return [
            '_media' =>
                [
                    'label' => 'Photo',
                ]
        ];
    }
}
