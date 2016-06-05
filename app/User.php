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
        return $obj;
    }

    public function getRankingAttribute() {
        
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
            $relative_arr[] = $relative->registered->toArray();
        }
        return $relative_arr;
    }
}
