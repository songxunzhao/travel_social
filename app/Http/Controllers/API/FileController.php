<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FileController extends Controller
{
    //
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
                                'message' => 'Bad request format']);
   	}
}
