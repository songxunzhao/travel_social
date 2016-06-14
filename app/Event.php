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
    protected $img;
    /**
     * @SWG\Property()
     * @var string
     */
    protected $title;
    /**
     * @SWG\Property()
     * @var dateTime
     */
    protected $from;
    /**
     * @SWG\Property()
     * @var dateTime
     */
    protected $to;
    /**
     * @SWG\Property()
     * @var string
     */
    protected $description;
    /**
     * @SWG\Property()
     * @var string
     */
    protected $venue;
    /**
     * @SWG\Property()
     * @var integer
     */
    protected $creator_id;
    /**
     * @SWG\Property()
     * @var float
     */
    protected $lat;
    /**
     * @SWG\Property()
     * @var float
     */
    protected $lng;

    protected $fillable=['img', 'from', 'to', 'description', 
                        'venue', 'creator_id', 'lat', 'lng'];
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
