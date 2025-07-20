<?php

namespace App\Models;

use App\Interfaces\SageInterface;
use App\Traits\SageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MetaFramework\Mediaclass\Interfaces\MediaclassInterface;
use MetaFramework\Mediaclass\Traits\Mediaclass;
use MetaFramework\Polyglote\Traits\Translation;

/**
 * @property int|null $parent
 * @property int $dictionnary_id
 */
class DictionnaryEntry extends Model implements MediaclassInterface, SageInterface
{
    use HasFactory;
    use Mediaclass;
    use Translation;
    use SageTrait;
    use SoftDeletes;

    public $timestamps = false;

    public array $fillables = [
        'name' =>[
            'type' => 'text',
            'label' => 'Intitulé',
            'required' => true,
        ],
    ];
    protected $casts = [
        'custom' => 'array',
    ];
    protected $guarded = [];
    public const string SAGEACCOUNT = 'compte_comptable';

    public function __construct(array $attributes = [])
    {
        //parent::__construct($attributes);
        $this->translatable = array_keys($this->fillables);
    }

    public static function scopeOfDictionnary($query, ?Dictionnary $dictionnary): Builder
    {
        if ($dictionnary->id) {
            $query->where('dictionnary_id', $dictionnary->id)->with('dictionnary');
        }
        return $query;
    }

    public function dictionnary(): BelongsTo
    {
        return $this->belongsTo(Dictionnary::class);
    }

    public function entries(): hasMany
    {
        return $this->hasMany(self::class, 'parent');
    }

    public function sageFields(): array
    {
        return [
            self::SAGEACCOUNT => 'Compte Comptable'
        ];
    }

    public function defaultSageReferenceValue(): string
    {
        return 'ACCNT';
    }
}
