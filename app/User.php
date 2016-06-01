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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'age', 'location', 'lat', 'lng', 'job_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $appends = ['ranking'];
    public function getRankingAttribute() {
        return 1;
    }
}
