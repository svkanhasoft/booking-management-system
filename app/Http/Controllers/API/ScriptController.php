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
use DB;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\SigneeDocument;
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

        // $result = User::select('id', 'email', 'parent_id', 'first_name', 'status', 'last_login_date')->where('status', '!=', 'Active')->where('role', 'SIGNEE')->get()->toArray();
        // foreach ($result as $key => $value) {
        //     SigneeOrganization::where(['user_id' => $value['id'], 'organization_id' => $value['parent_id']])->update([
        //         'profile_status' =>  $value['status']
        //     ]);
        // }


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

    function documentCron()
    {
        echo  date('Y-m-d H:i:s');
        if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == 'localhost') {
            date_default_timezone_set('Asia/Kolkata');
        }
        \Log::info("Conjob run for documentCron with current time " . date('Y-m-d H:i:s'));
        $documentArray = array(
            'passport' => 'Copy of Passport in Colour including front cover. (Right to work)',
            'immunisation_records' => 'Immunisation records - Proof of immunity for (Varicella, Tuberculosis, Rubella, Measles, Hep B Level 100). Blood results needs to be traceable to exact Clinic/ source. For EPP clearance ( HIV 1 & 2) Hep C and Hep B surface antigen ( IVS)',
            'training_certificates' => 'Mandatory training certificates- Fire safety, BLS,MH, Infection control, safeguarding child/Adult etc',
            'nursing_certificates' => 'Nursing Certificates/ Diploma/NVQ',
            'professional_indemnity_insurance' => 'Proof of Current Professional Indemnity Insurance',
            'nmc_statement' => 'NMC statement of entry',
            'dbs_disclosure_certificate' => 'DBS disclosure certificate- Front and back',
            'cv' => 'CV- Work history from school leaving age with no gaps. Please ensure that all dates are in (DD/MM/YY) format',
            'employment' => 'TWO references covering the last 3 years of employment (must include hospital/company stamp or company/hospital logo letter head)',
            'address_proof' => 'TWO proofs of address dated within last 3 months (bank statement, utility bill, official government letter etc.',
            'passport_photo' => 'X1 passport Photo for ID badge',
            'proof_of_ni' => "Proof of NI- Any letter from HMRC showing NI number or Copy of NI card ( front & back Copy ) -We don’t accept payslips",
        );
        // $bokObj = SigneeDocument::select('*')->where('expire_date', '!=', null)->get()->toArray();
        $from = date("Y-m-d");
        $to = date("Y-m-d", strtotime("+2 month"));
        // $to = date("Y-m-d", strtotime("+12 month"));
        $subQuery = SigneeDocument::select(
            'signee_documents.id',
            'signee_documents.signee_id as signeeId',
            'signee_documents.key',
            'signee_documents.file_name',
            'signee_documents.expire_date',
            'signee_documents.organization_id',
            'users.platform',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.device_id',
            'users.parent_id',
            'users.status',
            'users.subscription_name',
            'users.subscription_purchase_date',
            'users.subscription_expire_date',
            // DB::raw('GROUP_CONCAT(DISTINCT  signee_documents.key SEPARATOR ",") AS fileType')
        );
        $subQuery->leftJoin('users',  'users.id', '=', 'signee_documents.signee_id');
        $subQuery->where('signee_documents.expire_date', '!=', null);
        $subQuery->whereBetween('signee_documents.expire_date', [$from, $to]);
        // $subQuery->groupBy('signee_documents.signee_id');
        $bokObj = $subQuery->get()->toArray();
        // print_r($bokObj);
        // exit;
        $userAdmin = User::select('id', 'email', 'first_name', 'last_login_date')->where('role', 'SUPERADMIN')->first();
        foreach ($bokObj as $key => $val) {
            echo $documentArray[$val['key']];
            $val['fileType'] = $documentArray[$val['key']];
            $today =  date('Y-m-d H:i:s');
            $expireDate = $val['expire_date'];

            $x = new DateTime($today);
            $y = new DateTime($expireDate);
            $interval = $y->diff($x);


            echo $val['id'] . " => " . $expireDate .  " => Hours $interval->h <br/>";
            // dd($interval);
            // if ($interval ){
            if ($interval->days == 10 || $interval->days == 60 || $interval->days == 30) {
                $msg = 'Hello ' . $val['first_name'] . ' Your ' . $val['key'] . ' document expired on ' . date('jS, F Y', strtotime($val['expire_date']));
                echo "<br/>$msg<br/>";
                $notification = new Notification();
                $notification->signee_id = $val['signeeId'];
                $notification->organization_id = (isset($val['organization_id']) && $val['organization_id'] > 0 ? $val['organization_id'] : $val['parent_id']);
                $notification->booking_id = null;
                $notification->message = $msg;
                $notification->status = 'docs_expire_notification';
                $notification->is_read = 0;
                $notification->is_sent = 0;
                $notification->created_by = $userAdmin->id;
                $notification->updated_by = $userAdmin->id;
                $notification->is_showing_for = "SIGNEE";
                // dd($notification);
                //print_r($notification);
                $notification->save();
                $objNotification = new Notification();
                if ($val['device_id'] != '' && $val['platform'] == 'Android') {
                    $objNotification->sendAndroidNotification($msg, $val['device_id'], "", 'docs_expire_notification', $val['organization_id']);
                } else if ($val['device_id'] != '' && $val['platform'] == 'Iphone') {
                    $objNotification->sendIOSNotification($msg, $val['device_id'], "", 'docs_expire_notification', $val['organization_id']);
                }
                try {
                    $details = [
                        'title' => '',
                        'body' => 'Hello ',
                        'mailTitle' => 'document_expire',
                        'subject' => 'Booking Management System: Document Expire Notification',
                        'data' => $val,
                    ];
                    $emailRes = \Mail::to($val['email'])->send(new \App\Mail\SendSmtpMail($details));
                    // echo "dd";
                    // exit;
                } catch (\Exception $e) {
                    continue;
                }
            } else {
                // echo "errors";
            }
        }
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

        $this->documentCron();
    }
}
