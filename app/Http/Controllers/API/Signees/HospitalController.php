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
        //print_r(Auth::user()->id);exit();
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
            return response()->json(['message' => 'Hospital not available.', 'status' => false], 200);
        }

        // -----------------------------------------------------------------------------------
        //print_r($hospitals);exit();

        // $hospital = Hospital::select(
        //     //'bookings.hospital_id',
        //     'hospitals.id',
        //     'hospitals.hospital_name',
        //     'hospitals.trust_id'
        // );
        // $hospital->leftJoin('trusts',  'trusts.id', '=', 'hospitals.trust_id');
        // $hospital->leftJoin('users',  'users.id', '=', 'trusts.user_id');
        // $hospital->whereNull('hospitals.deleted_at');
        // $hospital->groupBy('hospitals.trust_id');
        // $res = $hospital->get()->toArray();
        // if ($res) {
        //     return response()->json(['status' => true, 'message' => 'Hospitals get successfully', 'data' => $res], $this->successStatus);
        // } else {
        //     return response()->json(['message' => 'Hospitals not available.', 'status' => false], 200);
        // }

        //old
        // $bookingMatches = BookingMatch::select(
        //     //'bookings.hospital_id',
        //     'hospitals.id',
        //     'hospitals.hospital_name'
        // );
        // $bookingMatches->leftJoin('bookings',  'bookings.id', '=', 'booking_matches.booking_id');
        // $bookingMatches->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        // $bookingMatches->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        // $bookingMatches->whereNull('hospitals.deleted_at');
        // $bookingMatches->groupBy('bookings.hospital_id');
        // $res = $bookingMatches->get()->toArray();
        // if ($res) {
        //     return response()->json(['status' => true, 'message' => 'Hospitals get successfully', 'data' => $res], $this->successStatus);
        // } else {
        //     return response()->json(['message' => 'Hospitals not available.', 'status' => false], 200);
        // }

        // $trustList = Trust::where('user_id', $this->userId)->get()->toArray();
        // $trustIdArray = array_column($trustList, 'id');
        // $hospital = Hospital::select(
        //     'hospital_name'
        // );
        // $hospital->whereIn('trust_id', $trustIdArray);
        // $hospital->whereNull('hospitals.deleted_at');
        // $res = $hospital->get()->toArray();


        //print_r(count($res));exit();
        // $hospitalList = Hospital::select(
        //     'hospital_name',
        // )->distinct()->get()->toArray();
        // //$hospitalList->where();
        
    }

    public function showAllSpeciality()
    {
        // echo Auth::user()->id;
        $staff = User::select('id')->where('parent_id', Auth::user()->parent_id)->get()->toArray();
        $staffIdArray = array_column($staff, 'id');
        $staffIdArray[] = Auth::user()->parent_id;
        //print_r($staffIdArray);exit();
        $query2 = Speciality::whereIn('user_id', $staffIdArray)->get()->toArray();
        //print_r($query2);exit();

        $selectedSpec = SigneeSpecialitie::select('speciality_id')->where('user_id', Auth::user()->id)->get()->toArray();
        $query2 [] = $selectedSpec;
        //print_r($query2);exit;

        if ($query2) {
            return response()->json(['status' => true, 'message' => 'Speciality get successfully', 'data' => $query2], $this->successStatus);
        } else {
            return response()->json(['message' => 'Speciality not available.', 'status' => false], 200);
        }
    }
}
