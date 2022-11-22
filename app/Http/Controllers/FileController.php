<?php

namespace App\Http\Controllers;

use App\Models\files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    // get file from database
    public function getfile(Request $request)
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
    public function getuser (Request $request)
    {
       //get username, name, public_key, status every user that online
        $users = DB::table('users')->where('status', 'online')->get();

        return response()->json($users);
    }
}
