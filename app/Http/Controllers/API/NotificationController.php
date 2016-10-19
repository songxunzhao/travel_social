<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use App\Http\Requests;

class NotificationController extends Controller
{

	public function index(Request $request){
         $user = $request->user();
	 $page_size = $request->input('page_size', 10);	 
	 $notifications = Notification::where('uid', $user->id)->orderBy('created_at', 'desc')->paginate($page_size);
            $not_arr =[];
            foreach($notifications as $notification) {
                $not_arr[] = $notification->toSummaryArray();
            }  	 
	return response()->json([
                                    'code'=>200,
                                    'data'=>[
                                        'count'=>$notifications->count(),
                                        'next'=>$notifications->nextPageUrl(),
                                        'prev'=>$notifications->previousPageUrl(),
                                        'results'=>$not_arr
                                    ]
                                ]);	
	
	}
    //
}
