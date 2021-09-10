<?php

namespace App\Http\Controllers\API\Signees;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\SigneePreferences;
use Illuminate\Support\Facades\Auth;

class SigneePreferencesController extends Controller
{
    public $successStatus = 200;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!empty(Auth::user())) {
                $this->userId = Auth::user()->id;
            }
            return $next($request);
        });
    }

    public function addPreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //"day_of_week" => 'required',
            //"no_of_shift" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        //print_r($requestData);exit();
        $signeePreference = new SigneePreferences();
        $res = $signeePreference->addOrUpdatePreference($requestData, $this->userId);
        //print_r($res);exit();
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Preferences added Successfully', 'data' => $res], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Preferences added Failed!', 'status' => false], 200);
        }
    }
}
