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

class ScriptController extends Controller
{
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

}
