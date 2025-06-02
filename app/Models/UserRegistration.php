<?php

namespace App\Models;

use App\Traits\BelongsTo\BelongsToEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string        $id
 * @property null|Event    $event
 * @property null|int      $event_id
 * @property null|Account  $account_id
 * @property string        $email
 * @property string        $registration_type
 * @property string|Carbon $validated_at
 * @property string|Carbon $terminated_at
 * @property string|Carbon $created_at
 * @property null|Account  $account
 */
class UserRegistration extends Model
{
    use BelongsToEvent;

    public $incrementing = false;
    public $timestamps = false;
    /**
     * @var int|mixed
     */
    protected $table = 'users_registration';
    protected $keyType = 'string';

    protected $fillable
        = [
            'email',
            'account_id',
            'event_id',
            'registration_type',
            'options',
            'validated_at',
            'terminated_at',
        ];

    protected $casts
        = [
            'options'       => 'array',
            'created_at'    => 'datetime',
            'validated_at'  => 'datetime',
            'terminated_at' => 'datetime',
        ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string)Str::uuid();
            }
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
