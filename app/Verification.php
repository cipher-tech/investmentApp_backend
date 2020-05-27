<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
   protected $fillable = ['images',"status", "user_id"];

   public function user()
   {
       return $this->belongsTo('App\User');
   }
}
