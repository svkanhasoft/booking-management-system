<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\Organization;
use Hash;

class OrganizationController extends Controller
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function __construct()
    {
    }

    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $checkRecord = User::where('email', $request->all('email'))->where('role', 'ORGANIZATION')->count();
        if ($checkRecord == 0) {
            return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 200);
        }
        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => 'ORGANIZATION'])) {
            $user = Auth::user();
            $user['token'] =  $user->createToken('MyApp')->accessToken;
            return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Email or password are not match', 'status' => false], 200);
        }
    }
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required',
            'password' => 'required|min:6',
            'email' => 'required|email|unique:users',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['first_name'] = $input['organization_name'];
        $input['last_name'] = $input['organization_name'];
        $input['status'] = 'Active';
        $input['role'] = 'ORGANIZATION';
        // dd($input);
        $user = User::create($input);
        $userRes = User::find($user['id']);
        if (!empty($userRes)) {
            $requestData = $request->all();
            $requestData['user_id'] = $user['id'];
            Organization::create($requestData);
            $userRes['token'] =  $user->createToken('MyApp')->accessToken;
            return response()->json(['status' => true, 'message' => 'Register Successfully completed.', 'data' => $userRes], $this->successStatus);
        } else {
            return response()->json(['status' => false, 'message' => "something will be wrong"], 200);
        }
    }
    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function details(Request $request)
    {
        $user = Auth::user();
        return response()->json([
            'status' => true, 'message' => 'organization Details Get Successfully',
            'data' => $user
        ], $this->successStatus);
    }


    /** 
     * Logout api 
     * 
     * @return \Illuminate\Http\Response 
     */
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
            return response()->json(['status' => false, 'message' => $e], 200);
        }
    }

    /** 
     * Forgot api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function forgot(Request $request)
    {
        $user = User::where('role', "ORGANIZATION")
            ->where('email', $request->all('email'))->get()->toArray();
        // print_r($user);
        // exit;
        if (isset($user) && !empty($user)) {
            // $user = User::where(['email' => 'testshailesh1@gmail.com'])->first();
            // $user['link'] = "<a  href=". route('reset-password',array('lang'=>'en','id' => base64_encode($user['id']))).">Click Here </a>";
            // $details = [
            //     'title' => '',
            //     'body' => 'Hello ',
            //     'mailTitle' => 'forgot',
            //     'subject' => 'Needeet Power Bank : TEST EMAIL',
            //     'data' =>  $user,
            // ];
            // $sss = \Mail::to($user['email'])->cc('svanaliya@innovegicsolutions.in')->send(new \App\Mail\MyTestMail($details));            return response()->json(['data' => $userObj, 'message' => 'OTP sent to your email', 'status' => true], $this->successStatus);

            return response()->json(['data' => $user, 'message' => 'OTP sent to your email', 'status' => true], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Invalid phone number', 'status' => false], 200);
        }
    }

    /** 
     * otp verify 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function otpVerify(Request $request)
    {
        $user = User::where('role', "ORGANIZATION")
            ->where('email', $request->all('email'))->first();
        if ($user['otp'] == $request->post('otp')) {
            $userObj = User::find($user['id']);
            $userObj->otp = '';
            $userObj->is_verified = 1;
            $userObj->device_id = $request->header('deviceid');
            $userObj->save();
            return response()->json(['message' => 'OTP Successfully verified', 'status' => true, 'data' => $userObj], $this->successStatus);
        } else {
            return response()->json(['message' => 'Please Enter valid OTP.', 'status' => false], 200);
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
            'c_password' => 'required|same:password',
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $user = User::where('role', 'ORGANIZATION')->where('email', $request->all('email'))->first();
        if (!empty($user)) {
            $userObj = User::find($user['id']);
            $userObj['password'] = Hash::make($request->post('password'));
            $userObj->save();
            return response()->json(['status' => true, 'message' => 'Password Successfully changed, please login'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Password change failed. please try again', 'status' => false], 200);
        }
    }
}
