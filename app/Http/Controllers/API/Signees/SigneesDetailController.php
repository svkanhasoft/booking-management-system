<?php

namespace App\Http\Controllers\API\Signees;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\SigneesDetail;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Validator;
use Illuminate\Support\Facades\Auth;

class SigneesDetailController extends Controller
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
    public function create(Request $request)
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
        $requestData['password'] = Hash::make($request->post('password'));
        // $requestData['parent_id'] = $this->userId;
        $requestData['role'] = 'SIGNEE';
        $userCreated = User::create($requestData);
        if ($userCreated) {
            $requestData['user_id'] = $userCreated['id'];
            $orgResult = SigneesDetail::create($requestData);
            if ($orgResult) {
                $UserObj = new User();
                // $userCreated = $UserObj->getOrganizationDetails($userCreated['id']);
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

        $checkRecord = User::where('email', $request->all('email'))->where('role', 'SIGNEE')->first();
        if (empty($checkRecord)) {
            return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 200);
        }
        if ($checkRecord->status != 'Active') {
            return response()->json(['message' => 'Sorry, Your account is Inactive, contact to organization admin', 'status' => false], 200);
        }
        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => 'SIGNEE'])) {
            $userResult = Auth::user();
            $user = User::find($this->userId)->SigneesDetail;
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
        $user =  User::find($this->userId)->SigneesDetail;
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
        $user = User::where('role', 'SIGNEE')->where('id', $this->userId)->first();
        if (!empty($user)) {
            $userObj = User::find($this->userId);
            $userObj['password'] = Hash::make($request->post('password'));
            $userObj->save();
            return response()->json(['status' => true, 'message' => 'Password Successfully change.'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Password change failed.', 'status' => false], 200);
        }
    }
}
