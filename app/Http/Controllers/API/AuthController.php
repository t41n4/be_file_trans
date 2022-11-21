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
    // const timeout = 60;
    // 30 days
    const timeout = 60 * 60 * 24 * 30;

    public function register(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string|unique:users,username|min:3',
            'password' => 'required|min:4|confirmed',
        ]);

        $user = User::create([
            'username' => $fields['username'],
            'userRoleID' => '2',
            'password' => bcrypt($fields['password'])
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
            'userID' => $user->userID,
            'userRoleID' => $user->userRoleID,
            'username' => $user->username,
        ];

        $total_active_time = DB::table('devices')->where('id', '1')->first();

        $response = [
            'user' => $userReturnData,
            'token' => $token,
            'total_active_time' => $total_active_time == null ? 0 : $total_active_time->total_active_time,
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
