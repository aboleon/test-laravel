<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MetaFramework\Casts\PriceInteger;

/**
 * @property string $type
 * @property AccountProfile|null $profile
 * @property Collection $address
 * @property Collection $groups
 */
class PayboxReimbursementRequest extends Model
{
    protected $table = 'paybox_reimbursement_requests';
    protected $fillable = [
        'shoppable_type',
        'shoppable_id',
        'amount',
        'calling_params',
        'received_data',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'calling_params' => 'array',
        'received_data' => 'array',
    ];

    public function response()
    {
        return $this->received_data;
    }

    public function responseCode(): string
    {
        return (string)$this->received_data['CODEREPONSE'];
    }

    public function responseComment(): string
    {
        return $this->received_data['COMMENTAIRE'];
    }

    public function isSuccessful(): bool
    {
        $returnCode = trim($this->responseCode());

        return $returnCode !== '' && intval($returnCode) === 0;
    }

    public function shoppable(): MorphTo
    {
        return $this->morphTo();
    }

}
