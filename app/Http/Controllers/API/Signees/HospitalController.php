<?php

namespace App\Http\Controllers\API\Signees;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models;
use App\Models\Booking;
use App\Models\BookingMatch;
use App\Models\Hospital;
use App\Models\SigneeOrganization;
use App\Models\SigneeSpecialitie;
use App\Models\Speciality;
use App\Models\User;
use App\Models\Trust;
use Illuminate\Support\Facades\Auth;

class HospitalController extends Controller
{
    public $successStatus = 200;

    protected $userId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!empty(Auth::user())) {
                $this->userId = Auth::user()->id;
            }
            return $next($request);
        });
    }

    public function showAllHospital()
    {
        $staff = User::select('id')->where(['parent_id' => Auth::user()->parent_id, 'role'=>'STAFF'])->get()->toArray();
        $staffIdArray = array_column($staff, 'id');
        $staffIdArray[] = Auth::user()->parent_id;
       // print_r($staffIdArray);exit();
        $trusts = Trust::whereIn('user_id', $staffIdArray)->get()->toArray();
        $trustIdArray = array_column($trusts, 'id');
        //print_r($trustIdArray);exit();
        $hospitals = Hospital::whereIn('trust_id', $trustIdArray)->get()->toArray();
        if ($hospitals) {
            return response()->json(['status' => true, 'message' => 'Hospital get successfully', 'data' => $hospitals], $this->successStatus);
        } else {
            return response()->json(['message' => 'Hospital not available.', 'status' => false], 404);
        }
        
    }

    public function showAllSpeciality()
    {
        $staff = User::select('id')->where('parent_id', Auth::user()->parent_id)->get()->toArray();
        $staffIdArray = array_column($staff, 'id');
        $staffIdArray[] = Auth::user()->parent_id;
        //print_r($staffIdArray);exit();
        $query2 = Speciality::whereIn('user_id', $staffIdArray)->get()->toArray();
        if ($query2) {
            return response()->json(['status' => true, 'message' => 'Speciality get successfully', 'data' => $query2], $this->successStatus);
        } else {
            return response()->json(['message' => 'Speciality not available.', 'status' => false], 404);
        }
    }
}
