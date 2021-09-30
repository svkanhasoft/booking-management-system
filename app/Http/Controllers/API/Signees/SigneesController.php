<?php

namespace App\Http\Controllers\API\Signees;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Booking;
use App\Models\SigneesDetail;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SigneeOrganization;
use App\Models\SigneePreferences;
use App\Models\SigneeSpecialitie;
use App\Models\BookingMatch;
use App\Models\CandidateReferredFrom;
use App\Models\Speciality;
use App\Models\SigneeDocument;
use Hash;
use Validator;
use Config;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Log;

class SigneesController extends Controller
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

    /**
     *  creating a new resource.
     *
     * @return \Illuminate\View\View
     */

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|unique:users',
            "first_name" => 'required',
            "last_name" => 'required',
            "contact_number" => 'required',
            "address_line_1" => 'required',
            "city" => 'required',
            "postcode" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $requestData = $request->all();
        //print_r($requestData);exit();
        if ($request->hasFile('cv')) {
            $files1 = $request->file('cv');
            $name = time() . '_signee_' . $files1->getClientOriginalName();
            $files1->move(public_path() . '/uploads/signee_docs/', $name);
            $requestData['cv'] = $name;
        }
        if ($request->hasFile('profile_pic')) {
            $files1 = $request->file('profile_pic');
            $name2 = time() . '_signee_' . $files1->getClientOriginalName();
            $files1->move(public_path() . '/uploads/signee_docs/', $name2);
            $requestData['profile_pic'] = $name2;
        }

        $requestData['password'] = Hash::make($request->post('password'));
        $requestData['parent_id'] = $request->post('organization_id');
        $requestData['role'] = 'SIGNEE';
        $userCreated = User::create($requestData);
        if ($userCreated) {
            $requestData['user_id'] = $userCreated['id'];
            $orgResult = SigneesDetail::create($requestData);

            $objSpeciality = new SigneeSpecialitie();
            $objSpeciality->updateSpeciality($requestData['speciality'], $userCreated['id'], false);

            $requestData['organization_id'] = $request->post('organization_id');
            $requestData['user_id'] = $userCreated['id'];
            $sing = SigneeOrganization::create($requestData);
            if ($orgResult) {
                $UserObj = new User();
                $mailRes =  $UserObj->sendRegisterEmail($request);
                return response()->json(['status' => true, 'message' => 'User added Successfully', 'data' => $userCreated], $this->successStatus);
            }
        } else {
            return response()->json(['message' => 'Sorry, User added failed!', 'status' => false], 200);
        }
    }

    /**
     *  creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function signin(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "email" => 'required',
            "password" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 400);
        }

        $checkRecord = User::where('email', $request->all('email'))->where('role', 'SIGNEE')->first();
       // dd($checkRecord->id);
        if (empty($checkRecord)) {
            return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 400);
        }
        if ($checkRecord->status != 'Active') {
            return response()->json(['message' => 'Sorry, Your account is Inactive, contact to organization admin', 'status' => false], 200);
        }
        // dd($checkRecord->id, $request->organization_id);
        $orgResult = SigneeOrganization::where(['user_id' => $checkRecord->id, 'organization_id' => $request->organization_id])->first();
        //print_r($orgResult);exit();
        if(empty($orgResult)){
            return response()->json(['message' => 'Your account does not exist with a selected organization!', 'status' => false], 400);
        }
        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => 'SIGNEE'])) {
            $checkRecord->parent_id =  request('organization_id');
            $checkRecord->save();
            $userResult = Auth::user();
            $this->userId = Auth::user()->id;
            $this->organizationId = Auth::user()->organization_id;
            $userObj = new User();
            $user = $userObj->getSigneeDetails(Auth::user()->id);
            $user['token'] =  $userResult->createToken('User')->accessToken;
            return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Email or password are not match', 'status' => false], 200);
        }
    }

    /** 
     * Get User details 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function getDetails()
    {
        $userObj = new User();
        $user = $userObj->getSigneeDetails($this->userId);
        if (!empty($user)) {
            return response()->json(['status' => true, 'message' => 'Signee details get successfully.', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'something will be wrong', 'status' => false], 200);
        }
    }

    /** 
     * Change Password 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        if (!(Hash::check($request->old_password, Auth::user()->password))) {
            return response()->json(['status' => false, 'message' => "Your old password can't be match"], 200);
        }
        $user = User::where('role', 'SIGNEE')->where('id', $this->userId)->first();
        //print_r($user);exit();
        if (!empty($user)) {
            $userObj = User::find($this->userId);
            $userObj['password'] = Hash::make($request->post('password'));
            $userObj['password_change'] = 1;
            $userObj->save();
            return response()->json(['status' => true, 'message' => 'Password Successfully change.'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Password change failed.', 'status' => false], 200);
        }
    }



    /** 
     * Change profile 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => 'required',
            "last_name" => 'required',
            "contact_number" => 'required',
            "address_line_1" => 'required',
            "city" => 'required',
            "postcode" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        if (!empty($request->post('password'))) {
            $requestData['password'] = Hash::make($request->post('password'));
        }
        $role = User::findOrFail($this->userId);
        $userCreated = $role->update($requestData);
        if ($userCreated) {
            $orgResult = SigneesDetail::where('user_id', '=', $this->userId)->firstOrFail();
            $result = $orgResult->update($requestData);
            if ($result) {
                $user = User::find($this->userId)->SigneesDetail;
                return response()->json(['status' => true, 'message' => 'User update Successfully', 'data' =>  $user], $this->successStatus);
            }
        } else {
            return response()->json(['message' => 'Sorry, User update failed!', 'status' => false], 200);
        }
    }

    /** 
     * Forgot api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function forgot(Request $request)
    {
        $userObj = new User();
        $mailRes =  $userObj->sendForgotEmail($request);
        if ($mailRes) {
            return response()->json(['message' => 'Please check your email and change your password', 'status' => true], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Invalid Email address.', 'status' => false], 200);
        }
    }
    /** 
     * reset Password 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'decode_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $user = Auth::user();
        $input = $request->all();
        $decodeId = base64_decode($input['decode_id']);
        // base64_encode

        $userObj = User::find($decodeId);
        $userObj['password'] = Hash::make($input['password']);
        $res = $userObj->save();
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Your password Successfully changed'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Invalid user id.', 'status' => false], 200);
        }
    }
    /** 
     * delete Password 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $user = Auth::user();
        $input = $request->all();
        $decodeId = base64_decode($input['decode_id']);
        // base64_encode

        $userObj = User::find($decodeId);
        $userObj['password'] = Hash::make($input['password']);
        $res = $userObj->save();
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Your password Successfully changed'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Invalid user id.', 'status' => false], 200);
        }
    }

    /** 
     * Candidate Referred From
     * 
     * @return \Illuminate\Http\Response 
     */
    public function getCandidateReferredFrom(Request $request)
    {
        $candidateReferredFromObj = CandidateReferredFrom::all();

        if ($candidateReferredFromObj) {
            return response()->json(['status' => true, 'message' => 'Candidate Referred From get successfully', 'data' => $candidateReferredFromObj], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Candidate Referred From.', 'status' => false], 200);
        }
    }

    public function getOrganisation()
    {
        $query = User::select(
            "users.*",
            'org.organization_name'
        );
        $query->join('organizations as org', 'org.user_id', '=', 'users.id');
        $query->where('users.role', '=', 'ORGANIZATION');
        $count =  $query->orderBy('org.organization_name','asc')->get();
        if ($count) {
            return response()->json(['status' => true, 'message' => 'Organizations listed successfully', 'data' => $count], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, organizations not available.', 'status' => false], 200);
        }


        // $query = User::select(
        //     "users.*",
        //     'org.organization_name',

        // );
        // $query->join('organizations as org', 'org.user_id', '=', 'users.id');

        // $query->where('users.role', '=', 'ORGANIZATION');
        // $query->orderBy('org.organization_name','asc');
        // $orgList = $query->get()->toArray();
        //print_r(gettype($orgList));exit();

        // $orgId = array_column($orgList, 'id');

        // $query2 = Speciality::select(
        //     'id',
        //     'speciality_name',
        // );
        // $query2->whereIn('user_id', $orgId);
        // $orgSpec = $query2->get()->toArray();

        // $result = [];
        
        // $result = $orgList; 

        // $result['speciality'] = $orgSpec;
        //print_r($result);exit();

        // if ($result) {
        //     return response()->json(['status' => true, 'message' => 'Organizations listed successfully', 'data' => $result], $this->successStatus);
        // } else {
        //     return response()->json(['message' => 'Sorry, organizations not available.', 'status' => false], 200);
        // }
    }

    public function getOrgSpecialities($id)
    {
        $speciality = Speciality::select('id', 'speciality_name')->where('user_id', $id)->get()->toArray();
        if ($speciality) {
            return response()->json(['status' => true, 'message' => 'Specialities get successfully', 'data' => $speciality], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, No specialities available.', 'status' => false], 200);
        }
    }

    public function logout(Request $request)
    { 
        $user = Auth::user()->token();
        try {
            if (Auth::user()) {
                $user->revoke();
                return response()->json([
                    'status' => true,
                    'message' => 'You have Successfully logout',
                ], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'Sorry, logout failed'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' =>  $e->getMessage()], 200);
        }
    }

    public function getCandidateId(Request $request)
    {
        try {
            $time = [];
            $time['candidate_id'] = date("ymdHis");
            if ($time) {
                return response()->json(['status' => true, 'message' => 'Candidate get successfully', 'data' => $time], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Candidate not available!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function shiftList()
    {
        $booking = new BookingMatch();
        $result = $booking->getShiftList();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Booking listed successfully', 'data' => $result], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking not available.', 'status' => false], 200);
        }
    }
    public function viewShiftDetails($id)
    {
        $booking = new BookingMatch();
        $result = $booking->viewShiftDetails($id);
        $start_time = strtotime($result['start_time']);
        $end_time = strtotime($result['end_time']);
        $diff = gmdate('H:i:s', $end_time - $start_time);
        $result['duration'] = $diff;   
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Booking get successfully', 'data' => $result], $this->successStatus);
        } else {
            return response()->json(['message' => 'Something is wrong.', 'status' => false], 200);
        }
    }

    public function filterBookings(Request $request)
    {
        $booking = new BookingMatch();
        $result = $booking->getFilterBookings($request,$this->userId);
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Booking get successfully', 'data' => $result], $this->successStatus);
        } else {
            return response()->json(['message' => 'Something is wrong.', 'status' => false], 200);
        }
    }

    public function changeSigneeStatus(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $bookingMatch = BookingMatch::firstOrNew(['signee_id' => $requestData['id']]);
            $bookingMatch->signee_status = $requestData['status'];
            $res =  $bookingMatch->save();
            if ($res) {
                return response()->json(['status' => true, 'message' => 'Status changed successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, status not change.', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function addOrg(Request $request)
    {
        $requestData = $request->all();
        //print_r($requestData);exit;
        $requestData['user_id'] = $this->userId;
        //$orgId = $requestData['organization']['organization_id'];
        $signeeOrg = new SigneeOrganization();
        $signeeOrg->addOrganisation($requestData['organization'], $this->userId, false);

        $objSpeciality = new SigneeSpecialitie();
        $objSpeciality->addSpeciality($requestData['organization'], $this->userId, true);
        return response()->json(['status' => true, 'message' => 'Organisation added Successfully'], $this->successStatus);
    }

    public function documentUpload(Request $request)
    {
        $user = User::where('id', $this->userId)->first();
        //echo $this->userId;exit;
        //print_r($request->file());exit;
        $requestData = $request->all();
        //print_r($requestData['key']);exit();
        $validator = Validator::make($request->all(), [
            // 'passport[]' => 'mimes:jpeg,jpg,png,gif,csv,txt,pdf|max:2048',
            'files[]' => 'mimes:jpg,png,jpeg,pdf,docs|max:2048',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try
        { 
            if($request->hasfile('files'))
            {
                if($request->file('files'))
                {
                    $files = $request->file('files');
                    foreach($files as $key=>$file)
                    {
                        $name = $file->getClientOriginalName();
                        $filename = pathinfo($name, PATHINFO_FILENAME);
                        $extension = pathinfo($name, PATHINFO_EXTENSION);
                        
                        $new_filename = $filename.'_'.time().'.'.$extension;

                        $new_name = preg_replace('/[^A-Za-z0-9\-._]/', '', $new_filename);
                        $file->move(public_path().'/uploads/signee_docs/', $new_name);
                        $image = new SigneeDocument();
                        $image->signee_id = $this->userId;
                        $image->key = $requestData['key'];
                        $image->file_name = $new_name;
                        $image->organization_id = $user->parent_id;
                        $docUpload = $image->save(); 
                    }
                    if($docUpload)
                    {
                        $document = $image->getDocument($image->signee_id, $image->key, $user->parent_id);
                        return response()->json(['status' => true, 'message' => 'Document Uploaded Successfully', 'data' => $document], $this->successStatus);
                    }
                }
            }
            else{
                return response()->json(['message' => 'No file selected', 'status' => false], 200);
            }
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function updateSpeciality(Request $request, $userId)
    {
        //echo $userId;
        try{
            $requestData = $request->all();
            $objSpeciality = new SigneeSpecialitie();
            $objSpeciality->updateSpeciality($requestData['speciality_id'], $userId, true);
            if ($objSpeciality) {
                return response()->json(['status' => true, 'message' => 'Speciality updated successfully'], $this->successStatus);
            }
            else {
                return response()->json(['message' => 'Sorry, Speciality updation failed!', 'status' => false], 200);
            }
        }
        catch(\Exception $e){
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
        
    }

    public function getSigneeSpeciality()
    {
        // echo Auth::user()->id;exit;
        // $query = SigneeSpecialitie::select('speciality_id')->where('user_id', Auth::user()->id)->get()->toArray();
        $query = SigneeSpecialitie::select(
            // DB::raw('GROUP_CONCAT( specialities.id SEPARATOR ",") AS speciality_id'),
            'specialities.id as speciality_id'
        );
        $query->leftJoin('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');
        
        $query->where('signee_speciality.user_id', Auth::user()->id);
        $query->whereNull('signee_speciality.deleted_at');
        $res = $query->get()->toArray();
        
        //$array = explode(',', $res);
        
        // $array = [];
        // foreach ($array as $key => $value) {
        //     $array[] = $value;
        // }
        
       // $speciality_id['speciality_id'] = $res;
        
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Speciality get successfully', 'data'=> $res], $this->successStatus);
        }
        else {
            return response()->json(['message' => 'Sorry, Speciality getting error!', 'status' => false], 200);
        }
    }


    public function getEmailOrganisation(Request $request)
    {
        $requestData = $request->all();
        $email = $requestData['email'];
        $userData = User::where('email', $email)->first();
       // print_r($userData);exit();
        $query = SigneeOrganization::select(
            //"users.*",
            'organizations.organization_name'
        );
        $query->leftJoin('users' , 'users.id', '=', 'organizations.user_id');
        $query->leftJoin('organizations' , 'organizations.user_id', '=', 'users.id');
        $query->where('users.email', $email);
        $res = $query->toSql();
        return $res;
        // $organization = SigneeOrganization::where('user_id', $userData['id'])->get();
        // if($organization)
        // {
        //     return response()->json(['status' => true, 'message' => 'Booking get successfully', 'data' => $organization], $this->successStatus);
        // }
    }

    public function getSigneeDocument(Request $request)
    {
        $perPage = Config::get('constants.pagination.perPage');
        $key = $request->get('key');
        // $signeeDocument = SigneeDocument::where('key', $key)->get()->toArray();
        $signeeDocument = SigneeDocument::select(
            "id",
            "signee_id",
            "key",
            "file_name",
            "organization_id",
            DB::raw('date(created_at) as date_added'),
        );
        $signeeDocument->where(['signee_id'=> $this->userId, 'organization_id'=>Auth::user()->parent_id]);
        if (!empty($key)) {
            // echo $keyword;exit;
            $signeeDocument->Where(['key'=> $key, 'signee_id'=>$this->userId, 'organization_id'=>Auth::user()->parent_id]);
        }

        $data = $signeeDocument->latest()->paginate($perPage);
        $count =  $data->count();
        if ($count) {
            return response()->json(['status' => true, 'message' => 'Documents get successfully', 'data'=>$data], $this->successStatus);
        }
        else {
            return response()->json(['message' => 'Sorry, Documents not found!', 'status' => false], 200);
        }
    }

    public function deleteDocument($id)
    {
        // echo $id;
        $document = SigneeDocument::find($id);
        unlink(public_path()."/uploads/signee_docs/" . $document->file_name);
        $document = SigneeDocument::where("id", $document->id)->delete();
        if($document)
        {
            return response()->json(['status' => true, 'message' => 'Image deleted successfully'], $this->successStatus);           
        }
    }
}
