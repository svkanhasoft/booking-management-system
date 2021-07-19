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
        // echo $this->userId;exit;
        // dd($request->all());
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
        // print_r($request->all());
        // exit;
        $objAvailability = new Availability();
        $orgResult = $objAvailability->addAvailability($request->all(), $this->userId);
        if ($orgResult) {
            $UserObj = new User();
            return response()->json(['status' => true, 'message' => 'Availablity added Successfully','data' =>$request->all()], $this->successStatus);
            // return response()->json(['status' => true, 'message' => 'Availablity added Successfully','data' => $orgResult], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Availablity added failed!', 'status' => false], 200);
        }
    }
}
