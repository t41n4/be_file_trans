<?php

namespace App\Http\Controllers;

use App\Models\files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    // update public key
    public function updatePublicKey(Request $request){
        $fields = $request->validate([
            'public_key' => 'required|string',
        ]);
        $user = DB::table('users')->where('id', $request->user()->id)->update([
            'public_key' => $fields['public_key'],
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Public key updated',
        ], 200);
    }
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
            $file->From = DB::table('users')->where('id', $file->user_id_from)->first()->username;
        }
        return response()->json($files);
    }
    public function getuser (Request $request)
    {
       //get username, name, public_key, status every user that online
        $users = DB::table('users')->where('status', 'online')->get();

        return response()->json($users);
    }
    // uploadfile
    public function uploadfile(Request $request){
        try {
            // insert file
        $file = new files;
        $file->user_id_from =  DB::table('personal_access_tokens')->where('token', hash('sha256', $request->bearerToken()))->first()->tokenable_id;
        $file->user_id_to = DB::table('users')->where('username', $request->user_to)->first()->id;
        $file->file_name = $request->file_name;
        $file->file_link = $request->file_link;
        $file->file_signature = $request->file_signature;
        $file->time_upload = date('Y-m-d H:i:s');
        $file->aes_key = $request->aes_key;
        $file->aes_iv = $request->aes_iv;
        $file->save();

        return response()->json([
            'status' => 'success',
            'message' => 'File uploaded',
        ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'File upload failed',
                'error' => $th->getMessage(),
            ], 401);
        }

    }
}
