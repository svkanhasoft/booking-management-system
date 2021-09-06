<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests;
use App\Models\User;
use App\Models\OrganizationUserDetail;
use App\Models\SigneesDetail;
use App\Models\SigneeOrganization;

use Hash;
use App\Models\Role;
use App\Models\SigneeSpecialitie;
use App\Models\Speciality;

class UserController extends Controller
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
            "role_id" => 'required',
            "designation_id" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        $requestData['password'] = Hash::make(123456);
        $requestData['parent_id'] = $this->userId;
        $requestData['role'] = 'STAFF';
        $userCreated = User::create($requestData);
        if ($userCreated) {
            $requestData['user_id'] = $userCreated['id'];
            $orgResult = OrganizationUserDetail::create($requestData);
            if ($orgResult) {
                $UserObj = new User();
                $userCreated = $UserObj->getOrganizationDetails($userCreated['id']);
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
        $validator = Validator::make($request->all(), [
            "email" => 'required',
            "password" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $checkRecord = User::where('email', $request->all('email'))->where('role', 'STAFF')->first();
        if (empty($checkRecord)) {
            return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 200);
        }
        if($checkRecord->status !='Active'){
            return response()->json(['message' => 'Sorry, Your account is Inactive, contact to organization admin', 'status' => false], 200);
        }
        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => 'STAFF'])) {
            $userResult = Auth::user();
            $UserObj = new User();
            $user = $UserObj->getOrganizationDetails($userResult['id']);
            $user[0]['token'] =  $userResult->createToken('User')->accessToken;
            return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Email or password are not match', 'status' => false], 200);
        }
    }
    /**
     *  creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function signinV2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required',
            "password" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $checkRecord = User::where('email', $request->all('email'))->where('role', 'STAFF')->first();
        if (empty($checkRecord)) {
            return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 200);
        }
        if($checkRecord->status !='Active'){
            return response()->json(['message' => 'Sorry, Your account is Inactive, contact to organization admin', 'status' => false], 200);
        }
        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => 'STAFF'])) {
            $userResult = Auth::user();
            $UserObj = new User();
            $user = $UserObj->getOrganizationDetails($userResult['id']);
            $user[0]['token'] =  $userResult->createToken('User')->accessToken;
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
        $UserObj = new User();
        $user = $UserObj->getOrganizationDetails($this->userId);
        if (!empty($user)) {
            return response()->json(['status' => true, 'message' => 'User details get successfully.', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'something will be wrong', 'status' => false], 200);
        }
    }
    /** 
     * Get User list
     * 
     * @return \Illuminate\Http\Response 
     */
    public function getuserlist(Request $request)
    {
        $UserObj = new User();
        $user = $UserObj->fetchStaflist($request, $this->userId);
        if (!empty($user)) {
            return response()->json(['status' => true, 'message' => 'User details get successfully.', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'something will be wrong', 'status' => false], 200);
        }
    }
    
    /** 
     * Get User list
     * 
     * @return \Illuminate\Http\Response 
     */
    public function getuserById($userId)
    {
        $UserObj = new User();
        $user = $UserObj->getStafById($userId);
        if (!empty($user)) {
            return response()->json(['status' => true, 'message' => 'User details get successfully.', 'data' => $user], $this->successStatus);
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
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $user = User::where('role', 'STAFF')->where('id', $this->userId)->first();
        //print_r($this->userId);exit();
        if (!empty($user)) {
            $userObj = User::find($this->userId);
            $userObj['password'] = Hash::make($request->post('password'));
            $userObj->save();
            return response()->json(['status' => true, 'message' => 'Password Successfully change.'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Password change failed.', 'status' => false], 200);
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

        $userObj = User::find($decodeId);
        $userObj['password'] = Hash::make($input['password']);
        $userObj['password_change'] = 1;
        $res = $userObj->save();
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Your password Successfully changed'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Invalid user id.', 'status' => false], 200);
        }
    }

    /** 
     * update user/staff 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function update(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            "id" => 'required',
            'email' => 'unique:users,email,'.$requestData['id'].'NULL,id',
             "first_name" => 'required',
             "last_name" => 'required',
             "contact_number" => 'required',
             "role_id" => 'required',
             "designation_id" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        
        $user = User::findOrFail($requestData['id']);
        $addResult = $user->update($requestData);
        if ($addResult) {
            $oudData = OrganizationUserDetail::where('user_id', $requestData['id'])->first();
            $oud = OrganizationUserDetail::findOrFail($oudData['id']);
            $oudResult = $oud->update($requestData);
            $UserObj = new User();
            $userData = $UserObj->getStafById($user['id']);
            if($oudResult)
            {
                return response()->json(['status' => true, 'message' => 'User update Successfully', 'data' => $userData], $this->successStatus);
            } 
            else 
            {
                return response()->json(['message' => 'Sorry, user update failed!', 'status' => false], 200);
            }
        }
    }

    public function destroy($userId)
    {
        OrganizationUserDetail::where('user_id', $userId)->delete();
        $userDelete = User::where('id', $userId)->delete();
        if($userDelete)
        {
            return response()->json(['status' => true, 'message' => 'User deleted successfully.'], $this->successStatus);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Sorry, User not deleted.'], $this->successStatus);
        }
    }

    public function addSignee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|unique:users',
            "first_name" => 'required',
            "last_name" => 'required',
            "password" => 'required',
            "mobile_number" => 'required',
            "date_of_birth" => 'required',
            // "candidate_id" => "required",
            "candidate_id" => 'unique:signees_detail,candidate_id',
            "address_line_1" => 'required',
            "address_line_2" => 'required',
            "city" => 'required',
            "zipcode" => 'required',
            "candidate_referred_from" => 'required',
            "nationality" => 'required',
            "date_registered" => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();

        // if ($request->hasFile('cv')) {
        //     $files1 = $request->file('cv');
        //     $name = time() . '_signee_' . $files1->getClientOriginalName();
        //     $files1->move(public_path() . '/uploads/signee_docs/', $name);
        //     $requestData['cv'] = $name;
        // }

        $requestData['password'] = Hash::make($request->post('password'));
        $requestData['parent_id'] = $requestData['organization_id'];
        $requestData['role'] = 'SIGNEE';
        $userCreated = User::create($requestData);
        if ($userCreated) {
            $requestData['user_id'] = $userCreated['id'];
            $orgResult = SigneesDetail::create($requestData);

            $objSpeciality = new SigneeSpecialitie();
            $objSpeciality->addSpeciality($requestData['speciality'], $userCreated['id'], false);

            //$requestData['organization_id'] = $request->post('organization_id');
            $requestData['user_id'] = $userCreated['id'];
            $sing = SigneeOrganization::create($requestData);
            if ($orgResult) {
                $UserObj = new User();
                $mailRes =  $UserObj->sendRegisterEmail($request);
                return response()->json(['status' => true, 'message' => 'Signee added Successfully', 'data' => $userCreated], $this->successStatus);
            }
        } else {
            return response()->json(['message' => 'Sorry, Signee added failed!', 'status' => false], 200);
        }
    }

    public function viewSignee(Request $request)
    {
        $UserObj = new User();
        $user = $UserObj->getSignee($this->userId);
        //print_r($user);exit();
        if(!empty($user))
        {
            return response()->json(['status' => true, 'message' => 'Signee Get Successfully', 'data' => $user], $this->successStatus);
        }
        else
        {
            return response()->json(['message' => 'Sorry, Something is Wrong!', 'status' => false], 200);
        }
    }

    public function editSignee(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            "id"=>'required',
            "first_name" => 'required',
            "last_name" => 'required',
            "password" => 'nullable|min:6',
            "mobile_number" => 'required',
            "date_of_birth" => 'required',
            // "candidate_id" => 'required',
            //"candidate_id" => 'unique:signees_detail,candidate_id,'.$requestData['id'].',NULL,id',
            "address_line_1" => 'required',
            "address_line_2" => 'required',
            "city" => 'required',
            "zipcode" => 'required',
            "candidate_referred_from" => 'required',
            "nationality" => 'required',
            "date_registered" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        
       // print_r($requestData);exit();
        if (!empty($request->post('password'))) {
            $requestData['password'] = Hash::make($request->post('password'));
        }
        $signee = User::findOrFail($requestData['id']);;
        $signeeUpdated = $signee->update($requestData);
        if ($signeeUpdated) {
            $signeeDetailResult = SigneesDetail::where('user_id', '=', $requestData['id'])->firstOrFail();
            $result = $signeeDetailResult->update($requestData);
            if ($result) {
                $speciality = new Speciality();
                $speciality->addOrUpdateSpeciality($requestData['speciality'], $requestData['id']);
                 $user = User::find($this->userId)->SigneesDetail;
                return response()->json(['status' => true, 'message' => 'Signee update Successfully', 'data' =>  $signee], $this->successStatus);
            }
        } else {
            return response()->json(['message' => 'Sorry, Signee update failed!', 'status' => false], 200);
        }
    }

    public function deleteSignee($id)
    {
        $userDelete = User::where('id', $id)->delete();
        if($userDelete)
        {
            return response()->json(['status' => true, 'message' => 'Signee deleted successfully.'], $this->successStatus);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Sorry, Signee not deleted.'], $this->successStatus);
        }
    }

    public function getCandidate(Request $request)
    {
        $time = [];
        $time['candidate_id'] = date("ymdHis");
        if ($time) {
            return response()->json(['status' => true, 'message' => 'Candidate get successfully', 'data' => $time], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Candidate not available!', 'status' => false], 200);
        }
    }
}