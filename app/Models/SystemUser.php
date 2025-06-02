<?php

namespace App\Models;

use App\Enum\UserType;
use App\Modules\CustomFields\Traits\HasCustomFields;
use App\Traits\{Locale, Users};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasOne};
use Illuminate\Database\Eloquent\SoftDeletes;
use MetaFramework\Mediaclass\Interfaces\MediaclassInterface;
use MetaFramework\Mediaclass\Traits\Mediaclass;
use MetaFramework\Traits\DateManipulator;

/**
 * @property string                      $type
 * @property AccountProfile|null         $profile
 */
class SystemUser extends Model implements MediaclassInterface
{
    use HasFactory;
    use HasCustomFields;
    use DateManipulator;
    use Locale;
    use Users;
    use SoftDeletes;
    use Mediaclass;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable
        = [
            'name',
            'type',
            'first_name',
            'last_name',
            'email',
            'password',
        ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden
        = [
            'password',
            'remember_token',
            'two_factor_recovery_codes',
            'two_factor_secret',
        ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('userOfTypeAccount', function ($query) {
            $query->where('type', UserType::SYSTEM->value);
        });

        static::creating(function ($model) {
            $model->type = UserType::SYSTEM->value;
        });
    }


    public function profile(): HasOne
    {
        return $this->hasOne(AccountProfile::class, 'user_id');
    }

}
