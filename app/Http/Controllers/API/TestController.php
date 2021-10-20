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

class TestController extends Controller
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        echo "hiiii index";
    }


    function test($trustId)
    {
        // echo "tESTTS";
        try {
            $trustObj = new Trust();
            $result = $trustObj->test($trustId);
            // dd($result);
            // exit;
            return response()->json(['status' => true, 'message' => 'Test data.', 'data' => $result], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e], 200);
        }
    }

    function inactive()
    {
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

    // public function pdf(Request $request)
    // {
    //     $requestData = $request->all();
        
    //     $objBooking = new Booking();
    //     $booking = $objBooking->getBooking($requestData['booking_id'])->toArray();

    //     $userObj = new User();
    //     $user = $userObj->getSigneeById($requestData['signee_id'])->toArray();
        
    //     $bookingSigneeData = array_merge($booking, $user);
    //     // print_r($data);exit();

    //     $result = [
    //         'title' => 'Signee Details',
    //         'date' => date('m/d/Y'),
    //         'data' => $bookingSigneeData
    //     ];

    //     //print_r($result);exit();
    //     // $pdf = PDF::loadView('signee', $data);
    //     // return $pdf->download('itsolutionstuff.pdf');

    //     $pdf = App::make('dompdf.wrapper');
    //     // load from other pages use object or array by comma like (pdf-view,$user) 
    //     $pdf->loadView('signee', $result);
    //     // return $pdf->stream();
    //     $filePath = public_path().'/uploads/signee_pdf/';
    //     $time = date('Ymdhms');
    //     $file = $filePath ."$time-offerLetter.pdf";
    //     file_put_contents($file, $pdf->output());
        
    //     return response()->json(['status' => true, 'message' => $file], 200);
    // }

    // public function pdf()
    // {
    //     $data = [
    //         'title' => 'Welcome to ItSolutionStuff.com',
    //         'date' => date('m/d/Y')
    //     ];
    //     print_r($data);exit();
    //     $pdf = PDF::loadView('signee', $data);
    //     return $pdf->download('itsolutionstuff.pdf');
    // }
}
