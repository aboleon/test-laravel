<?php

namespace App\Models\EventManager\Grant;

use App\Models\EventManager\Sellable\Deposit;
use App\Models\ParticipationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFramework\Casts\PriceInteger;

class GrantDeposit extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'event_grant_deposit';

    protected $casts = [
        'amount' => PriceInteger::class
    ];

    protected $fillable = [
        'amount'
    ];
    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }


    public function locations(): HasMany
    {
        return $this->hasMany(GrantDepositLocation::class, 'deposit_id');
    }
    public function participations(): BelongsToMany
    {
        return $this->belongsToMany(ParticipationType::class, 'event_grant_deposit_participation', 'deposit_id', 'participation_id');
    }
}
