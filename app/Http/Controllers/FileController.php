<?php

namespace App\Http\Controllers;

use App\Models\files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\files  $files
     * @return \Illuminate\Http\Response
     */


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\files  $files
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\files  $files
     * @return \Illuminate\Http\Response
     */

    // get file from database
    public function getFile(Request $request)
    {
        // get bearer token
        $token = $request->bearerToken();
        // hash token
        $token = hash('sha256', $token);
        // get user id from token
        $user_id = DB::table('personal_access_tokens')->where('token', $token)->first()->tokenable_id;
        // get every file that to this user
        $files = DB::table('files')->where('user_id_to', $user_id)->get();
        // add user name to file
        foreach ($files as $file) {
            $file->From = DB::table('users')->where('id', $file->user_id_from)->first()->name;
        }
        return response()->json($files);
    }
}
