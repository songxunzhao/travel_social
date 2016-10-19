<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	protected $table = 'notifications';

 protected $fillable=['creator_id', 'uid', 'type', 'nid'];

    //

	 public function usr() {
        return $this->hasOne('App\User','id','creator_id');
    }


 public function toSummaryArray() {
        $obj = $this->toArray();
        $usr = $this->usr;
        $obj['name'] = $usr->name;
        $obj['profile_img'] = $usr->profile_img;
        return $obj;
    }
}
