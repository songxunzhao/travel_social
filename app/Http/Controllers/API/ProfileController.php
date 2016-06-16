<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
    		'name'=> 'required',
    		'birth' => 'date',
    		'location' => 'required',
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
        return response()->json(['code' => 200, 'data'=> $user->toArray()]);
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
