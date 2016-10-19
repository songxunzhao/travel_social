<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Validator;
use App\Chat;
use App\User;
use PushNotification;
use App\Notification;

class ChatController extends Controller
{
    //
	protected function validator($data) {
        return Validator::make($data,
            [
                'email'=>'email|required',
                'type'=>'required',
                'message'=>'required'
            ]
        );
    }
	
       public function store(Request $request) {
        $user = $request->user();
        $validator = $this->validator($request->all());
        $request_data = $request->all();
	$receiver = User::whereEmail($request_data['email'])->first();
	if(!$receiver)
		 return response()->json(['code'=>400,'message'=>'No user with this email']);
	$request_data['rid'] = $receiver->id;
        $request_data['sid'] = $user->id;
        if($validator->fails()) {
            return response()->json(['code'=>400, 'errors'=> $validator->errors(),
                                    'message'=>'Some fields are missing or wrong']);
        }

        $chat = Chat::create($request_data);
        //$chat->save();
        $message = PushNotification::Message('Message Text',array(
    'badge' => +1,
    'sound' => 'default',

    'actionLocKey' => 'respond to the alert!',
    'locKey' => 'Message from '.$user->name,
    'locArgs' => array(
        'localized args',
        'localized args',
    ),
    'launchImage' => 'image.jpg',

    'custom' => array('custom data' => array(
        'we' => 'want', 'send to app'
    ))
));
              $notData = array('creator_id' => $user->id, 'uid' => $receiver->id,'type'=>2,'nid'=>$chat->id);
              //print_r($not_data);
              $not = Notification::create($notData);
              $not->save();
              $deviceToken = $receiver->device_token;
		if($deviceToken!='')
                {
               //$deviceToken = 'd24d4776596e1a8d05049103464845a891c72ee8e1fac0e42d621d05e86727a9';
       	  $deviceToken = '3f48cc22ee4a0a748b7c3ccba4c20bb5b1a7d0c1ca6ff2782cc83b4067a4626b';
		$push = PushNotification::app('appNameIOS')
                ->to($deviceToken)
                ->send($message);
                dd($push);
		}

        return response()->json(['code'=>200, 'data'=> $chat->toArray()]);
    }

	public function index(Request $request) {
        $user = $request->user();
	$uid = $user->id;
       // $page_size = $request->input('page_size', 10);
        
            $chats = Chat::where('type','1')
			->where('rid',$uid)
//			->orWhere('sid',$uid)
			->orderBy('created_at', 'desc')
			->groupBy('rid')
			->get();
            $chat_arr =[];
		$unread = 0;
$threads = [];
            foreach($chats as $chat) {
		if ($chat->rid == $uid) {
			if (in_array($chat->sid, $threads)) {
				continue;
			}
				$threads[] = $chat->sid;
		} else {
			if (in_array($chat->rid, $threads)) {
				continue;
			}
			$threads[] = $chat->rid;
		}
		
               $chat_arr[] = $chat->toInboxArray();
		if(!$chat->is_read)
				$unread += 1;
            }
//	$chat_arr['unread']= $unread;
        return response()->json([
                                    'code'=>200,
                                    'data'=>$chat_arr,
				    'unread'=>$unread     
				]);
    }

	public function admin(Request $request) {
        $user = $request->user();
        $uid = $user->id;
       // $page_size = $request->input('page_size', 10);


            $chats = Chat::where('type','3')
                        ->where('rid',$uid)
                        ->orderBy('created_at', 'desc')
                        ->get();
            $chat_arr =[];
                $unread = 0;
            foreach($chats as $chat) {
                $chat_arr[] = $chat->toAdminArray();
                if(!$chat->is_read)
                        $unread += 1;
            }
//      $chat_arr['unread']= $unread;
        return response()->json([
                                    'code'=>200,
                                    'data'=>$chat_arr,
                                    'unread'=>$unread
                                ]);
    }

	public function show(Request $request) {
        $user = $request->user();
        $uid = $user->id;
       // $page_size = $request->input('page_size', 10);


            $chats = Chat::where('type','1')
                        ->where('rid',$uid)
			->where('starred',1)
                        ->orderBy('created_at', 'desc')
                        ->get();
            $chat_arr =[];
            foreach($chats as $chat) {
                $chat_arr[] = $chat->toInboxArray();
            }

        return response()->json([
                                    'code'=>200,
                                    'data'=>$chat_arr
                                ]);
    }

	public function sent(Request $request) {
        $user = $request->user();
        $uid = $user->id;
       // $page_size = $request->input('page_size', 10);


            $chats = Chat::where('type','1')
                        ->where('sid',$uid)
                        ->orderBy('created_at', 'desc')
                        ->get();
            $chat_arr =[];
            foreach($chats as $chat) {
                $chat_arr[] = $chat->toSentArray();
            }

        return response()->json([
                                    'code'=>200,
                                    'data'=>$chat_arr
                                ]);
    }
	
	public function star(Request $request) {
        $user = $request->user();
	$request_data = $request->all();
	
	$msgId = $request_data['mid'];
        $chat = Chat::where('id',$msgId)->where('rid',$user->id)->first();
        if(!$chat)
            return response()->json(['code'=>404, 'message'=> 'Chat was not found'], 404);

        $chat->starred = 1;
        $chat->save();

        return response()->json(['code'=>200, 'message'=>'Chat starred']);
    }
      	
       public function unstar(Request $request) {
        $user = $request->user();
        $request_data = $request->all();

        $msgId = $request_data['mid'];
        $chat = Chat::where('id',$msgId)->where('rid',$user->id)->first();
        if(!$chat)
            return response()->json(['code'=>404, 'message'=> 'Chat was not found'], 404);

        $chat->starred = 0;
        $chat->save();

        return response()->json(['code'=>200, 'message'=>'Chat unstarred']);
    }

     public function read(Request $request) {
        $user = $request->user();
        $request_data = $request->all();

        $msgId = $request_data['mid'];
        $chat = Chat::where('id',$msgId)->where('rid',$user->id)->first();
        if(!$chat)
            return response()->json(['code'=>404, 'message'=> 'Chat was not found'], 404);

        $chat->is_read = 1;
        $chat->save();

        return response()->json(['code'=>200, 'message'=>'Chat marked read']);
    }  
	public function getall(Request $request, $email) {
        $user = $request->user();
        $uid = $user->id;
	$request_data = $request->all();//print_r($request_data);
	//$email = $request_data['email'];
        $page_size = $request->input('page_size', 10);
	$receiver = User::whereEmail($email)->first();
        if(!$receiver)
                 return response()->json(['code'=>400,'message'=>'No user with this email']);
	$uid2 = $receiver->id;
            $chats = Chat::where('type','1')
                        ->where(function ($query) use($uid,$uid2){
              		  $query->where('sid', '=',$uid)
                      ->where('rid', '=',$uid2);
            		})
			->orWhere(function ($query) use($uid,$uid2){
	                $query->where('sid', '=', $uid2)
                      ->where('rid', '=', $uid);
        	        })
                        ->orderBy('created_at', 'desc')
                        ->paginate($page_size);
            $chat_arr =[];
            foreach($chats as $chat) {
                $chat_arr[] = $chat->toArray();
            }
	

        return response()->json([
                                    'code'=>200,
                                    'data'=>[
                                        'count'=>$chats->count(),
                                        'next'=>$chats->nextPageUrl(),
                                        'prev'=>$chats->previousPageUrl(),
					'name'=> $receiver->name,
					'email'=>$receiver->email,
					'profile_img'=> $receiver->profile_img,
                                        'results'=>$chat_arr
                                    ]
                                ]);
    }
}
