<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\EventMember;
/**
 *    @SWG\Definition(@SWG\Xml(name="Event"))
 */
class Event extends Model
{
    //
    /**
     * @SWG\Property()
     * @var string
     */
    public $img;
    /**
     * @SWG\Property()
     * @var string
     */
    public $title;
    /**
     * @SWG\Property()
     * @var dateTime
     */
    public $from;
    /**
     * @SWG\Property()
     * @var dateTime
     */
    public $to;
    /**
     * @SWG\Property()
     * @var string
     */
    public $description;
    /**
     * @SWG\Property()
     * @var string
     */
    public $venue;
    /**
     * @SWG\Property()
     * @var integer
     */
    public $creator_id;
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

    protected $guarded=['id', 'created_at', 'updated_at'];
    public function attends() {
        return $this->hasMany('App\EventMember', 'event_id');
    }
    public function toCompleteArray() {
        $obj = $this->toArray();

        $attends = $this->attends;
        $attend_arr = [];
        foreach($attends as $attend) {
            $attend_arr[] = $attend->user->toArray();
        }
        $obj['attends'] = $attend_arr;
        return $obj;
    }
    public function toSummaryArray() {
        $obj = $this->toArray();

        $num_attends = $this->attends->count();
        $obj['num_attend'] = $num_attends;
        return $obj;
    }

}
