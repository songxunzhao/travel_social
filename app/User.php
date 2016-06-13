<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
/**
*    @SWG\Definition(@SWG\Xml(name="User"))
*/
class User extends Authenticatable
{
    /**
     * @SWG\Property()
     * @var string
     */
    public $name;
    /**
     * @SWG\Property()
     * @var int
     */
    public $age;
    /**
     * @SWG\Property()
     * @var string
     */
    public $location;
    /**
     * @SWG\Property()
     * @var float
     */
    public $lat;
    /**
     * @SWG\Property()
     * @var float
     */
    public $lng;
    /**
     * @SWG\Property()
     * @var string
     */
    public $job_name;

    /**
     * @SWG\Property()
     * @var string
     */
    public $profile_img;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'age', 'location', 'lat', 'lng', 'job_name', 'profile_img'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function toProfileArray() {
        $obj = $this->toArray();
        $obj['invited_by'] = $this->getInvitedByAttribute();
        $obj['relationship'] = $this->getRelationshipAttribute();
        $obj['rank'] = $this->getRankingAttribute();
        return $obj;
    }

    public function getRankingAttribute() {
        return User::where('score', '>', $this->score)->count() + 1;
    }

    public function getInvitedByAttribute() {
        $invite_arr= [];
        $invites = UserInvite::where('registered_id', $this->id)->get();
        foreach($invites as $invite){
            $invite_arr[] = $invite->user->toArray();
        }
        return $invite_arr;
    }
    public function getRelationshipAttribute(){
        $relative_arr = [];
        $relatives = UserInvite::where('user_id', $this->id)->get();
        foreach($relatives as $relative) {
            if($relative->registered)
                $relative_arr[] = $relative->registered->toArray();
        }
        return $relative_arr;
    }

    public static function score_cases() {
        return [
            "invite_user" => 101,
            "request_meet_up" => 4,
            "accept_meet_up" => 8,
            "sos" => 1,
            "help_sos" => 10,
            "buy_item" => 40,
            "report_spam" => 10,
            "create_event" => 10,
            "attend_event" => 2,
            "add_item" => 10,
            "get_reported" => -30,
            "deleted_by_admin" => -80,
            "cancel_attend" => -3
        ];
    }
}
