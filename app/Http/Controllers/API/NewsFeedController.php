<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Event;
use App\MeetUp;
use App\EventMember;
use DB;


class NewsFeedController extends Controller
{	
	 function mysort($a,$b)
        {
                if ($a['created_at']==$b['created_at']) return 0;
                return ($a['created_at']>$b['created_at'])?-1:1;
        }
	
        public function index(Request $request){
         $user = $request->user();
        //$page_size = $request->input('page_size', 10);
         $lat = $user->lat;
         $lng = $user->lng;


         $users = MeetUp::selectRaw('*, p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(meetups.lat))
                 * COS(RADIANS(p.longpoint) - RADIANS(meetups.lng))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(meetups.lat)))) AS distance_in_km')->join(DB::raw("(SELECT  $lat AS latpoint,  $lng AS longpoint,
                        100.0 AS radius,      111.045 AS distance_unit) as p"), function($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1'));
            })->where('creator_id','!=',$user->id)
           // ->where('distance_in_km','<=',40)
            ->orderBy('distance_in_km', 'asc')
            ->orderBy('created_at', 'desc')->get();
            //->paginate($page_size);

            $user_arr =[];
            foreach($users as $user) {
                if($user->distance_in_km >10000)
                        break;
                $user_arr[] = $user->toNewsnearArray();

		}
			
		//print_r($user_arr);	

		 $events = Event::selectRaw('*, p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(events.lat))
                 * COS(RADIANS(p.longpoint) - RADIANS(events.lng))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(events.lat)))) AS distance_in_km')->join(DB::raw("(SELECT  $lat AS latpoint,  $lng AS longpoint,
                        100.0 AS radius,      111.045 AS distance_unit) as p"), function($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1'));
            })->where('from','>',DB::raw('DATE_SUB(NOW(),INTERVAL 2 DAY)'))
            ->orderBy('distance_in_km', 'asc')
            ->orderBy('created_at', 'desc')->get();
            //->paginate($page_size);

            $event_arr =[];
            foreach($events as $event) {
                $event_arr[] = $event->toSummeventArray();
            }
        $res = array_merge($event_arr,$user_arr);
        usort($res,[$this,'mysort']);

        return response()->json([
                                    'code'=>200,
                                    'data'=>[
                                        'count'=>count($res),
                                        'results'=>$res
                                    ]
                                ]);

        } 
}
