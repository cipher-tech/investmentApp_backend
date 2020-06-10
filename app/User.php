<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $guarded = [
        'id'
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
    ];

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function verifiedUsers() {
        return $this->hasOne(Verification::class);
    }
    public function deposit() {
        return $this->hasMany(Deposit::class);
    }
    public function widthdrawl() {
        return $this->hasMany(Widthdrawal::class);
    }

    public function history()
    {
        return $this->hasMany('App\History');
    }

    public function plan()
    {
        return $this->hasOne('App\Plan');
    }

    public function plans()
    {
        return $this->belongsToMany('App\Plan', "plans_users")->withTimestamps()->withPivot("amount","count","duration","status","rate","earnings") ;;
    }
}
