<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Event;
use App\EventMember;
class EventMemberController extends Controller
{
    /**
     * @SWG\Get(
     *     path="api/events/{event_id}/members",
     *     tags={"EventMember"},
     *     summary="Get event members",
     *     description="Get event members",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="event_id",
     *          in="path",
     *          description="Event id",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          name="page_size",
     *          in="query",
     *          description="Page size",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
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
     *                  type="string"
     *               )
     *         )
     *      )
     * )
     */
    public function index(Request $request, $eventId) {
        $user = $request->user();
        $page_size = $request->input('page_size', 10);

        $event = Event::find($eventId);
        if(!$event)
            return response()->json(['code'=>404, 'message'=> 'Event was not found'], 404);

        $members = EventMember::where('event_id', $event->id)->paginate($page_size);
        $member_arr = [];
        foreach($members as $member) {
            $member_arr[] = $member->toDetailArray();
        }
        return response()->json([
            'code'=>200,
            'data'=>[
                'count'=>$members->count(),
                'next'=>$members->nextPageUrl(),
                'prev'=>$members->previousPageUrl(),
                'results'=>$member_arr
            ]
        ]);
    }
}
