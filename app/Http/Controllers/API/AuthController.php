<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    //
    public function index(){
    	$users = User::all();
    	return $users;
    }
    public function __construct()
    {
       // Apply the jwt.auth middleware to all methods in this controller
       // except for the authenticate method. We don't want to prevent
       // the user from retrieving their token if they don't already have it
       
    }
    
    public function signin(Request $request){
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    ['code' => 401, 'error' => 'invalid_credentials'], 
                    401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'code' => 500, 
                'error' => 'could_not_create_token'], 
                500);
        }

        // all good so return the token
        return response()->json([
            'code' => 200,
            'data' => ['token' => $token]]);
    }
    
    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required|max:255', 
            'email'=> 'required|email|unique:users|max:255',
            ]);
        if($validator->fails()) {
            return response()->json(['code' => 200, 
                'error' => $validator->errors()]);
        }

        $this->create($request->all());
        return response()->json(['code' => 200], 200);
    }
}
