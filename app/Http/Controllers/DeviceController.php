<?php

namespace App\Http\Controllers;

use App\Models\devices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
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
     * @param  \App\Models\devices  $devices
     * @return \Illuminate\Http\Response
     */
    public function show(devices $devices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\devices  $devices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, devices $devices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\devices  $devices
     * @return \Illuminate\Http\Response
     */
    public function destroy(devices $devices)
    {
        //
    }
    // post log
    // public function postLogWithToken(Request $request)
    // {
    //     try {
    //         // get token
    //         $token = $request->bearerToken();
    //         // bcrypt token
    //         $token = hash('sha256', $token);
    //         // get userID by token
    //         $userID = DB::table('personal_access_tokens')->where('token', $token)->first()->tokenable_id;
    //         // check if user is admin
    //         $status = $request->status;

    //         // Corner case: user has no device
    //         if (DB::table('devices')->where('user_id', $userID)->doesntExist()) {
    //             return response()->json([
    //                 'message' => 'You have no device'
    //             ], 401);
    //         }

    //         $db_on_time = DB::table('devices')->where('user_id', $userID)->first();
    //         $db_on_time != null ? $db_on_time = $db_on_time->on_time : null;

    //         $db_off_time = DB::table('devices')->where('user_id', $userID)->first();
    //         $db_off_time != null ? $db_off_time = $db_off_time->off_time : null;

    //         $db_status = DB::table('devices')->where('user_id', $userID)->first();
    //         $db_status != null ? $db_status = $db_status->status : null;

    //         $db_total_time = DB::table('devices')->where('user_id', $userID)->first();
    //         $db_total_time != null ? $db_total_time = $db_total_time->total_active_time : null;

    //         // case: device already off
    //         if ($status == 'off' && $db_on_time == null) {
    //             return response()->json([
    //                 'message' => 'fail',
    //                 'error' => 'device already off'
    //             ], 200);
    //         }

    //         // case: device is online
    //         if ($db_status == 'on' && $status == 'off') {
    //             $on_time = $db_on_time;
    //             $off_time = date('Y-m-d H:i:s');

    //             $on_time = strtotime($on_time);
    //             $off_time = strtotime($off_time);
    //             $db_total_time = strtotime($db_total_time);

    //             $time = $off_time - $on_time;
    //             $db_total_time = $db_total_time + $time;

    //             $db_total_time = date('H:i:s', $db_total_time);

    //             DB::table('devices')->where('user_id', $userID)->update(['off_time' => date('Y-m-d H:i:s'), 'status' => 'off', 'total_active_time' => $db_total_time]);
    //         }
    //         // case: device is offline
    //         if (($db_status == 'off' || $db_status == null) && $status == 'on') {
    //             DB::table('devices')->where('user_id', $userID)->update(['on_time' => date('Y-m-d H:i:s'), 'status' => 'on']);
    //         }

    //         return response()->json([
    //             'message' => 'success',
    //             'total_active_time' => $db_total_time,
    //             'status' => $db_status
    //         ], 200);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'message' => 'fail',
    //             'error' => $th->getMessage(),
    //         ], 500);
    //     }
    // }

    public function postLog(Request $request)
    {
        try {

            // check if user is admin
            $status = $request->status;
            $deviceID = '1';

            $db_on_time = DB::table('devices')->where('id', $deviceID)->first();
            $db_on_time != null ? $db_on_time = $db_on_time->on_time : null;

            $db_off_time = DB::table('devices')->where('id', $deviceID)->first();
            $db_off_time != null ? $db_off_time = $db_off_time->off_time : null;

            $db_status = DB::table('devices')->where('id', $deviceID)->first();
            $db_status != null ? $db_status = $db_status->status : null;

            $db_total_time = DB::table('devices')->where('id', $deviceID)->first();
            if ($db_total_time != null) {
                if ($db_total_time->total_active_time != null) {
                    // day of now
                    $day = date('d');
                    // day of db_off_time
                    $db_off_time_day = date('d', strtotime($db_off_time));
                    if ($day != $db_off_time_day) {
                        $db_total_time = '00:00:00';
                        //update total_active_time
                        DB::table('devices')->where('id', $deviceID)->update(['total_active_time' => $db_total_time]);
                    } else {
                        $db_total_time = $db_total_time->total_active_time;
                    }
                } else {
                    $db_total_time = '00:00:00';
                }
            }


            // case: device already off
            if ($status == 'off' && $db_on_time == null) {
                return response()->json([
                    'message' => 'fail',
                    'error' => 'device already off'
                ], 200);
            }

            // case: device is online
            if ($db_status == 'on' && $status == 'off') {
                $on_time = strtotime($db_on_time);
                $off_time = date('Y-m-d H:i:s');

                $off_time = strtotime($off_time);
                $db_total_time = strtotime($db_total_time);

                $time = $off_time - $on_time;
                $db_total_time = $db_total_time + $time;

                $db_total_time = date('H:i:s', $db_total_time);

                DB::table('devices')->where('id', $deviceID)->update(['off_time' => date('Y-m-d H:i:s'), 'status' => 'off', 'total_active_time' => $db_total_time]);
                $db_status = 'off';
            }
            // case: device is offline
            if (($db_status == 'off' || $db_status == null) && $status == 'on') {
                DB::table('devices')->where('id', $deviceID)->update(['on_time' => date('Y-m-d H:i:s'), 'status' => 'on', 'total_active_time' => $db_total_time]);
                $db_status = 'on';
            }

            return response()->json([
                'message' => 'success',
                'total_active_time' => $db_total_time,
                'status' => $db_status
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'fail',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
