<?php

namespace App\Http\Controllers\API\Signees;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models;
use App\Models\Booking;
use App\Models\BookingMatch;
use App\Models\Hospital;
use App\Models\SigneeOrganization;
use App\Models\Speciality;
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
       // $hospital = BookingMatch::where('signee_id', $this->userId)->get();
       // print_r($hospital);exit();

        $bookingMatches = BookingMatch::select(
            'bookings.hospital_id',
            'hospitals.hospital_name'
        );
        $bookingMatches->leftJoin('bookings',  'bookings.id', '=', 'booking_matches.booking_id');
        $bookingMatches->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $bookingMatches->whereNull('hospitals.deleted_at');
        $bookingMatches->groupBy('bookings.hospital_id');
        $res = $bookingMatches->get()->toArray();
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Hospitals get successfully', 'data' => $res], $this->successStatus);
        } else {
            return response()->json(['message' => 'Hospitals not available.', 'status' => false], 200);
        }

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
        $org = SigneeOrganization::where('user_id', $this->userId)->get()->toArray();
        $orgIdArray = array_column($org, 'organization_id');
        //print_r($orgIdArray);exit();
        $specialityList = Speciality::whereIn('user_id', $orgIdArray)->get();
        if ($specialityList) {
            return response()->json(['status' => true, 'message' => 'Speciality get successfully', 'data' => $specialityList], $this->successStatus);
        } else {
            return response()->json(['message' => 'Speciality not available.', 'status' => false], 200);
        }
    }
}
