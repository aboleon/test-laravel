<?php

namespace App\Models\EventManager\Sellable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFramework\Polyglote\Traits\Translation;

class Option extends Model
{
    use HasFactory;
    use Translation;

    public $timestamps = false;
    protected $table = 'event_sellable_service_options';

    protected $fillable = [
        'event_sellable_service_id',
        'description'
    ];

    public array $fillables = [
        'description' => [
            'label' => 'Texte',
        ],
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defineTranslatables();
    }
}
