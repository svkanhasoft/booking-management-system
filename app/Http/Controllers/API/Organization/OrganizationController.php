<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\Organization;
use Hash;
use Config;
class OrganizationController extends Controller
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
            'contact_person_name' => 'required',
            'contact_number' => 'required',
            'address_line_1' => 'required',
            // 'address_line_2' => 'required',
            'city' => 'required',
            // 'password' => 'required|min:6',
            'email' => 'required|email|unique:users',
        ]);
        
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $input = $request->all();
        $input['password'] = Hash::make(123456);
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
            $requestData['start_date'] = date('Y-m-d');
            $requestData['end_date'] =  date('Y-m-d', strtotime('+1 month'));
            $requestData['plan'] = 'Basic';
            Organization::create($requestData);

            $userObj = new User();
            $mailRes =  $userObj->sendRegisterEmail($request);

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
        // $user = Auth::user();
        // $user->Organization;
        $userObj = new User();
        $user = $userObj->getOrganizationById(Auth::user()->id);
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
        $userObj = new User();
        $mailRes =  $userObj->sendForgotEmail($request);
        if ($mailRes) {
            return response()->json(['message' => 'Please check your email and change your password', 'status' => true], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Invalid Email address.', 'status' => false], 200);
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
            'conform_password' => 'required|same:password',
            'old_password' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        if (!(Hash::check($request->old_password, Auth::user()->password))) {
            return response()->json(['status' => false, 'message' => "Your old password can't be match"], 200);
        }
        $user = User::where('role', 'ORGANIZATION')->where('id', $this->userId)->first();
        // $user = User::where('role', 'ORGANIZATION')->where('email', $request->all('email'))->first();
        if (!empty($user)) {
            $userObj = User::find($user['id']);
            $userObj['password'] = Hash::make($request->post('password'));
            $userObj->save();
            return response()->json(['status' => true, 'message' => 'Password Successfully changed, please login'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Password change failed. please try again', 'status' => false], 200);
        }
    }
    /** 
     * change Status 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $userObj = User::find($request->post('user_id'));
        $userObj['status'] = $request->post('status');
        $res =  $userObj->save();
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Status changed successfully'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, status not change.', 'status' => false], 200);
        }
    }


    /**
     * Search user listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $keyword = $request->get('keyword');
        $perPage = Config::get('constants.pagination.perPage');
        
        $query = User::select("users.*");
        $query->leftjoin('organization_user_details as oud', 'oud.user_id', '=', 'users.id');
        $query->leftjoin('roles',  'roles.id', '=', 'oud.role_id');
        $query->leftjoin('designations',  'designations.id', '=', 'oud.designation_id');
        $query->Where('users.status',  "Active");

        $query->Where('users.first_name',  'LIKE', "%$keyword%");
        $query->orWhere('users.last_name',  'LIKE', "%$keyword%");
        $query->orWhere('users.email',  'LIKE', "%$keyword%");
        $query->orWhere('oud.contact_number',  'LIKE', "%$keyword%");
        $query->orWhere('designations.designation_name',  'LIKE', "%$keyword%");
        $query->orWhere('roles.role_name',  'LIKE', "%$keyword%");
        $query->groupBy('users.id');
        $res =  $query->latest('users.created_at')->paginate($perPage);
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Status changed successfully', 'data' => $res], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, status not change.', 'status' => false], 200);
        }
    }

    public function organizationlist(Request $request, $status = null)
    {
        // $validator = Validator::make($request->all(), [
        //     // 'keyword' => 'required',
        // ]);
        // if ($validator->fails()) {
        //     $error = $validator->messages()->first();
        //     return response()->json(['status' => false, 'message' => $error], 200);
        // }
        $keyword = $request->get('search');
        $status = $request->get('status');
        $perPage = Config::get('constants.pagination.perPage');
        $query = User::select(
            "users.*",
            'org.organization_name',
            'org.contact_person_name',
            'users.contact_number',
            'users.address_line_1',
            'users.address_line_2',
            'users.city',
            'users.postcode'
        );
        $query->join('organizations as org', 'org.user_id', '=', 'users.id');
        $query->where('users.role',  "ORGANIZATION");

        if (!empty($keyword)) {
            $query->Where('users.first_name',  'LIKE', "%$keyword");
            $query->orWhere('users.last_name',  'LIKE', "%$keyword%");
            $query->orWhere('users.email',  'LIKE', "%$keyword%");
            $query->orWhere('org.organization_name',  'LIKE', "%$keyword%");
            $query->orWhere('org.contact_person_name',  'LIKE', "%$keyword%");
            $query->orWhere('users.contact_number',  'LIKE', "%$keyword%");
            $query->orWhere('users.address_line_1',  'LIKE', "%$keyword%");
            $query->orWhere('users.address_line_2',  'LIKE', "%$keyword%");
            $query->orWhere('users.city',  'LIKE', "%$keyword%");
            $query->orWhere('users.postcode',  'LIKE', "%$keyword%");
        }
        if (!empty($status)) {
            $query->Where('users.status',  "$status");
        }
        $res =  $query->latest('users.created_at')->paginate($perPage);
        $count =  $query->latest('users.created_at')->paginate($perPage)->count();
        if ($count > 0) {
            // if ($res) {
            return response()->json(['status' => true, 'message' => 'Organizations listed successfully', 'data' => $res], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, organizations not available.', 'status' => false], 200);
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


    /** 
     * update profile api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function update(Request $request)
    {
        // print_r($request->all());
        // exit;
        $validator = Validator::make($request->all(), [
            'organization_name' => 'required',
            'contact_number' => 'required|min:6',
            'contact_person_name' => 'required',
            'address_line_1' => 'required',
            // 'address_line_2' => 'required',
            'city' => 'required',
            'postcode' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $requestData = $request->all();
        $role = User::findOrFail(Auth::user()->id);
        $roleUpdated = $role->update($requestData);
        if (!empty($roleUpdated)) {
            $requestData = $request->all();
            $org = Organization::where(['user_id' => Auth::user()->id])->update([
                "organization_name" => $requestData['organization_name'],
                "contact_person_name" => $requestData['contact_person_name'],
                // "contact_number" => $requestData['contact_number'],
                // "address_line_1" => $requestData['address_line_1'],
                // "address_line_2" => $requestData['address_line_2'],
                // "city" => $requestData['city'],
                // "postcode" => $requestData['postcode'],
            ]);
            return response()->json(['status' => true, 'message' => 'Update profile successfully.', 'data' => $requestData], $this->successStatus);
        } else {
            return response()->json(['status' => false, 'message' => "something will be wrong"], 200);
        }
    }
}
