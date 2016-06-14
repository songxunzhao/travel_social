<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FileController extends Controller
{
    //
    /**
     * @SWG\Post(
     *     path="api/files",
     *     tags={"Files"},
     *     summary="Upload file, return url",
     *     description="Upload file",
     *     consumes={"multipart/form-data"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         required=true,
     *         type="file"
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
     *               ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  description="Response code",
     *                  @SWG\Property(
     *                      property="url",
     *                      type="string",
     *                      description="Link to uploaded image"
     *                  )
     *               )
     *         )
     *      )
     * )
     */
   	public function upload(Request $request) {
        if ($request->hasFile('file')) {
            $orignal_file_name = $request->files->get('file')->getClientOriginalName();
            $ext = pathinfo($orignal_file_name, PATHINFO_EXTENSION);
            
            $file = $request->file('file');
            $upload_dir = realpath(public_path()) . DIRECTORY_SEPARATOR . 'uploads';
            
            $filename = str_random(16) . '.' . $ext;
            $file->move($upload_dir, $filename);
            return response()->json(['code'=>200, 
                                    'data'=>[
                                        'url'=>url('uploads/' . $filename)
                                    ]
                                    ]);
        }
        return response()->json(['code' => 400,
                                'message' => 'Some fields are missing']);
   	}
}
