<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    //
	protected $table = 'chats';

         protected $fillable=['sid','rid','subject', 'message','type','thread_id','starred','is_read'];

	public function usr() {
        return $this->hasOne('App\User','id','sid');
    }
	 public function use() {
        return $this->hasOne('App\User','id','rid');
    }
	public function attends() {
        return $this->hasMany('App\EventMember', 'event_id');
    }

	public function toInboxArray() {
        $obj = $this->toArray();

       
        $usr = $this->usr;
        $obj['name'] = $usr->name;
        $obj['profile_img'] = $usr->profile_img;
	$obj['email'] = $usr->email;
        return $obj;
    }
	public function toAdminArray() {
        $obj = $this->toArray();


       // $usr = $this->usr;
        $obj['name'] = 'Admin';
        $obj['profile_img'] = '';
        //$obj['email'] = $usr->email;
        return $obj;
    }
	 public function toSentArray() {
        $obj = $this->toArray();


        $usr = $this->use;
        $obj['name'] = $usr->name;
        $obj['profile_img'] = $usr->profile_img;
	$obj['email'] = $usr->email;
        return $obj;
    }
}
