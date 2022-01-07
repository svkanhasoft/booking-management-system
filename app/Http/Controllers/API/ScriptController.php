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
use App\Models\Notification;

class ScriptController extends Controller
{
    public $successStatus = 200;
    public function __construct()
    {
    }

    public function addsignee(Request $request){
        echo "Fsdsd";

    }

    /**
     * change user status
     *
     * @return \Illuminate\Http\Response
     */
    function statusCron()
    {
        \Log::info(" Run Status Inactive cronjob ");
        $userObj = User::select('id', 'email', 'first_name', 'last_login_date')->where('status', 'Active')->where('role', 'SIGNEE')->get()->toArray();
        foreach ($userObj as $key => $value) {
            if ($value['last_login_date'] != '' && $value['last_login_date'] != null) {
                $date1 = new DateTime(date('Y-m-d H:i:s'));
                $date2 = new DateTime($value['last_login_date']);
                $interval = $date1->diff($date2);
                if ($interval->y > 0) {
                    $userUpdateObj = User::find($value['id']);
                    $userUpdateObj->status = 'Dormant';
                    $userUpdateObj->save();
                } else if ($interval->m > 5) {
                    $userUpdateObj = User::find($value['id']);
                    $userUpdateObj->status = 'Inactive';
                    $userUpdateObj->save();
                }
            }
        }
    }

    function getBooking()
    {
        date_default_timezone_set('Asia/Kolkata');

        $objBooking = new Booking();
        \Log::info(" Sent shift start notification cron ");
        $bokObj = Booking::select('*')->where('bookings.date', '=', date('Y-m-d'))->where('status', 'CONFIRMED')->get()->toArray();
        // $bokObj = Booking::select('*')->where('bookings.date', '>=', date('Y-m-d'))->where('status', 'CONFIRMED')->get()->toArray();

        foreach ($bokObj as $key => $val) {
            //print_r($val);exit;
            $date1 =  date('Y-m-d H:i:s');
            $date2 = $val['date'].' '. $val['start_time'];
            $x = new DateTime($date1);
            $y = new DateTime($date2);
            $interval = $y->diff($x);
            echo $val['id'] . " => " .$val['reference_id']. " => Hours $interval->h <br/>" ;
            //dd($interval);
            if($interval->h == 4 || $interval->h == 6 || $interval->h == 7)
            // if(($interval->h >= 4) || $interval->h >= 6 || $interval->h >= 7)
            {
                $confirmedCandidate = $objBooking->getConfirmedCandidates($val['id']);
                foreach($confirmedCandidate as $key => $candidate)
                {
                    $objNotification = new Notification();
                    $sendNotification = $objNotification->addNotificationV2($candidate, 'shift_start_noti', '',$interval);
                }
                return response()->json(['status' => true, 'message' => 'Your shift '.$candidate['hospital_name'].' hospital ('.$candidate['ward_name'].' ward) starts after '.$interval->h.' hour(s)'], $this->successStatus);
            } else{
                echo "errors";
            }
        }
    }

}
