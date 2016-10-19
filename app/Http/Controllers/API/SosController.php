<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Validator;
use App\Sos;
use App\User;
use App\Notification;
use DB;
use PushNotification;

class SosController extends Controller
{
    //

	 protected function validator($data) {
        return Validator::make($data, [
            'message'=>'required',
            'lat'=>'numeric|required',
            'lng'=>'numeric|required',
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

                $sos = Sos::create($request_data);
                //$sos->save();
		$lat = $request_data['lat'];
		$lng = $request_data['lng'];
		
		$usrs = User::selectRaw('*, p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(users.lat))
                 * COS(RADIANS(p.longpoint) - RADIANS(users.lng))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(users.lat)))) AS distance_in_km')->join(DB::raw("(SELECT  $lat AS latpoint,  $lng AS longpoint,
                        100.0 AS radius,      111.045 AS distance_unit) as p"), function($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1'));
            })->where('id','!=', $user->id)
            ->orderBy('distance_in_km', 'asc')
            ->orderBy('created_at', 'desc')->get();
		/*echo "count:".count($usrs);		
            $message = PushNotification::Message('Message Text',array(
    'badge' => +1,
    'sound' => 'default',

    'actionLocKey' => 'respond to the alert!',
    'locKey' => 'Sos Alert!!',
    'locArgs' => array(
        'localized args',
        'localized args',
    ),
    'launchImage' => 'image.jpg',

    'custom' => array('custom data' => array(
        'we' => 'want', 'send to app'
    ))
));*/
            foreach($usrs as $usr) {
		//echo "username:".$usr->name;
                if($usr->distance_in_km >3)
                        break;
	      $notData = array('creator_id' => $user->id, 'uid' => $usr->id,'type'=>1,'nid'=>$sos->id);
	      //print_r($not_data);
	      $not = Notification::create($notData);
	      $not->save();
              //$deviceToken = $usr->device_token;
	      //if(!$deviceToken)
		//break;
		$deviceToken = '3f48cc22ee4a0a748b7c3ccba4c20bb5b1a7d0c1ca6ff2782cc83b4067a4626b';
		PushNotification::app('appNameIOS')
                ->to($deviceToken)
                ->send("sos alert");
		}
                return response()->json(['code'=>200, 'data'=> $sos->toArray()]);
        }


}
