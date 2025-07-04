<?php

namespace App\Models;

use App\Interfaces\SageInterface;
use App\Traits\SageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $bank)
 */
class BankAccount extends Model implements SageInterface
{
    use HasFactory;
    use SageTrait;

    protected $fillable = [
        'name',
        'code',
        'branch',
        'account',
        'rib',
        'holder',
        'domiciliation',
        'iban',
        'swift'
    ];

    public array $fillables = [
        'name' => [
            'label' => 'Nom *',
        ],
        'code' => [
            'label' => 'Code banque *',
        ],
        'branch' => [
            'label' => 'Code guichet *',
        ],
        'account' => [
            'label' => 'N° de compte *',
        ],
        'rib' => [
            'label' => 'Clé RIB *',
        ],
        'holder' => [
            'label' => 'Titulaire *',
        ],
        'domiciliation' => [
            'label' => 'Domiciliation *',
        ],
        'iban' => [
            'label' => 'IBAN *',
        ],
        'swift' => [
            'label' => 'SWIFT *',
        ],
    ];

    public function sageFields(): array
    {
        return [
            'compte_comptable' => 'Compte Comptable Sage',
        ];
    }
}
