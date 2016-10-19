<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

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

	public function getUsers(Request $request){

	$user = $request->user();
        $page_size = $request->input('page_size', 10);
        $lat = $user->lat;
        $lng = $user->lng;

	 $users = User::selectRaw('*, p.distance_unit
                 * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                 * COS(RADIANS(users.lat))
                 * COS(RADIANS(p.longpoint) - RADIANS(users.lng))
                 + SIN(RADIANS(p.latpoint))
                 * SIN(RADIANS(users.lat)))) AS distance_in_km')->join(DB::raw("(SELECT  $lat AS latpoint,  $lng AS longpoint,
                        100.0 AS radius,      111.045 AS distance_unit) as p"), function($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1'));
            })->where('id','!=',$user->id)
	    ->orderBy('distance_in_km', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($page_size);

            $user_arr =[];
            foreach($users as $user) {
		if($user->distance_in_km >10000)
			break;
                $user_arr[] = $user->toNearArray();
            }
   
        return response()->json([
                                    'code'=>200,
                                    'data'=>[
                                        'count'=>count($user_arr),
                                        'next'=>$users->nextPageUrl(),
                                        'prev'=>$users->previousPageUrl(),
                                        'results'=>$user_arr
                                    ]
                                ]);

	}
}
