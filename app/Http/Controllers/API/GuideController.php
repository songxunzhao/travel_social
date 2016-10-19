<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Guide;
use App\Event;
use App\EventMember;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;

class GuideController extends Controller
{
    //
	public function store(Request $request) {
        $user = $request->user();

        $guide = Guide::where('type','1');
        if(!$guide)
            return response()->json(['code'=>404, 'message'=> 'Guide was not found'], 404);
	 else 
        	return response()->json(['code'=>200, 'data'=>$guide]);
    }
  public function index(Request $request) {
        $user = $request->user();
        $page_size = $request->input('page_size', 10);
        $lat = $user->lat;
        $lng = $user->lng;
	$request_data = $request->all();
	$type = $request_data['type'];
	if($type ==0){
/*$events = Event::where('creator_id','3')->orderBy('created_at', 'desc')->paginate($page_size);
            $event_arr =[];
            foreach($events as $event) {
                $event_arr[] = $event->toSummaryArray();
            }

/*/         $guides = Guide::selectRaw('*, p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(guides.lat))
                 * COS(RADIANS(p.longpoint) - RADIANS(guides.lng))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(guides.lat)))) AS distance_in_km')->join(DB::raw("(SELECT  $lat AS latpoint,  $lng AS longpoint,
                        100.0 AS radius,      111.045 AS distance_unit) as p"), function($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1'));
            })->orderBy('distance_in_km', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($page_size);
	}else{
	$guides = Guide::selectRaw('*, p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(guides.lat))
                 * COS(RADIANS(p.longpoint) - RADIANS(guides.lng))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(guides.lat)))) AS distance_in_km')->join(DB::raw("(SELECT  $lat AS latpoint,  $lng AS longpoint,
                        100.0 AS radius,      111.045 AS distance_unit) as p"), function($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1'));
            })->where('type', $type)
            ->orderBy('distance_in_km', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($page_size);}
//	if($guides==''){ return response()->json(['code'=>'404','data'=>'null']);}
	//var_dump($guides);
//	`$gde = $guides->toArray();
	//dd($guides);
	$gde = [];
	foreach ($guides as $guide){
	$gde[]=$guide->toArray();
	//echo "guide:".$guide;
}
 				return response()->json([
                                    'code'=>200,
                                    'data'=>[
                                        'count'=>$guides->count(),
                                        /*'next'=>$guides->nextPageUrl(),
                                        'prev'=>$guides->previousPageUrl(),*/
                                        'results'=>$gde
                                    ]
                                ]);
	}
	
	 public function show(Request $request, $guideId) {
        $user = $request->user();
        $guide = Guide::find($guideId);
        if($guide)
            return response()->json(['code'=>200, 'data'=>$guide->toCompleteArray()]);
        else
            return response()->json(['code'=>404, 'message'=>'Guide was not found']);
    }	

}
