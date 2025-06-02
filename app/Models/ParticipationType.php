<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MetaFramework\Polyglote\Traits\Translation;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $slug
 */
class ParticipationType extends Model
{
    use HasFactory;

    use Translation;

    public $timestamps = false;
    public array $fillables = [
        'name' =>[
            'type' => 'text',
            'label' => 'Intitulé',
            'required' => true,
        ],
    ];
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->translatable = array_keys($this->fillables);
    }
}
