<?php

namespace App\Http\Controllers\API\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Validator;
use Session;
use App\Models\User;
use App\Models\Organization;
use App\Models\Designation;
use App\Models\OrganizationUserDetail;
use Hash;

class SuperAdminController extends Controller
{
    public $successStatus = 200;
    protected $userId;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!empty(Auth::user())) {
                $this->userId = Auth::user()->id;
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        echo "hiiii SuperAdminController";
    }
    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 422);
        }

        $checkRecord = User::where('email', $request->all('email'))->where('role', 'SUPERADMIN')->count();
        if ($checkRecord == 0) {
            return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 404);
        }
        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => 'SUPERADMIN'])) {
            $user = Auth::user();
            $user['token'] =  $user->createToken('MyApp')->accessToken;
            return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Email or password are not match', 'status' => false], 409);
        }
    }

    /** 
     * login for super admin and  organization admin api 
     * 
     * @return \Illuminate\Http\Response 
     */

    public function signinV2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        $checkRecord = User::where('email', $request->all('email'))->whereIn('role', array('SUPERADMIN', 'ORGANIZATION', 'STAFF'))->first();

        $query = Designation::select(
            'designations.designation_name'
        );
        $query->leftJoin('organization_user_details',  'organization_user_details.designation_id', '=', 'designations.id');
        $query->where('organization_user_details.user_id', $checkRecord->id);
        $userDetais = $query->first();
        //return $userDetais;
        //print_r($userDetais['designation_name']);exit();
        if (empty($checkRecord)) {
            return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 404);
        }
        if ($checkRecord->status !== 'Active') {
            return response()->json(['message' => "Sorry, your account is inactive please contact to administrator", 'status' => false], 403);
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $user['token'] =  $user->createToken('MyApp')->accessToken;
            if($checkRecord->role == 'STAFF'){
                // $user['staffdetails'] =  $checkRecord->stafdetails;
                $user['staffdetails'] = $userDetais['designation_name'];
            }
            User::where(['id' => $user->id])->update([
                'last_login_date' => date('Y-m-d H:i:s'),
                'password_change' => 1
            ]);
            return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Email or password are not match', 'status' => false], 409);
        }
    }

    // public function signinV2(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required',
    //         'password' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         $error = $validator->messages()->first();
    //         return response()->json(['status' => false, 'message' => $error], 200);
    //     }
    //     $checkRecord = User::where('email', $request->all('email'))->whereIn('role', array('SUPERADMIN', 'ORGANIZATION', 'STAFF'))->first();
    //     if (empty($checkRecord)) {
    //         return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 200);
    //     }
    //     if ($checkRecord->status !== 'Active') {
    //         return response()->json(['message' => "Sorry, your account is inactive please contact to administrator", 'status' => false], 200);
    //     }

    //     if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
    //         $user = Auth::user();
    //         $user['token'] =  $user->createToken('MyApp')->accessToken;

    //         User::where(['id' => $user->id])->update([
    //             'last_login_date' => date('Y-m-d H:i:s'),
    //             'password_change' => 1
    //         ]);
    //         return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
    //     } else {
    //         return response()->json(['message' => 'Sorry, Email or password are not match', 'status' => false], 200);
    //     }
    // }
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'password' => 'required|min:6',
            'email' => 'required|unique:users:email',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 422);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $userRes = User::find($user['id']);
        if (!empty($userRes)) {
            $userRes['token'] =  $user->createToken('MyApp')->accessToken;
            return response()->json(['status' => true, 'message' => 'Register Successfully completed.', 'data' => $userRes], $this->successStatus);
        } else {
            return response()->json(['status' => false, 'message' => "something will be wrong"], 409);
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
            'status' => true, 'message' => 'Super Admin Details Get Successfully',
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
                return response()->json(['status' => false, 'message' => 'Sorry, logout failed'], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /** 
     * Forgot api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function forgot(Request $request)
    {
        $user = User::where('email', $request->all('email'))->count();
        if ($user >  0) {
            $userObj = new User();
            $mailRes =  $userObj->sendForgotEmail($request);
            return response()->json(['message' => 'Please check your email and change your password', 'status' => true], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Invalid Email address.', 'status' => false], 409);
        }
    }

    /** 
     * otp verify 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function otpVerify(Request $request)
    {
        $user = User::where('role', "SUPERADMIN")
            ->where('email', $request->all('email'))->first();
        if ($user['otp'] == $request->post('otp')) {
            $userObj = User::find($user['id']);
            $userObj->otp = '';
            $userObj->is_verified = 1;
            $userObj->device_id = $request->header('deviceid');
            $userObj->save();
            return response()->json(['message' => 'OTP Successfully verified', 'status' => true, 'data' => $userObj], $this->successStatus);
        } else {
            return response()->json(['message' => 'Please Enter valid OTP.', 'status' => false], 409);
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
            'old_password' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        if (!(Hash::check($request->old_password, Auth::user()->password))) {
            return response()->json(['status' => false, 'message' => "Your old password can't be match"], 400);
        }
        $user = User::where('role', 'SUPERADMIN')->where('id', $this->userId)->first();
        if (!empty($user)) {
            $userObj = User::find($user['id']);
            $userObj['password'] = Hash::make($request->post('password'));
            $userObj['password_change'] = 1;
            $userObj->save();
            return response()->json(['status' => true, 'message' => 'Password Successfully changed, please login'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Password change failed. please try again', 'status' => false], 409);
        }
    }



    /** 
     * get organization details api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function getOrgdetails(Request $request, $id)
    {
        $userObj = new User();
        $user = $userObj->getOrganizationById($id);
        return response()->json([
            'status' => true, 'message' => 'organization Details Get Successfully',
            'data' => $user
        ], $this->successStatus);
    }

    public function updateorg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required',
            'contact_number' => 'required|min:6',
            'contact_person_name' => 'required',
            'address_line_1' => 'required',
            'city' => 'required',
            'postcode' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        try {
            $requestData = $request->all();
            $userId = $requestData['id'];
            unset($requestData['id']);
            $role = User::findOrFail($userId);
            $roleUpdated = $role->update($requestData);
            if (!empty($roleUpdated)) {
                $requestData = $request->all();
                $org = Organization::where(['user_id' =>  $userId])->update([
                    "organization_name" => $requestData['organization_name'],
                    "contact_person_name" => $requestData['contact_person_name'],
                ]);
                return response()->json(['status' => true, 'message' => 'Organization detail updated successfully.', 'data' => $requestData], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => "something will be wrong"], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function updates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'contact_number' => 'required|min:6',
            'address_line_1' => 'required',
            'city' => 'required',
            'postcode' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();

            return response()->json(['status' => false, 'message' => $error], 422);
        }

        $requestData = $request->all();
        $role = User::findOrFail($this->userId);
        $roleUpdated = $role->update($requestData);
        if (!empty($roleUpdated)) {
            $user = Auth::user();
            return response()->json(['status' => true, 'message' => 'Profile updated successfully.', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['status' => false, 'message' => "something will be wrong"], 409);
        }
    }
}
