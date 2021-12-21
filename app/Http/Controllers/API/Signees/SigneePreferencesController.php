<?php

namespace App\Http\Controllers\API\Signees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\SigneePreferences;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
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

    /**
     * Add candidate preferences.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        try {
            $requestData = $request->all();
            $signeePreference = new SigneePreferences();
            $res = $signeePreference->addOrUpdatePreference($requestData, $this->userId);
            if ($res) {
                $bookingArray = new Booking();
                $bookingArray->addsigneeMatch($this->userId);
                return response()->json(['status' => true, 'message' => 'Preferences updated Successfully', 'data' => $requestData], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Preferences changed Failed!', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * Get candidate preferences.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPreferences(Request $request)
    {
        try {
            $preference = SigneePreferences::where(['user_id' => $this->userId])->first();
            if ($preference) {
                return response()->json(['status' => true, 'message' => 'Preferences get Successfully', 'data' => $preference], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Preferences not found!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }
}
