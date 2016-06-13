<?php

namespace App\Http\Controllers\API;

use App\EventMember;
use App\User;
use Illuminate\Http\Request;
use App\Event;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use DB;
class EventController extends Controller
{
    //
    protected function validator($data) {
        return Validator::make($data,
            [
                'title'=>'required',
                'from'=>'required|date',
                'to'=>'required|date',
                'lat'=>'numeric',
                'lng'=>'numeric'
            ]
        );
    }
    /**
     * @SWG\Get(
     *     path="api/events",
     *     tags={"Event"},
     *     summary="Get event list",
     *     description="Get event list",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *          response="200",
     *          description="",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  default=200,
     *                  description="Response code"
     *               ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Event")
     *               )
     *         )
     *      )
     * )
     */
    public function index(Request $request) {
        $user = $request->user();
        $lat = $user->lat;
        $lng = $user->lng;

        #TODO: sort by distance from user
        // $events = Event::all();
        $events = DB::select("SELECT z.*,
        p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(z.latitude))
                 * COS(RADIANS(p.longpoint) - RADIANS(z.longitude))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(z.latitude)))) AS distance_in_km
          FROM events AS z
          JOIN (   /* these are the query parameters */
                SELECT  $lat AS latpoint,  $lng AS longpoint,
                        100.0 AS radius,      111.045 AS distance_unit
            ) AS p ON 1=1
          WHERE z.latitude
             BETWEEN p.latpoint  - (p.radius / p.distance_unit)
                 AND p.latpoint  + (p.radius / p.distance_unit)
            AND z.longitude
             BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
                 AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
          ORDER BY distance_in_km asc");

        $event_arr =[];
        foreach($events as $event) {
            $event_arr[] = $event->toSummaryArray();
        }
        return response()->json(['code'=>200, 'data'=>$event_arr]);
    }
    /**
     * @SWG\Get(
     *     path="api/events/{event_id}/attend",
     *     tags={"Event"},
     *     summary="Attend event",
     *     description="Attend event",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="event_id",
     *          in="path",
     *          description="Event id",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Response(
     *          response="200",
     *          description="",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  default=200,
     *                  description="Response code"
     *               ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *               )
     *         )
     *      )
     * )
     */
    public function attend(Request $request, $eventId) {
        $user = $request->user();
        $event = Event::find($eventId);
        $member = EventMember::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if($member)
        {
            return response()->json(['code'=>429, 'message'=> 'You are already attending event'], 429);
        }
        else {
            EventMember::create(['uuid'=>EventMember::getuuid(), 'event_id'=>$eventId, 'user_id'=>$user->id]);
        }

        $user->score += User::score_cases()['attend_event'];
        $user->save();

        return response()->json(['code'=>200, 'message'=>'Attend request was successful']);
    }
    /**
     * @SWG\Get(
     *     path="api/events/{event_id}",
     *     tags={"Event"},
     *     summary="Get event detail",
     *     description="Get event detail",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="event_id",
     *          in="path",
     *          description="Event id",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Response(
     *          response="200",
     *          description="",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  default=200,
     *                  description="Response code"
     *               ),
     *              @SWG\Property(
     *                  property="data",
     *                  @SWG\Schema(ref="#/definitions/Event")
     *               )
     *         )
     *      )
     * )
     */
    public function show(Request $request, $eventId) {
        $user = $request->user();
        $event = Event::find($eventId);
        if($event)
            return response()->json(['code'=>200, 'data'=>$event->toCompleteArray()]);
        else
            return response()->json(['code'=>404, 'message'=>'Event was not found']);
    }
    /**
     * @SWG\Post(
     *     path="api/events",
     *     tags={"Event"},
     *     summary="Create event",
     *     description="Create event",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Register data",
     *          required=true,
     *          type="string",
     *           @SWG\Schema(
     *              ref="#/definitions/Event"
     *           )
     *      ),
     *     @SWG\Response(
     *          response="200",
     *          description="",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  default=200,
     *                  description="Response code"
     *               ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Schema(ref="#/definitions/Event")
     *               )
     *         )
     *      )
     * )
     */
    public function store(Request $request) {
        $user = $request->user();
        $validator = $this->validator($request->all());
        $request_data = $request->all();
        $request_data['creator_id'] = $user->id;
        if($validator->fails()) {
            return response()->json(['code'=>400, 'errors'=> $validator->errors(), 'message'=>'Bad request format']);
        }

        $event = Event::create($request_data);
        EventMember::create(['uuid'=>EventMember::getuuid(), 'event_id'=>$event->id, 'user_id'=>$user->id]);

        #Add score
        $user->score += User::score_cases()['create_event'];
        $user->save();
        return response()->json(['code'=>200, 'data'=> $event->toArray()]);
    }
}
