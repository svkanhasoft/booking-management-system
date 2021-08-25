<?php

namespace App\Http\Controllers\API\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Hospital;
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
            'hospital' => 'required:hospital,[
                ward => required:ward,[]
            ]',
            'traning' => 'required:traning,[]',
            // 'ward' => 'required:ward,[]',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $requestData = $request->all();
       // print_r($requestData['hospital'][1]);exit();
        $requestData['password'] = Hash::make($request->post('portal_password'));
        $requestData['user_id'] = $this->userId;
        $trustResult = Trust::create($requestData);
        
        $objHospital = new Hospital();
        $hospitalResult = $objHospital->addHospital($requestData['hospital'], $trustResult['id'], false);
        $hospitals = Hospital::where('trust_id', $trustResult['id'])->get();
      //  print_r($hospital);exit();


        $i=0;
        $objWard = new Ward();
        foreach($requestData['hospital'][$i] as $ward)
        {
            foreach($hospitals as $hospital)
            {
                $wardResult = $objWard->addWard($requestData['hospital'][$i]['ward'], $trustResult['id'], $hospital->id ,false);
                $i++;
            }
           break;
        }
        // $objWard = new Ward();
        //$wardResult = $objWard->addWard($requestData['hospital'][0]['ward'], $trustResult['id'], false);

        $objTraning = new Traning();
        $specialityResult = $objTraning->addTraning($requestData['traning'], $trustResult['id'], false);

        if ($specialityResult) {
            $data = new Trust();
            $trustDetails = $data->getTrustById($trustResult['id']);
            return response()->json(['status' => true, 'message' => 'Trust added successfully.', 'data' => $trustResult], $this->successStatus);
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
            'hospital' => 'required:hospital,[
                ward => required:ward,[]
            ]',
            'traning' => 'required:traning,[]',
            //'ward' => 'required:ward,[]',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
       // print_r($requestData);exit();
        $requestData['password'] = Hash::make($request->post('portal_password'));
        $trustResult = Trust::findOrFail($requestData['id']);
        $trustResult->update($requestData);

        $objHospital = new Hospital();
        $objHospital->addUpdateHospital($requestData);

        if ($trustResult) {
            $trustData = new Trust();
            $trustDetails = $trustData->getTrustById($requestData['id']);
            return response()->json(['status' => true, 'message' => 'Trust update successfully.', 'data' => $trustDetails], $this->successStatus);
        } else {
            return response()->json(['message' => 'Trust update failed.', 'status' => false], 200);
        }
    }

    function getTrustDetail($trustId = null, Request $request)
    {
        if ($trustId > 0) {

            $trustObj = new Trust();
            $trust = $trustObj->getTrustById($trustId);
            print_r($trust);exit(); 
            
            // $result = [];
            // $result = Trust::find($trustId);
            //print_r($result);exit();
            // $result->hospital;
            // $result->ward;
            // $result->training;


            return response()->json(['status' => true, 'message' => 'Trust detail get successfully.', 'data' => $result], $this->successStatus);
        } else {
            $keyword = $request->get('search');
            $query = Trust::where('user_id', $this->userId);
            if (!empty($keyword)) {
                $query->Where('name',  'LIKE', "%$keyword%");
                $query->orWhere('code',  'LIKE', "%$keyword%");
                $query->orWhere('preference_invoive_method',  'LIKE', "%$keyword%");
                $query->orWhere('email_address',  'LIKE', "%$keyword%");
                $query->orWhere('address_line_1',  'LIKE', "%$keyword%");
                $query->orWhere('address_line_2',  'LIKE', "%$keyword%");
                $query->orWhere('address_line_3',  'LIKE', "%$keyword%");
                $query->orWhere('city',  'LIKE', "%$keyword%");
                $query->orWhere('post_code',  'LIKE', "%$keyword%");
                $query->orWhere('trust_portal_url',  'LIKE', "%$keyword%");
                $query->orWhere('portal_email',  'LIKE', "%$keyword%");
                $query->orWhere('first_name',  'LIKE', "%$keyword%");
                $query->orWhere('last_name',  'LIKE', "%$keyword%");
                $query->orWhere('contact_email_address',  'LIKE', "%$keyword%");
                $query->orWhere('phone_number',  'LIKE', "%$keyword%");
                $query->orWhere('client',  'LIKE', "%$keyword%");
                $query->orWhere('department',  'LIKE', "%$keyword%");
            }
            $result = $query->get();
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
}
