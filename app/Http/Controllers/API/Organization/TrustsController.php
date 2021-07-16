<?php

namespace App\Http\Controllers\API\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Trust;
use Illuminate\Http\Request;
use App\Models\Ward;
use App\Models\Traning;
use Validator;

class TrustsController extends Controller
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

    function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => 'required',
            "code" => 'required',
            "preference_invoive_method" => 'required',
            "email_address" => 'required',
            "address_line_1" => 'required',
            "city" => 'required',
            "post_code" => 'required',
            "trust_portal_url" => 'required',
            "portal_email" => 'required',
            "portal_password" => 'required',
            "first_name" => 'required',
            "last_name" => 'required',
            "contact_email_address" => 'required',
            "phone_number" => 'required',
            // 'traning.0.traning_name' => 'required|max:255',
            'traning' => 'required:traning,[]',
            'ward' => 'required:ward,[]',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        // $result = $this->getTrustDetail(1);
        $requestData = $request->all();
        $requestData['password'] = Hash::make($request->post('portal_password'));
        $requestData['user_id'] = $this->userId;
        $trustResult = Trust::create($requestData);
        $objWard = new Ward();
        $wardResult = $objWard->addWard($requestData['ward'], $trustResult['id'], false);
        $objTraning = new Traning();
        $specialityResult = $objTraning->addTraning($requestData['traning'], $trustResult['id'], false);
        if ($specialityResult) {
            return response()->json(['status' => true, 'message' => 'Trust added successfully.', 'data' => $specialityResult], $this->successStatus);
        } else {
            return response()->json(['message' => 'Trust added failed.', 'status' => false], 200);
        }
    }

    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => 'required',
            "code" => 'required',
            "preference_invoive_method" => 'required',
            "email_address" => 'required',
            "address_line_1" => 'required',
            "city" => 'required',
            "post_code" => 'required',
            "trust_portal_url" => 'required',
            "portal_email" => 'required',
            "portal_password" => 'required',
            "first_name" => 'required',
            "last_name" => 'required',
            "contact_email_address" => 'required',
            "phone_number" => 'required',
            'traning' => 'required:traning,[]',
            'ward' => 'required:ward,[]',
            // "ward.*" => 'required|min:1',
            // "traning.*" => 'required|min:1',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        $requestData['password'] = Hash::make($request->post('portal_password'));
        $trustResult = Trust::findOrFail($requestData['id']);
        $trustResult->update($requestData);

        $objWard = new Ward();
        $wardResult = $objWard->addWard($requestData['ward'], $requestData['id'], true);
        $objTraning = new Traning();
        $specialityResult = $objTraning->addTraning($requestData['traning'], $requestData['id'], true);
        if ($specialityResult) {
            // $result = $this->getTrustDetail($requestData['id']);
            return response()->json(['status' => true, 'message' => 'Trust update successfully.', 'data' => $specialityResult], $this->successStatus);
        } else {
            return response()->json(['message' => 'Trust update failed.', 'status' => false], 200);
        }
    }

    function getTrustDetail($trustId = null)
    {
        if ($trustId > 0) {
            $result = [];
            $result = Trust::find($trustId);
            $result->ward;
            $result->training;
            return response()->json(['status' => true, 'message' => 'Trust detail get successfully.', 'data' => $result], $this->successStatus);
        } else {
            $result = Trust::where('user_id', $this->userId)->get();
            if ($result) {
                return response()->json(['status' => true, 'message' => 'Trust list get successfully.', 'data' => $result], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'Sorry, Trust not available.'], $this->successStatus);
            }
        }
    }

    // function getAllTrust()
    // {
    //     $result = Trust::where('user_id', $this->userId)->get();
    //     if ($result) {
    //         return response()->json(['status' => true, 'message' => 'Trust list get successfully.', 'data' => $result], $this->successStatus);
    //     } else {
    //         return response()->json(['status' => false, 'message' => 'Sorry, Trust not available.'], $this->successStatus);
    //     }
    // }

    function destroy($trustId)
    {
        // echo "$trustId" ;
        // exit;
        Ward::where('trust_id', $trustId)->delete();
        Traning::where('trust_id', $trustId)->delete();
        $result = Trust::where('id', $trustId)->delete();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Trust Delete successfully.'], $this->successStatus);
        } else {
            return response()->json(['status' => false, 'message' => 'Sorry, Trust not deleted.'], $this->successStatus);
        }
    }
}
