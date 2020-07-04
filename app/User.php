<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    const DEFAULT_PER_PAGE = 5;
    const TOKEN_LENGTH = 32;
    const MINUTES_TOKEN_EXPIRE_IN = 180;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'bearer_token_expire_at' => 'datetime'
    ];

    /**
     * Return true if email_verified date is present
     * @return bool
     */
    public function getIsEmailVerifiedAttribute() {
        return boolval($this->email_verified_at);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Hash password during creating new user
     */
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }
}
