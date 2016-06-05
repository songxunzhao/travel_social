<?php

namespace App\Http\Controllers\API;

use App\UserInvite;
use App\User;
use Illuminate\Http\Request;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class InviteUserController extends Controller
{
    //
    protected function validator($data) {
        return Validator::make($data, [
            'email' => 'required|email',
        ]);
    }

    /**
     * @SWG\Post(
     *     path="api/user/invite",
     *     tags={"User"},
     *     summary="Invite user by email",
     *     description="Invite user by email",
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
     *              required={"email"},
     *              @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  description="Invitee's email"
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
     *                  property="message",
     *                  type="string",
     *                  description="Response message"
     *               )
     *         )
     *      )
     * )
     */
    public function invite(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()) {
            return response()->json(['code' => 400, 'errors' => $validator->errors()]);
        }

        //TODO: Send invitation email to user

        //Add invitation record
        $user = $request->user();

        $email = $request->input('email', '');
        $email_user = User::where('email', $email)->first();
        if($email_user)
        {
            return response()->json(['code' => 429, 'message'=> 'User already exist'], 429);
        }

        $user_id = $user->id;
        $invite = UserInvite::where('email', $email)->where('user_id', $user_id)->first();
        if($invite){
            return response()->json(['code' => 200, 'message'=> 'You already sent invitation']);
        }
        $data = [
            'uuid' => UserInvite::getuuid(),
            'email' => $email,
            'user_id' => $user_id
        ];
        $user_invite = UserInvite::create($data);
        return response()->json(['code' => 200, 'message'=> 'Request was created']);

    }
}
