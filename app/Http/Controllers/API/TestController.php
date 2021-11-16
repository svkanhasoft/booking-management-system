<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Models\Hospital;
use Hash;
use App\Models\User;
use App\Models\SigneeOrganization;
use App\Models\Trust;
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

    public function getData()
    {
        $res = Booking::find(102);
        // $res->users;
        // $res->ward;
        // $res->trust;
        $res->trust->hospitals;
        // $res->trust->hospital->ward;
        // $res->hospital;
        // $res->shift;
        // $res->shiftType;
        echo "<pre/>";
        dd($res);
    }

    public function getOrg()
    {
        // $res = User::where(['user_id' => 138]);
        $res = User::find(138);
        $res->organizations;
        // foreach($res->organizations as $key => $val){
        //     print_r($key . " == ");
        //     print_r($val->id);
        //     // dd($val);
        //     // exit;
        //     //  dd($res->specialitys($val->id)); 
        //     // $res->organizations[$key]['organization'] = $res->specialitys($val->organization_id); 
        //     echo "$val->organization_id >>>";
        //     print_r($res->specialitys($val->organization_id));
        // }
        // $res->organizations->specialitys;
        // $res->specialitys;
        // $res->user;
        echo "<pre/>";
        dd($res);
        return response()->json(['message' => 'Sorry, Availablity update failed!', 'data' => $res, 'status' => false], 409);
    }

    public function notification()
    {
        echo "fsdfsd";
        $token =  "dQE4u0N01ACA4o-RoJ71ac:APA91bF4BOzk21A-5DS-wpypdGjlwxn1D76-RlxxpZEoeNMdhfmeFHEd3ZoryraNhBGa3V3DehGR9TUVxFCiXgM9iqsmN4lGneR77uGCr6S9Ajk4doLocSwyRq_Uh8EJ0CqkLuOBUL1j";
        $objNotification = new Notification();
        $response   = $objNotification->sendAndroidNotification("Hello notification",$token,7);
        dd($response);
    }
}
