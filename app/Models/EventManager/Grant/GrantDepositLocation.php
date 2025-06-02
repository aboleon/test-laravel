<?php

namespace App\Models\EventManager\Grant;

use App\Abstract\GrantLocationAbstract;
use App\Models\EventManager\Sellable\Deposit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrantDepositLocation extends GrantLocationAbstract
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'event_grant_deposit_location';

    protected $fillable = [
        'locality',
        'country_code'
    ];

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }
}
