<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *    @SWG\Definition(@SWG\Xml(name="UserInvite"))
 */
class UserInvite extends Model
{
    //
    /**
     * @SWG\Property(property="email")
     * @var string
     */

    protected $primaryKey = "uuid";
    protected $fillable = [
        'uuid', 'email', 'user_id', 'registered_id'
    ];
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function registered() {
        return $this->belongsTo('App\User', 'registered_id');
    }
    public static function getuuid() {
        return uniqid('ui');
    }

}
