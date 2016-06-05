<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventMember extends Model
{
    //
    protected $primaryKey = "uuid";
    protected $fillable = ['uuid', 'user_id', 'event_id'];
    public $timestamps = false;
    public static function getuuid(){
        return uniqid('ev');
    }
    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

}
