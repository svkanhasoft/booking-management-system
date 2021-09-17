<?php

namespace App\Http\Controllers\API\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Hospital;
use Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Trust;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Ward;
use App\Models\Traning;
use Validator;
use Config;
use DB;

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
            // "code" => 'required',
            "code" => 'unique:trusts,code',
            "preference_invoice_method" => 'required',
            "email_address" => 'required|email',
            "address_line_1" => 'required',
            "city" => 'required',
            "post_code" => 'required',
            "trust_portal_url" => 'required|url',
            "portal_email" => 'required|email',
            "portal_password" => 'required',
            "first_name" => 'required',
            "last_name" => 'required',
            "contact_email_address" => 'required|email',
            "phone_number" => 'required|numeric',
            'hospital' => 'required:hospital,[
                ward => required:ward,[]
            ]',
            'training' => 'required:training,[]',
            // 'ward' => 'required:ward,[]',

            'hospital.*.hospital_name' => 'required',
            'hospital.*.ward' => 'required',
            'hospital.*.ward.*.ward_name' => 'required',
            'hospital.*.ward.*.ward_type_id' => 'required',
            'hospital.*.ward.*.ward_number' => 'required|numeric',
            'training.*.training_name' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $requestData = $request->all();
            // print_r($requestData['hospital'][1]);exit();
            $requestData['password'] = Hash::make($request->post('portal_password'));
            $requestData['user_id'] = $this->userId;
            $trustResult = Trust::create($requestData);

            $objHospital = new Hospital();
            $hospitalResult = $objHospital->addHospital($requestData['hospital'], $trustResult['id'], false);
            $hospitals = Hospital::where('trust_id', $trustResult['id'])->get();
            //  print_r($hospital);exit();


            $i = 0;
            $objWard = new Ward();
            foreach ($requestData['hospital'][$i] as $ward) {
                foreach ($hospitals as $hospital) {
                    $wardResult = $objWard->addWard($requestData['hospital'][$i]['ward'], $trustResult['id'], $hospital->id, false);
                    $i++;
                }
                break;
            }
            // $objWard = new Ward();
            //$wardResult = $objWard->addWard($requestData['hospital'][0]['ward'], $trustResult['id'], false);
            $objTraning = new Traning();
            $specialityResult = $objTraning->addTraning($requestData['training'], $trustResult['id'], false);

            if ($specialityResult) {
                $data = new Trust();
                $trustDetails = $data->getTrustById($trustResult['id']);
                return response()->json(['status' => true, 'message' => 'Trust added successfully.', 'data' => $trustResult], $this->successStatus);
            } else {
                return response()->json(['message' => 'Trust added failed.', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    function update(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            "id" => 'required',
            "name" => 'required',
            //"code" => 'required',
            "code" => 'unique:trusts,code,' . $requestData['id'] . 'NULL,id,user_id,' . $this->userId,
            "preference_invoice_method" => 'required',
            "email_address" => 'required|email',
            "address_line_1" => 'required',
            "city" => 'required',
            "post_code" => 'required',
            "trust_portal_url" => 'required|url',
            "portal_email" => 'required|email',
            "portal_password" => 'required',
            "first_name" => 'required',
            "last_name" => 'required',
            "contact_email_address" => 'required|email',
            "phone_number" => 'required|numeric',
            'hospital' => 'required:hospital,[
                ward => required:ward,[]
            ]',
            'training' => 'required:training,[]',
            //'ward' => 'required:ward,[]',
          
            'hospital.*.hospital_name' => 'required',
            'hospital.*.ward' => 'required',
            'hospital.*.ward.*.ward_name' => 'required',
            'hospital.*.ward.*.ward_type_id' => 'required',
            'hospital.*.ward.*.ward_number' => '|numeric',
            'training.*.training_name' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $requestData = $request->all();
            //print_r($requestData);exit();
            $requestData['password'] = Hash::make($request->post('portal_password'));
            $trustResult = Trust::findOrFail($requestData['id']);
            $trustResult->update($requestData);

            $objHospital = new Hospital();
            $objHospital->addUpdateHospital($requestData);

            $objTraning = new Traning();
            $objTraning->updateTraning($requestData);

            // $objTraning = new Traning();
            // $specialityResult = $objTraning->addTraning($requestData['training'], $requestData['id'], true);

            if ($trustResult) {
                $trustData = new Trust();
                $trustDetails = $trustData->getTrustById($requestData['id']);
                return response()->json(['status' => true, 'message' => 'Trust update successfully.', 'data' => $trustDetails], $this->successStatus);
            } else {
                return response()->json(['message' => 'Trust update failed.', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    function getTrustDetail($trustId = null, Request $request)
    {
        //print_r(Auth::user()->parent_id);exit();
        $perPage = Config::get('constants.pagination.perPage');
        if ($trustId > 0) {

            $trustObj = new Trust();
            $result = $trustObj->getTrustById($trustId);
            // print_r($trust);exit(); 

            // $result = [];
            // $result = Trust::find($trustId);
            //print_r($result);exit();
            // $result->hospital;
            // $result->ward;
            // $result->training;

            return response()->json(['status' => true, 'message' => 'Trust detail get successfully.', 'data' => $result], $this->successStatus);
        } else {
            $keyword = $request->get('search');
            // $query = Trust::where('user_id', $this->userId);
            if(Auth::user()->role == 'ORGANIZATION'){
                $staff = User::select('id')->where('parent_id', $this->userId)->get()->toArray();
                $staffIdArray = array_column($staff, 'id');
                $staffIdArray[] = Auth::user()->id;
                $query = Trust::whereIn('user_id', $staffIdArray);
            }else{
                $query = Trust::whereIn('user_id',array(Auth::user()->id,Auth::user()->parent_id));
            }
            if (!empty($keyword)) {
                $query->Where('name',  'LIKE', "%$keyword%");
            }
            $result =  $query->latest()->paginate($perPage);
            // $result = $query->get();
            if (count($result) > 0) {
                return response()->json(['status' => true, 'message' => 'Trust list get successfully.', 'data' => $result], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'Sorry, Trust not available.'], $this->successStatus);
            }
        }
    }

    function destroy($trustId)
    {
        // echo "$trustId" ;
        // exit;
        Ward::where('trust_id', $trustId)->delete();
        Traning::where('trust_id', $trustId)->delete();
        Hospital::where('trust_id', $trustId)->delete();
        $result = Trust::where('id', $trustId)->delete();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Trust Delete successfully.'], $this->successStatus);
        } else {
            return response()->json(['status' => false, 'message' => 'Sorry, Trust not deleted.'], $this->successStatus);
        }
    }

    // public function generateTrustCode()
    // {
    //     try {
    //         $time = [];
    //         $time['trust_code'] = date("ymdHis");
    //         if ($time) {
    //             return response()->json(['status' => true, 'message' => 'Candidate get successfully', 'data' => $time], $this->successStatus);
    //         } else {
    //             return response()->json(['message' => 'Sorry, Candidate not available!', 'status' => false], 200);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
    //     }
    // }
}
