<?php

namespace App\Http\Controllers\API\Signees;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models;
use App\Models\Hospital;
use App\Models\Speciality;

class HospitalController extends Controller
{
    public $successStatus = 200;
    public function showAllHospital()
    {
        $hospitalList = Hospital::select(
            'hospital_name',
        )->distinct()->get()->toArray();
        if ($hospitalList) {
            return response()->json(['status' => true, 'message' => 'Hospital get successfully', 'data' => $hospitalList], $this->successStatus);
        } else {
            return response()->json(['message' => 'Hospital not available.', 'status' => false], 200);
        }
    }

    public function showAllSpeciality()
    {
        $specialityList = Speciality::select(
            'speciality_name',
        )->distinct()->get()->toArray();
        if ($specialityList) {
            return response()->json(['status' => true, 'message' => 'Speciality get successfully', 'data' => $specialityList], $this->successStatus);
        } else {
            return response()->json(['message' => 'Speciality not available.', 'status' => false], 200);
        }
    }
}
