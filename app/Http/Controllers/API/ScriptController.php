<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Models\Hospital;
use Hash;
use App\Models\User;
use Config;
use DateTime;
use finfo;
use Illuminate\Support\Facades\Date;
use Session;
use Illuminate\Support\Carbon;
use PDF;
use App;
use App\Models\Booking;
use App\Models\SigneeOrganization;

class ScriptController extends Controller
{
    public $successStatus = 200;
    public function __construct()
    {
    }

    public function addsignee(Request $request)
    {
        echo "Fsdsd";
    }

    /**
     * change user status
     *
     * @return \Illuminate\Http\Response
     */
    function statusCron()
    {

        $result = User::select('id', 'email', 'parent_id', 'first_name', 'status', 'last_login_date')->where('status', '!=', 'Active')->where('role', 'SIGNEE')->get()->toArray();
        foreach ($result as $key => $value) {
            SigneeOrganization::where(['user_id' => $value['id'], 'organization_id' => $value['parent_id']])->update([
                'profile_status' =>  $value['status']
            ]);
        }


        // \Log::info(" Run Status Inactive cronjob ");
        // $userObj = User::select('id', 'email', 'first_name', 'last_login_date')->where('status', 'Active')->where('role', 'SIGNEE')->get()->toArray();
        // foreach ($userObj as $key => $value) {
        //     if ($value['last_login_date'] != '' && $value['last_login_date'] != null) {
        //         $date1 = new DateTime(date('Y-m-d H:i:s'));
        //         $date2 = new DateTime($value['last_login_date']);
        //         $interval = $date1->diff($date2);
        //         if ($interval->y > 0) {
        //             $userUpdateObj = User::find($value['id']);
        //             $userUpdateObj->status = 'Dormant';
        //             $userUpdateObj->save();
        //         } else if ($interval->m > 5) {
        //             $userUpdateObj = User::find($value['id']);
        //             $userUpdateObj->status = 'Inactive';
        //             $userUpdateObj->save();
        //         }
        //     }
        // }
    }

    function getBooking()
    {
        echo  date('Y-m-d H:i:s');
        if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == 'localhost') {
            date_default_timezone_set('Asia/Kolkata');
        }

        $objBooking = new Booking();
        \Log::info("Conjob run for send notification for shift with current time " . date('Y-m-d H:i:s'));
        $bokObj = Booking::select('*')->where('bookings.date', '=', date('Y-m-d'))->where('status', 'CONFIRMED')->get()->toArray();
        // $bokObj = Booking::select('*')->where('bookings.date', '>=', date('Y-m-d'))->where('status', 'CONFIRMED')->get()->toArray();
        $userAdmin = User::select('id', 'email', 'first_name', 'last_login_date')->where('role', 'SUPERADMIN')->first();

        // foreach ($bokObj as $key => $val) {
        //     $today =  date('Y-m-d H:i:s');
        //     $shiftDate = $val['date'] . ' ' . $val['start_time'];

        //     $x = new DateTime($today);
        //     $y = new DateTime($shiftDate);
        //     $interval = $y->diff($x);

        //     echo $val['id'] . " => " . $val['reference_id'] . " => Hours $interval->h <br/>";
        //     //dd($interval);
        //     if ($interval->h == 4 || $interval->h == 6 || $interval->h == 7)
        //     // if(($interval->h >= 4) || $interval->h >= 6 || $interval->h >= 7)
        //     {
        //         $confirmedCandidate = $objBooking->getConfirmedCandidates($val['id']);
        //         foreach ($confirmedCandidate as $key => $candidate) {
        //             $msg = 'Hello ' . $candidate['first_name'] . ' Your shift ' . $candidate['hospital_name'] . ' hospital (' . $candidate['ward_name'] . ' ward) start after ' . $interval->h . ' hour(s)';
        //             echo "<br/>$msg<br/>";
        //             $notification = new Notification();
        //             $notification->signee_id = $candidate['signeeId'];
        //             //$notification->organization_id = Auth::user()->id;
        //             $notification->organization_id = $candidate['organization_id'];
        //             $notification->booking_id = $candidate['booking_id'];
        //             $notification->message = $msg;
        //             $notification->status = 'shift_start_noti';
        //             $notification->is_read = 0;
        //             $notification->is_sent = 0;
        //             $notification->created_by = $userAdmin->id;
        //             $notification->updated_by = $userAdmin->id;
        //             $notification->is_showing_for = "SIGNEE";
        //             //dd($notification);
        //             //print_r($notification);
        //             $notification->save();
        //             $objNotification = new Notification();
        //             if ($candidate['device_id'] != '' && $candidate['platform'] == 'Android') {
        //                 $objNotification->sendAndroidNotification($msg, $candidate['device_id'], $candidate['booking_id'], 'shift_start_noti', $candidate['organization_id']);
        //             } else if ($candidate['device_id'] != '' && $candidate['platform'] == 'Iphone') {
        //                 $objNotification->sendIOSNotification($msg, $candidate['device_id'], $candidate['booking_id'], 'shift_start_noti', $candidate['organization_id']);
        //             }
        //             // $objNotification = new Notification();
        //             // $sendNotification = $objNotification->addNotificationV2($candidate, 'shift_start_noti', '',$interval);
        //         }
        //         // return response()->json(['status' => true, 'message' => 'Your shift '.$candidate['hospital_name'].' hospital ('.$candidate['ward_name'].' ward) starts after '.$interval->h.' hour(s)'], $this->successStatus);
        //     } else {
        //         // echo "errors";
        //     }
        // }
    }
}
