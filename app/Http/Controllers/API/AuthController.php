<?php

namespace App\Http\Controllers\API;

use App\UserInvite;
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

    /**
     * @SWG\Post(
     *     path="api/account/login",
     *     tags={"User"},
     *     summary="Login",
     *     description="Login user by email and password",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Login data",
     *          required=true,
     *          type="string",
     *           @SWG\Schema(
     *              type="object",
     *              required={"email", "password"},
     *              @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  description="User's email"
     *              ),
     *              @SWG\Property(
     *                  property="password",
     *                  type="string",
     *                  description="User's password, must be longer than 8 in length"
     *              )
     *           )
     *      ),
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
     *                  type="object",
     *                  description="Response data",
     *                  @SWG\Property(
     *                      property="token",
     *                      type="string"
     *                  ),
     *                  @SWG\Property(
     *                      property="complete_profile",
     *                      type="boolean"
     *                  )
     *              )
     *         )
     *      )
     * )
     */
    public function login(Request $request){
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

        $user = JWTAuth::toUser($token);

        if((is_null($user->name) || $user->name == "") ||
            ($user->age == 0) ||
            (is_null($user->job_name) || $user->job_name == ""))
            $complete_profile = false;
        else
            $complete_profile = true;
        // all good so return the token
        return response()->json([
            'code' => 200,
            'data' => ['token' => $token, 'complete_profile' => $complete_profile]]);
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

    /**
     * @SWG\Post(
     *     path="api/account/register",
     *     tags={"User"},
     *     summary="Register",
     *     description="Register user. name, email and password fields are required",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Register data",
     *          required=true,
     *          type="string",
     *           @SWG\Schema(
     *              type="object",
     *              required={"name", "email", "password"},
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  description="User's name"
     *              ),
     *              @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  description="User's email"
     *              ),
     *              @SWG\Property(
     *                  property="password",
     *                  type="string",
     *                  description="User's password, must be longer than 8 in length"
     *              )
     *           ),
     *      ),
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
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required|max:255', 
            'email'=> 'required|email|unique:users|max:255',
            'password'=> 'required|min:8'
            ]);
        if($validator->fails()) {
            return response()->json(['code' => 200, 
                'errors' => $validator->errors()]);
        }

        $email = $request->input('email');
        $invite = UserInvite::where('email', $email)->first();
        if(!$invite){
            return response()->json(['code' => 403, 'message' => 'You must get invitation to register'], 403);
        }
        $user = $this->create($request->all());
        $invite->registered_id = $user->id;
        $invite->save();
        
        return response()->json(['code' => 200], 200);
    }
}
