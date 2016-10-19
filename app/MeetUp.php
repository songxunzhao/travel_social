<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class MeetUp extends Model
{
    //
protected $table = 'meetups';
 
 protected $fillable=['creator_id', 'message', 'lat', 'lng', 'state','type'];

 public function usr() {
        return $this->hasOne('App\User','id','creator_id');
    }


 public function toNearArray() {
	$obj = $this->toArray();
	$usr = $this->usr;
	$obj['name'] = $usr->name;
	$obj['email'] = $usr->email;
	$obj['profile_img'] = $usr->profile_img;        
//$obj = ['id' => $this->id, 'name'=> $usr->name, 'profile_img' => $usr->profile_img,'distance_in_km'=>$this->distance_in_km,'message'=>$this->message,'lat'=>$this->lat,'lng'=>$this->lng,created_at];
        return $obj;
    }
	public function toNewsnearArray() {
        $obj = $this->toArray();
	$obj['news_type']= "meetup";
        $usr = $this->usr;
        $obj['name'] = $usr->name;
	$obj['email'] = $usr->email;
        $obj['profile_img'] = $usr->profile_img;
//$obj = ['id' => $this->id, 'name'=> $usr->name, 'profile_img' => $usr->profile_img,'distance_in_km'=>$this->distance_in_km,'message'=>$this->message,'lat'=>$this->lat,'lng'=>$this->lng,created_at];
        return $obj;
    }
}
