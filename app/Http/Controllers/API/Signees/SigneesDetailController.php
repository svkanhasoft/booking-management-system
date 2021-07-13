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
            $this->userId = Auth::user()->id;
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



    /** 
     * Change profile 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function profileUpdate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // "email" => 'required|unique:users',
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
        // echo "Hiii"; exit;
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
        $user = User::where('role', "SIGNEE")->where('email', $request->all('email'))->first();
        if (isset($user) && !empty($user)) {
            // // $user = User::where(['email' => 'testshailesh1@gmail.com'])->first();
            // $user['link'] = "<a  href=". route('reset-password',array('id' => base64_encode($user['id']))).">Click Here </a>";
            // $details = [
            //     'title' => '',
            //     'body' => 'Hello ',
            //     'mailTitle' => 'forgot',
            //     'subject' => 'Booking management system: TEST EMAIL',
            //     'data' =>  $user,
            // ];
            // $sss = \Mail::to('testshailesh1@gmail.com')->cc('shaileshv.kanhasoft@gmail.com')->send(new \App\Mail\MyTestMail($details));           
            // // $sss = \Mail::to($user['email'])->cc('shaileshv.kanhasoft@gmail.com')->send(new \App\Mail\MyTestMail($details));           
            // dd($sss);
            // exit;
            //  return response()->json(['data' => $user, 'message' => 'OTP sent to your email', 'status' => true], $this->successStatus);
            return response()->json(['data' => $user, 'message' => 'Please check your email and chnge your password', 'status' => true], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Invalid phone number', 'status' => false], 200);
        }
    }

}
