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
     * @SWG\Property(property="img")
     * @var string
     */
    /**
     * @SWG\Property(property="title")
     * @var string
     */
    /**
     * @SWG\Property(property="from")
     * @var dateTime
     */
    /**
     * @SWG\Property(property="to")
     * @var dateTime
     */
    /**
     * @SWG\Property(property="description")
     * @var string
     */
    /**
     * @SWG\Property(property="venue")
     * @var string
     */
    /**
     * @SWG\Property(property="creator_id")
     * @var integer
     */
    /**
     * @SWG\Property(property="lat")
     * @var float
     */
    /**
     * @SWG\Property(property="lng")
     * @var float
     */

    protected $fillable=['title', 'img', 'from', 'to', 'description',
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

        $attends = $this->attends;
        $attend_arr = [];
        foreach($attends as $attend) {
            $attend_arr[] = $attend->user->toAvatarArray();
        }
        $obj['attends'] = $attend_arr;
        return $obj;
    }

}
