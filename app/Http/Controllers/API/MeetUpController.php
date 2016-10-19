<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MeetUp;
use App\Http\Requests;
use Validator;
use DB;

class MeetUpController extends Controller
{
    //
	 protected function validator($data) {
        return Validator::make($data, [
            'message'=>'required',
	    'type'=>'numeric|required',
	    'lat'=>'numeric',
	    'lng'=>'numeric',
        ]);
	}
	
	public function store(Request $request){
		$user = $request->user();
		$validator =  $this->validator($request->all());
		$request_data = $request->all();
		$request_data['creator_id'] = $user->id;
		if($validator->fails()) {
         	   return response()->json(['code'=>400, 'errors'=> $validator->errors(),
                                    'message'=>'Some fields are missing or wrong']);
        	}
		
		$meetup = MeetUp::create($request_data);
		$meetup->save();

		return response()->json(['code'=>200, 'data'=> $meetup->toArray()]);
	}
	public function index(Request $request){
		 return response()->json(['code'=>201]);
	}

	public function getUsers(Request $request){
	
        $user = $request->user();
        $page_size = $request->input('page_size', 10);
        $lat = $user->lat;
        $lng = $user->lng;

         $users = MeetUp::selectRaw('distinct meetups.creator_id,meetups.type,meetups.message,meetups.lat,meetups.lng,meetups.state,meetups.created_at,meetups.updated_at, p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(meetups.lat))
                 * COS(RADIANS(p.longpoint) - RADIANS(meetups.lng))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(meetups.lat)))) AS distance_in_km')->join(DB::raw("(SELECT  $lat AS latpoint,  $lng AS longpoint,
                        100.0 AS radius,      111.045 AS distance_unit) as p"), function($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1'));
            })->where('creator_id','!=',$user->id)->groupBy('creator_id')
	   // ->where('distance_in_km','<=',40)
            ->orderBy('distance_in_km', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($page_size);

            $user_arr =[];
            foreach($users as $user) {
                if($user->distance_in_km >10000)
                        break;
                $user_arr[] = $user->toNearArray();
            }

        return response()->json([
                                    'code'=>200,
                                    'data'=>[
                                        'count'=>count($user_arr),
                                        'next'=>$users->nextPageUrl(),
                                        'prev'=>$users->previousPageUrl(),
                                        'results'=>$user_arr
                                    ]
                                ]);

        }
	
}
