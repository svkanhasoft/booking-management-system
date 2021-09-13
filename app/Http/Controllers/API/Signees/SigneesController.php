<?php

namespace App\Http\Controllers\API\Signees;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\SigneesDetail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SigneeOrganization;
use App\Models\SigneePreferences;
use App\Models\SigneeSpecialitie;
use App\Models\CandidateReferredFrom;
use Hash;
use Validator;
use Illuminate\Support\Facades\Auth;

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
            "password" => 'required',
            "mobile_number" => 'required',
            "date_of_birth" => 'required',
            "candidate_id" => 'required',
            "address_line_1" => 'required',
            "address_line_2" => 'required',
            "address_line_3" => 'required',
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

        $requestData = $request->all();

        if ($request->hasFile('cv')) {
            $files1 = $request->file('cv');
            $name = time() . '_signee_' . $files1->getClientOriginalName();
            $files1->move(public_path() . '/uploads/signee_docs/', $name);
            $requestData['cv'] = $name;
        }

        $requestData['password'] = Hash::make($request->post('password'));
        $requestData['parent_id'] = $request->post('organization_id');
        $requestData['role'] = 'SIGNEE';
        $userCreated = User::create($requestData);
        if ($userCreated) {
            $requestData['user_id'] = $userCreated['id'];
            $orgResult = SigneesDetail::create($requestData);

            $objSpeciality = new SigneeSpecialitie();
            $objSpeciality->addSpeciality($requestData['speciality'], $userCreated['id'], false);

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
        $validator = Validator::make($request->all(), [
            "email" => 'required',
            "password" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 400);
        }


        $checkRecord = User::where('email', $request->all('email'))->where('role', 'SIGNEE')->first();
  
        if (empty($checkRecord)) {
            return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 400);
        }
        if ($checkRecord->status != 'Active') {
            return response()->json(['message' => 'Sorry, Your account is Inactive, contact to organization admin', 'status' => false], 200);
        }
        $orgResult = SigneeOrganization::where(['user_id' => $checkRecord->id, 'organization_id' => $request->all('organization_id')])->first();
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
            'email' => 'unique:users,email,' . $this->userId,
            "first_name" => 'required',
            "last_name" => 'required',
            "password" => 'nullable|min:6',
            "mobile_number" => 'required',
            "date_of_birth" => 'required',
            "candidate_id" => 'required',
            "address_line_1" => 'required',
            "address_line_2" => 'required',
            "address_line_3" => 'required',
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
        //echo "hi";exit();
        $query = User::select(
            "users.*",
            'org.organization_name'
        );
        $query->join('organizations as org', 'org.user_id', '=', 'users.id');
        $query->where('users.role', '=', 'ORGANIZATION');
        $count =  $query->latest('users.created_at')->get();
        if ($count) {
            return response()->json(['status' => true, 'message' => 'Organizations listed successfully', 'data' => $count], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, organizations not available.', 'status' => false], 200);
        }
    }
}
