<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany('App\User', "plans_users")->withTimestamps()->withPivot("amount","count","duration","status","rate","earnings") ;
    }
}
