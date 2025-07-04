<?php

namespace App\Models;

use App\Interfaces\SageInterface;
use App\Traits\SageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invoice extends Model implements SageInterface
{
    use HasFactory;
    use SageTrait;

    protected $table = "order_invoices";
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'created_by',
        'invoice_number',
        'proforma'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'proforma' => 'boolean'
    ];


    protected static function booted()
    {
        static::created(function ($invoice) {
            // todo
            // https://trello.com/c/y6dunPis/374-prios-stp
            /**
             * prévoir que : quand statut de la commande passe en "facturé", un mail auto est envoyé vers pax avec lien vers facture (que la commande ait été faite en bo et "facturée" manuellement ou faite en front et "facturée" automatiquement)
             *
             * ---> Uniquement pour pax? --> wait for answer...
             */
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function sageFields(): array
    {
        return [
            'code_facture' => 'Numéro de facture SAGE',
        ];
    }

    public function getSageDatabaseId(): string
    {
        return str_pad(Str::substr($this->id, 0, 5), 5, '0', STR_PAD_LEFT);
    }

    public function getSageReferenceValue(): string
    {
        if ($this->sageReference === null) {
            $this->sageReference = (string)$this->sageData->where('name', 'code_facture')->value('value') ?: 'FACT';
        }

        return $this->sageReference;
    }

    public function getSageEvent(): Event
    {
        return $this->order->event;
    }
}
