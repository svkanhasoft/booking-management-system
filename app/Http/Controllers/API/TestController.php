<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Models\Hospital;
use Hash;
use App\Models\User;
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
    
    public function getData(){
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
    
}
