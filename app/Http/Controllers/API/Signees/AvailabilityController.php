<?php

namespace App\Http\Controllers\API\Signees;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\SigneesDetail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Availability;
use Hash;
use Validator;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
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

    public function availability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'sunday' => 'required:sunday,[]',
            'tuesday' => 'required:tuesday,[]',
            'wednesday' => 'required:wednesday,[]',
            'thursday' => 'required:thursday,[]',
            'friday' => 'required:friday,[]',
            'saturday' => 'required:saturday,[]',
            'monday' => 'required:monday,[]',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $objAvailability = new Availability();
        $orgResult = $objAvailability->addAvailability($request->all(), $this->userId);
        if ($orgResult) {
            $UserObj = new User();
            return response()->json(['status' => true, 'message' => 'Availablity update Successfully', 'data' => $request->all()], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Availablity update failed!', 'status' => false], 200);
        }
    }
    public function getAvailability()
    {
        $objAvailability = new Availability();
        $availability = $objAvailability->getAvailability($this->userId);
        if ($availability) {
            return response()->json(['status' => true, 'message' => 'Availablity get successfully', 'data' => $availability], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, availablity not available!', 'status' => false], 200);
        }
    }
}
