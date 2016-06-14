<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\AccountRequest;
use App\User;
use Mockery\CountValidator\Exception;
use Validator;
use Mail;
class AccountRequestController extends Controller
{
    //
    /**
     * @SWG\Post(
     *     path="api/account/request",
     *     tags={"AccountRequest"},
     *     summary="Request account action",
     *     description="Request account action",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *          type="object",
     *          required={"email", "type"},
     *          @SWG\Property(
     *              property="email",
     *              type="string",
     *              description="User's email"
     *          ),
     *          @SWG\Property(
     *              property="type",
     *              type="string",
     *              description="Request type - 'reset', 'verify' is available"
     *          )
     *         )
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
        $validator = Validator::make($request->all(), [
            'email'=> 'email|required',
            'type' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['code' => 400,
                'message' => 'Some fields are missing or wrong',
                'errors' => $validator->errors()], 400);
        }

        $email = $request->input('email', '');
        $type_str = $request->input('type', '');

        $user = User::where('email', $email)->first();
        if(!$user)
            return response()->json(['code' => 404, 'message' => 'User was not found'], 404);

        $type = AccountRequest::fromTypeString($type_str);
        if(is_null($type)) {
            return response()->json(['code' => 400, 'message' => 'Request type is not valid'], 400);
        }

        $code = strval(rand(1000000, 9999999));
        AccountRequest::create(['user_id'=> $user->id, 'type'=>$type, 'code' => $code, 'processed' => false]);

        #TODO: send email to user
        try{
            Mail::send('emails.resetpassword', ['code'=>$code], function($message) use($email) {
                $message->to($email);
                $message->from('mailer@habbis.com');
                $message->subject('Password reset');
            });
        }
        catch(Exception $ex){}
        return response()->json(['code' => 200, 'message'=>'Request was sent']);
    }
    /**
     * @SWG\Get(
     *     path="api/account/request",
     *     tags={"AccountRequest"},
     *     summary="Check existing request",
     *     description="Check existing request",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="email",
     *          in="query",
     *          description="User's email",
     *          required=true,
     *          type="string"
     *     ),
     *     @SWG\Parameter(
     *          name="code",
     *          in="query",
     *          description="Request code",
     *          required=true,
     *          type="string"
     *     ),
     *     @SWG\Parameter(
     *          name="type",
     *          in="query",
     *          description="Request type",
     *          required=true,
     *          type="string"
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
    public function index(Request $request) {
        $email = $request->input('email', '');
        $code = $request->input('code', '');
        $type_str = $request->input('type', '');

        $user = User::where('email', $email)->first();
        if(!$user)
            return response()->json(['code'=>404, 'message'=>'User was not found'], 404);

        $type = AccountRequest::fromTypeString($type_str);
        if(is_null($type))
            return response()->json(['code'=>400, 'message'=>'Type is not valid'], 400);

        $account_request = AccountRequest::where('user_id', $user->id)
                            ->where('type', $type)->where('code', $code)->first();
        if(!$account_request) {
            return response()->json(['code'=> 404, 'message'=>'Your request was not found'], 404);
        } else if($account_request->processed) {
            return response()->json(['code'=> 429, 'message'=>'Your request was already processed'], 429);
        }
        return response()->json(['code' => 200]);
    }

    /**
     * @SWG\Post(
     *     path="api/account/password/reset",
     *     tags={"AccountRequest"},
     *     summary="Request account action",
     *     description="Request account action",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="data",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *          type="object",
     *          required={"email", "code", "password"},
     *          @SWG\Property(
     *              property="email",
     *              type="string",
     *              description="User's email"
     *          ),
     *          @SWG\Property(
     *              property="code",
     *              type="string",
     *              description="Request code"
     *          ),
     *          @SWG\Property(
     *              property="password",
     *              type="string",
     *              description="New password"
     *          )
     *         )
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
    public function resetPassword(Request $request) {
        $email = $request->input('email', '');
        $code = $request->input('code', '');
        $new_password = $request->input('password', '');
        $type_str = 'reset';

        $type = AccountRequest::fromTypeString($type_str);
        $user = User::where('email', $email)->first();
        if(!$user)
            return response()->json(['code'=>404, 'message'=>'User was not found'], 404);

        $account_request = AccountRequest::where('user_id', $user->id)
            ->where('type', $type)->where('code', $code)->first();
        if(!$account_request) {
            return response()->json(['code'=> 404, 'message'=>'Your request was not found'], 404);
        } else if($account_request->processed) {
            return response()->json(['code'=> 429, 'message'=>'Your request was already processed'], 429);
        }

        if(strlen($new_password) < 8)
            return response()->json(['code' => 400, 'message'=>'Password must be at least 8 in length'], 400);

        $account_request->processed = true;
        $account_request->save();
        $user->setPassword($new_password);
        $user->save();
        return response()->json(['code' => 200, 'message'=> 'Password was changed']);
    }
}
