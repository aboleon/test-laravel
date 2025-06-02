<?php

namespace App\Models;

use App\Interfaces\UserCustomDataInterface;
use App\Models\EventManager\EventGroup\EventGroupContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MetaFramework\Mediaclass\Interfaces\MediaclassInterface;
use MetaFramework\Mediaclass\Traits\Mediaclass;
use App\Notifications\ResetPasswordNotification;
use App\Traits\Locale;
use MetaFramework\Traits\Responses;
use App\Traits\Users;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @property AccountProfile|null $accountProfile
 */
class User extends Authenticatable implements MediaclassInterface
{
    use HasFactory;

    use Locale;
    use Mediaclass;
    use Notifiable;
    use Responses;
    use Users;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime'
    ];

    public function canImpersonate(): bool
    {
        // For example
        return true;
    }
    public function canBeImpersonated(): bool
    {
        // For example
        return false;
    }

    public function processRoles(): static
    {
        if ($this->roles->isNotEmpty()) {
            $this->roles->each(function ($item) {
                UserRole::where(['user_id' => $this->id, 'role_id' => $item->role_id])->delete();
            });
        }
        if (request()->filled('roles')) {
            $roles = [];
            foreach (request('roles') as $role) {
                $roles[] = new UserRole(['role_id' => $role]);
            }
            $this->roles()->saveMany($roles);
        }

        return $this;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification);
    }

    public function getRoleAttribute(): ?int
    {
        return $this->userRole();
    }


    public function userSubData(?string $role = null): UserCustomDataInterface|bool
    {
        if (!$role) {
            return false;
        }

        $subclass = "\App\Models\User\\" . ucfirst(Str::camel($role));

        return class_exists($subclass) ? new $subclass : false;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'events_contacts', 'user_id', 'event_id');
    }

    public function eventGroupContacts(): BelongsToMany
    {
        return $this->belongsToMany(EventGroupContact::class, 'event_group_contacts', 'user_id', 'event_group_id');
    }




    //--------------------------------------------
    // MediaclassInterface
    //--------------------------------------------
    public function getMediaOptions(): array
    {
        return [
            'maxMediaCount' => 1,
        ];
    }
}
