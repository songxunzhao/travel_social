<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Image;

class ProfileController extends Controller
{
    //
    public function __construct()
    {
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the authenticate method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
    }
    protected function validator($data) {
    	return Validator::make($data, [
    		'birth' => 'date',
    		'lat' => 'numeric',
    		'lng' => 'numeric'
    	]);
    }

   
    /**
     * @SWG\Post(
     *     path="api/account/profile",
     *     tags={"Account"},
     *     summary="Profile",
     *     description="Get user profile",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/User")
     *     ),
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
     *               )
     *         )
     *      )
     * )
     */
    public function store(Request $request) {
        $user = $request->user();
    	$validator = $this->validator($request->all());
    	if($validator->fails()) {
    		return response()->json(['code' => 400, 
    								'message' => 'Some fields are missing or wrong',
    								'errors' => $validator->errors()]);
    	}
        $request_data = $request->except('email', 'password');
        $user->update($request_data);
        $complete_profile = $user->isCompleteProfile();
        return response()->json(['code' => 200,
            'data'=> ['complete_profile'=>$complete_profile, 'user'=>$user->toArray()]]);
    }

    public function image(Request $request){
 	$user = $request->user();
	$request_data = $request->all();
        $request_data['uid'] = $user->id; 
//        if($validator->fails()) {
 //             return response()->json(['code'=>400, 'errors'=> $validator->errors(),
   //                                 'message'=>'Some fields are missing or wrong']);
     //    }
	$image = Image::where('uid',$user->id)->where('num',$request_data['num'])->first();

	if(!$image){
		$imgs = Image::create($request_data);
		$imgs->save();
		}else{
		$image->image = $request_data['image'];
		$image->save();
	}
			
	 return response()->json(['code' => 200, 'data' => $user->toProfileArray()]);	
    }
    /**
     * @SWG\Get(
     *     path="api/account/profile",
     *     tags={"Account"},
     *     summary="Profile",
     *     description="Get user profile",
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
     *                  description="Response data",
     *                  ref="#/definitions/User"
     *              )
     *         )
     *      )
     * )
     */
    public function index(Request $request) {
        $user = $request->user();
        return response()->json(['code' => 200, 'data' => $user->toProfileArray()]);
    }
}
