<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Guide;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	$guides = DB::table('guides')->orderBy('created_at','desc')->get();

        return view('home',compact('guides'));
    }

    public function store(Request $request){
//	$myfile = fopen("check_habbis.txt", "w") or die("Unable to open file!");
  //              fwrite($myfile, "hit");
    //            fwrite($myfile, $request->title);

      //         fclose($myfile);
        if ($request->hasFile('file')) {
            $orignal_file_name = $request->files->get('file')->getClientOriginalName();
            $ext = pathinfo($orignal_file_name, PATHINFO_EXTENSION);

            $file = $request->file('file');
            $upload_dir = realpath(public_path()) . DIRECTORY_SEPARATOR . 'uploads';

            $filename = str_random(16) . '.' . $ext;
            $file->move($upload_dir, $filename);
            $url = url('uploads/' . $filename);
        }
	$request_data = $request->all();
//	print_r($request_data);
//	$request_data = $request->all();
	$request_data['img'] = $url;
//	print_r($request_data);
	$guide = Guide::create($request_data);

	$guides = DB::table('guides')->orderBy('created_at','desc')->get();

        return view('home',compact('guides'));
   }
}
