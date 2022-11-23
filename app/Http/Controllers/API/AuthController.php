<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function checkToken(Request $request){
        // if token exist in personal access token table
        // hash token
        if (DB::table('personal_access_tokens')->where('token',  hash('sha256', $request->bearerToken()))->first()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Token is valid',
                'public_key' => DB::table('users')->where('id', DB::table('personal_access_tokens')->where('token',  hash('sha256', $request->bearerToken()))->first()->tokenable_id)->first()->public_key,
                'username' => DB::table('users')->where('id', DB::table('personal_access_tokens')->where('token',  hash('sha256', $request->bearerToken()))->first()->tokenable_id)->first()->username,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is invalid',
            ], 401);
        }
    }
    public function username(){
        return 'username';
    }
    // const timeout = 60;
    // 30 days
    const timeout = 60 * 60 * 24 * 30;
    public function register(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string|unique:users,username|min:3',
            'password' => 'required|min:4|confirmed',
            'name' => 'required|string',
            'public_key' => 'required|string',
        ]);

        $user = User::create([
            'username' => $fields['username'],
            'password' => bcrypt($fields['password']),
            'name' => $fields['name'],
            'public_key' => $fields['public_key'],
            'status' => 'online',
        ]);

        $token = $user->createToken('myToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
            'message' => 'User created'
        ];
        return response($response, 201);

        return response()->json([
            'message' => 'You have no permission'
        ], 401);
    }

    public function login(Request $request)
    {
        // if empty username or password
        if (empty($request->username) || empty($request->password)) {
            return response()->json([
                'message' => 'Username or password is empty'
            ], 401);
        }
        $fields = $request->validate([
            'username' => 'required|string|min:3',
            'password' => 'required|min:4',
        ]);

        $user = User::where('username', $fields['username'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Invalid username or password'
            ], 401);
        };

        // clear all the token of this user
        $user->tokens()->delete();
        // create new token
        $token = $user->createToken('loginToken')->plainTextToken;
        // add expire time to personal access token
        // $user->tokens()->where('name','loginToken')->update(['expires_at' => now()->addDay(1)]);
        $user->tokens()->where('name', 'loginToken')->update(['expires_at' => now()->addMinute(self::timeout)]);

        // remove username field in user data
        $userReturnData = [
            'userID' => $user->id,
            'username' => $user->username,

        ];

        //update status to online
        DB::table('users')->where('id', $user->id)->update(['status' => 'online']);
        $response = [
            'user' => $userReturnData,
            'token' => $token,
            'public_key' => $user->public_key,
            'message' => 'Login success'
        ];

        return response($response, 201);
    }

    public function logout(User $user)
    {
        // get bearer token
        $token = request()->bearerToken();
        // hash token
        $token = hash('sha256', $token);
        // get user id from token
        $user_id = DB::table('personal_access_tokens')->where('token', $token)->first()->tokenable_id;
        // delete token
        DB::table('personal_access_tokens')->where('token', $token)->delete();
        // update status to offline
       DB::table('users')->where('id', $user_id)->update(['status' => 'offline']);
        return [
            'message' => 'User logged out'
        ];
    }
}
