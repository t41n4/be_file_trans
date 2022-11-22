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
        ]);

        $user = User::create([
            'username' => $fields['username'],
            'password' => bcrypt($fields['password']),
            'name' => $fields['name'],
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



        $response = [
            'user' => $userReturnData,
            'token' => $token,

            'message' => 'Login success'
        ];



        return response($response, 201);
    }

    public function logout(User $user)
    {
        $user->tokens()->delete();

        return [
            'message' => 'User logged out'
        ];
    }
}
