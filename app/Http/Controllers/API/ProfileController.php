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
    		'age' => 'numeric',
    		'location' => 'required',
    		'lat' => 'numeric',
    		'lng' => 'numeric',
    	]);
    }
    public function store(Request $request) {
        $user = $request->user();
    	$validator = $this->validator($request->all());
    	if($validator->fails()) {
    		return response()->json(['code' => 400, 
    								'message' => 'Bad request format', 
    								'errors' => $validator->errors()]);
    	}
        $request_data = $request->only(['name', 
                                        'age', 
                                        'location', 
                                        'lat', 
                                        'lng', 
                                        'job_name']);
        $user->update($request_data);
        return response()->json(['code' => 200]);
    }

    public function index(Request $request) {
        $user = $request->user();
        return response()->json(['code' => 200, 'data' => $user->toArray()]);
    }
}
