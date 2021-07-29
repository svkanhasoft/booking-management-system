<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests;
use App\Models\User;
use App\Models\OrganizationUserDetail;
use Hash;
use App\Models\Role;

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
            // "password" => 'required',
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
    public function getuserlist()
    {
        // echo $this->userId;exit;
        $UserObj = new User();
        $user = $UserObj->fetchStaflist($this->userId);
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
            'conform_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $user = User::where('role', 'STAFF')->where('id', $this->userId)->first();
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
            'conform_password' => 'required|same:password',
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
}
