<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Models\Hospital;
use Hash;
use App\Models\Trust;
use App\Models\Ward;
use App\Models\Traning;
use Config;
use DateTime;
use Session;

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

    
}

