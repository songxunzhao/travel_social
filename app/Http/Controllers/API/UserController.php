<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    //
    /**
     * @SWG\Get(
     *     path="api/users/{user_id}",
     *     tags={"User"},
     *     summary="Get user's detail",
     *     description="Get user's detail",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="user_id",
     *          in="path",
     *          description="User id",
     *          required=true,
     *          type="string"
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
     *                  @SWG\Schema(ref="#/definitions/User")
     *               )
     *         )
     *      )
     * )
     */
    public function show($userId) {
        $user = User::where('id', $userId)->first();
        if($user)
            return response()->json(['code'=>200, 'data'=> $user->toProfileArray()]);
        else
            return response()->json(['code'=>404, 'message'=>'User was not found'], 404);
    }
}
