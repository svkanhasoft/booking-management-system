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
use App\Models\Holiday;
use App\Models\Designation;
use App\Models\Plan;
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


    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
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
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $checkRecord = User::where('email', $request->all('email'))->whereIn('role', array('SUPERADMIN', 'ORGANIZATION', 'STAFF'))->first();
        if (!$checkRecord) {
            return response()->json(['message' => "Sorry, your email does't exists", 'status' => false], 200);
        }
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
        if ($checkRecord->status != 'Active') {
            return response()->json(['message' => "Sorry, your account is inactive please contact to administrator", 'status' => false], 200);
        }
        $userObj = new User();
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            // $user['subscription_expire'] = $this->checkSubscriptionExpire($user->subscriptsubscription_purchase_dateion_name,$user->subscription_name);
            $user['token'] =  $user->createToken('MyApp')->accessToken;
            if ($checkRecord->role == 'STAFF') {
                // $user['staffdetails'] =  $checkRecord->stafdetails;
                $user['staffdetails'] = $userDetais['designation_name'];
            }
            if ($checkRecord->role == 'ORGANIZATION') {
                // $user['staffdetails'] =  $checkRecord->stafdetails;
                $user['organization_name'] = $userObj->getOrganizationById($user->id)['organization_name'];
            }
            if ($user['subscription_expire_date'] >= date('Y-m-d')) {
                $user['is_plan_expire'] = false;
            } else {
                $user['is_plan_expire'] = true;
            }
            User::where(['id' => $user->id])->update([
                'last_login_date' => date('Y-m-d H:i:s'),
                'password_change' => 1,
                'device_id' => !empty($request->header('device_id')) ? $request->header('device_id') : '',
                'platform' => !empty($request->header('platform')) ? $request->header('platform') : '',
            ]);
            return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Email or password does not match', 'status' => false], 409);
        }
    }

    public function checkSubscriptionExpire($date, $planName)
    {
        $date = ($date == null) ? $date : date('Y-m-d');
        if ($date < date('Y-m-d')) {
            return true;
        } else {
            return false;
        }
    }

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
            return response()->json(['status' => false, 'message' => $error], 200);
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
            return response()->json(['status' => false, 'message' => $error], 200);
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

    /**
     * update organization by super admin
     *
     * @return \Illuminate\Http\Response
     */
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
            return response()->json(['status' => false, 'message' => $error], 200);
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

    /**
     * Organization updates thair profile
     *
     * @return \Illuminate\Http\Response
     */
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

            return response()->json(['status' => false, 'message' => $error], 200);
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

    /**
     * Change organization activity status [Active/Inactive]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ChangeOrgActivityStatus(Request $request)
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
            $userList = User::where(['parent_id' => $requestData['id'], 'role' => 'STAFF'])->get()->toArray();
            $userArray = array_column($userList, 'id');
            $userArray[] = $requestData['id'];
            $userUpdate = User::whereIn('id', $userArray)->update([
                'status' => $requestData['status'],
            ]);
            if ($userUpdate) {
                // \Log::info("Organization status changed successfully");
                return response()->json(['status' => true, 'message' => 'Organization status changed successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something went wrong.', 'status' => false], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * Get All plans
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllPlan(Request $request)
    {
        try {
            $planList = Plan::all();
            return response()->json([
                'status' => true, 'message' => 'Plan get Successfully',
                'data' => $planList
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' =>  $e->getMessage()], 400);
        }
    }
    /**
     * @param  int  $id
     * Update plan by id
     * @return \Illuminate\Http\Response
     */
    public function updatePlan(Request $request, $id)
    {
        try {
            $requestData = $request->all();
            $planObj = Plan::findOrFail($id);
            $requestData['updated_by'] = $this->userId;
            $resonse =  $planObj->update($requestData);
            if ($resonse) {
                return response()->json(['status' => true, 'message' => 'Plan update Successfully.', 'data' => $planObj], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Plan update failed!', 'status' => false], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' =>  $e->getMessage()], 400);
        }
    }


    /**
     * [getPlan description]
     *
     * @param   Request  $request  [$request description]
     * @param   [int]   $id       [$id description]
     *
     * @return  [array]             [return description]
     */
    public function getPlan(Request $request, $id)
    {
        try {
            $requestData = $request->all();
            $planObj = Plan::findOrFail($id);
            if ($planObj) {
                return response()->json(['status' => true, 'message' => 'Plan get Successfully.', 'data' => $planObj], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Plan update failed!', 'status' => false], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' =>  $e->getMessage()], 400);
        }
    }

    public function getHoliday(Request $request)
    {
        try {
            $requestData = $request->all();
            $planObj = Holiday::all();
            if ($planObj) {
                return response()->json(['status' => true, 'message' => 'Holiday get Successfully.', 'data' => $planObj], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Holiday no available!', 'status' => false], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' =>  $e->getMessage()], 400);
        }
    }

    /**
     * [addHoliday description]
     *
     * @param   Request  $request  [$request description]
     *
     * @return  [json]             [return description]
     */
    public function addHoliday(Request $request)
    {
        try {
            $requestData = $request->all();
            $planObj = new Holiday();
            $resonse =  $planObj->create($requestData);
            if ($resonse) {
                return response()->json(['status' => true, 'message' => 'Holiday add Successfully.', 'data' => $resonse], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Holiday add failed!', 'status' => false], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' =>  $e->getMessage()], 400);
        }
    }
    /**
     * [editHoliday description]
     *
     * @param   Request  $request  [$request description]
     * @param   [type]   $id       [$id description]
     *
     * @return  [type]             [return description]
     */
    public function editHoliday(Request $request, $id)
    {
        try {
            $requestData = $request->all();
            $planObj = Holiday::findOrFail($id);
            $resonse =  $planObj->update($requestData);
            if ($resonse) {
                return response()->json(['status' => true, 'message' => 'Holiday update Successfully.', 'data' => $resonse], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Holiday update failed!', 'status' => false], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' =>  $e->getMessage()], 400);
        }
    }
    /**
     * [getHolidayById description]
     *
     * @param   Request  $request  [$request description]
     * @param   [integer]   $id       [$id description]
     *
     * @return  [json array]             [return description]
     */
    public function getHolidayById(Request $request, $id)
    {
        try {
            $result = Holiday::findOrFail($id);
            if ($result) {
                return response()->json(['status' => true, 'message' => 'Holiday details get Successfully.', 'data' => $result], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Holiday details get failed!', 'status' => false], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' =>  $e->getMessage()], 400);
        }
    }
}
