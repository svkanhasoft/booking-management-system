<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests;
use App\Models\Booking;
use App\Models\BookingMatch;
use App\Models\BookingSpeciality;
use App\Models\User;
use App\Models\OrganizationUserDetail;
use App\Models\SigneesDetail;
use App\Models\SigneeOrganization;
use App\Models\Notification;
use Hash;
use App\Models\Role;
use App\Models\SigneeSpecialitie;
use App\Models\Speciality;
use App;
use App\Models\SigneeDocument;
use Carbon\Carbon;
use Config;
use DB;
use DateTime;

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

    /* Used to register user */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|unique:users',
            "first_name" => 'required|regex:/^[a-zA-Z]+$/u|max:255',
            "last_name" => 'required|regex:/^[a-zA-Z]+$/u|max:255',
            "contact_number" => 'required|numeric|regex:/^\d{10}$/',
            "role_id" => 'required',
            "designation_id" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $requestData = $request->all();
            $requestData['password'] = Hash::make(123456);
            $requestData['parent_id'] = $this->userId;
            $requestData['role'] = 'STAFF';
            // if(Auth::user()->role == 'ORGANIZATION')
            // {
            //     $requestData['created_by'] = Auth::user()->id;
            // }else{
            //     $requestData['created_by'] = Auth::user()->id;
            // }
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
                return response()->json(['message' => 'Sorry, User added failed!', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
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
        try {
            $checkRecord = User::where('email', $request->all('email'))->where('role', 'STAFF')->first();
            if (empty($checkRecord)) {
                return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 200);
            }
            if ($checkRecord->status != 'Active') {
                return response()->json(['message' => 'Sorry, Your account is Inactive, contact to organization admin', 'status' => false], 200);
            }
            if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => 'STAFF'])) {
                $userResult = Auth::user();
                $UserObj = new User();
                $user = $UserObj->getOrganizationDetails($userResult['id']);
                $user[0]['token'] =  $userResult->createToken('User')->accessToken;
                return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Email or password are not match', 'status' => false], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
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
        if ($checkRecord->status != 'Active') {
            return response()->json(['message' => 'Sorry, Your account is Inactive, contact to organization admin', 'status' => false], 200);
        }
        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => 'STAFF'])) {
            $userResult = Auth::user();
            $UserObj = new User();
            $user = $UserObj->getOrganizationDetails($userResult['id']);
            $user[0]['token'] =  $userResult->createToken('User')->accessToken;
            return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Email or password are not match', 'status' => false], 401);
        }
    }

    /**
     * Get User details
     *
     * @return \Illuminate\Http\Response
     */
    public function getDetails()
    {
        try {
            $UserObj = new User();
            $user = $UserObj->getOrganizationDetails($this->userId);
            if (!empty($user)) {
                return response()->json(['status' => true, 'message' => 'User details get successfully.', 'data' => $user], $this->successStatus);
            } else {
                return response()->json(['message' => 'something will be wrong', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }
    /**
     * Get User list
     *
     * @return \Illuminate\Http\Response
     */
    public function getuserlist(Request $request)
    {
        try {
            $UserObj = new User();
            $user = $UserObj->fetchStaflist($request, $this->userId);
            if (!empty($user)) {
                return response()->json(['status' => true, 'message' => 'User details get successfully.', 'data' => $user], $this->successStatus);
            } else {
                return response()->json(['message' => 'something will be wrong', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * Get User by id
     *
     * @return \Illuminate\Http\Response
     */
    public function getuserById($userId)
    {
        try {
            $UserObj = new User();
            $user = $UserObj->getStafById($userId);
            if (!empty($user)) {
                return response()->json(['status' => true, 'message' => 'User details get successfully.', 'data' => $user], $this->successStatus);
            } else {
                return response()->json(['message' => 'something will be wrong', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
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
        try {
            $user = User::where('role', 'STAFF')->where('id', $this->userId)->first();
            //print_r($this->userId);exit();
            if (!empty($user)) {
                $userObj = User::find($this->userId);
                $userObj['password'] = Hash::make($request->post('password'));
                $userObj['password_change'] = 1;
                $userObj->save();
                return response()->json(['status' => true, 'message' => 'Password Successfully change.'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Password change failed.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
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
        try {
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
                return response()->json(['message' => 'Sorry, Invalid user id.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
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
            'email' => 'unique:users,email,' . $requestData['id'] . 'NULL,id',
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
        try {
            $user = User::findOrFail($requestData['id']);
            // if(Auth::user()->role == 'ORGANIZATION')
            // {
            //     $user->updated_by = Auth::user()->id;
            // }else{
            //     $user->updated_by = Auth::user()->id;
            // }
            $addResult = $user->update($requestData);
            if ($addResult) {
                $oudData = OrganizationUserDetail::where('user_id', $requestData['id'])->first();
                $oud = OrganizationUserDetail::findOrFail($oudData['id']);
                $oudResult = $oud->update($requestData);
                $UserObj = new User();
                $userData = $UserObj->getStafById($user['id']);
                if ($oudResult) {
                    return response()->json(['status' => true, 'message' => 'User update Successfully', 'data' => $userData], $this->successStatus);
                } else {
                    return response()->json(['message' => 'Sorry, user update failed!', 'status' => false], 409);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * delete user/staff
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId)
    {
        try {
            OrganizationUserDetail::where('user_id', $userId)->delete();
            $userDelete = User::where('id', $userId)->delete();
            if ($userDelete) {
                return response()->json(['status' => true, 'message' => 'User deleted successfully.'], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'Sorry, User not deleted.'], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Update user/staff profile by himself
     *
     * @return \Illuminate\Http\Response
     */
    public function profileUpdate(Request $request)
    {
        //print_r($this->userId);exit();
        $validator = Validator::make($request->all(), [
            "first_name" => 'required',
            "last_name" => 'required',
            "contact_number" => 'required',
            "address_line_1" => 'required',
            "city" => 'required',
            "postcode" => 'required',
            // "role_id" => 'required',
            // "designation_id" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        if (!empty($request->post('password'))) {
            $requestData['password'] = Hash::make($request->post('password'));
        }
        $user = User::findOrFail($this->userId);
        $userCreated = $user->update($requestData);
        if ($userCreated) {
            return response()->json(['status' => true, 'message' => 'User update Successfully', 'data' =>  $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, User update failed!', 'status' => false], 409);
        }
    }





    //////////////////// candidate CRUD By Organisation ///////////////////////

    /*
     * add new candidate by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function addSignee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|unique:users',
            "first_name" => 'required|regex:/^[a-zA-Z]+$/u|max:255',
            "last_name" => 'required|regex:/^[a-zA-Z]+$/u|max:255',
            "city" => 'regex:/^[a-zA-Z]+$/u|max:255',
            "nationality" => 'regex:/^[a-zA-Z]+$/u|max:255',
            "postcode" => 'numeric|regex:/^\d{6}$/',
            "contact_number" => 'numeric|regex:/^\d{10}$/',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $requestData = $request->all();
        $a = new DateTime($requestData['date_of_birth']);
        $b = new Datetime(date('Y-m-d'));
        $interval = $b->diff($a);
        if ($interval->y < 18) {
            return response()->json(['status' => false, 'message' => 'Age must be greater than 18 years']);
        }

        try {
            $requestData['password'] = Hash::make($request->post('password'));
            $requestData['parent_id'] = $this->userId;
            $requestData['role'] = 'SIGNEE';
            if (Auth::user()->role == 'ORGANIZATION') {
                $requestData['created_by'] = Auth::user()->id;
            } else {
                $requestData['parent_id'] = Auth::user()->parent_id;
                $requestData['created_by'] = Auth::user()->id;
            }
            $userCreated = User::create($requestData);
            if ($userCreated) {
                $requestData['user_id'] = $userCreated['id'];
                $orgResult = SigneesDetail::create($requestData);

                $objSpeciality = new SigneeSpecialitie();
                $objSpeciality->updateSpeciality($requestData['speciality'], $userCreated['id'], $this->userId, false);

                //$requestData['organization_id'] = $request->post('organization_id');
                $requestData['organization_id'] = $this->userId;
                $requestData['user_id'] = $userCreated['id'];
                $sing = SigneeOrganization::create($requestData);
                if ($orgResult) {
                    $UserObj = new User();
                    $mailRes =  $UserObj->sendRegisterEmail($request);
                    return response()->json(['status' => true, 'message' => 'Candidate added Successfully', 'data' => $userCreated], $this->successStatus);
                }
            } else {
                return response()->json(['message' => 'Sorry, Candidate added failed!', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * View candidate by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function viewSignee(Request $request)    //get all candidate by organisation
    {
        //print_r($this->userId);exit();
        $UserObj = new User();
        $user = $UserObj->getSignee($request, $this->userId);
        //print_r($user);exit();
        if (!empty($user)) {
            return response()->json(['status' => true, 'message' => 'Candidate Get Successfully', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Something is Wrong!', 'status' => false], 409);
        }
    }

    /*
     * Edit candidate by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function editSignee(Request $request)
    {
        $requestData = $request->all();
        //print_r($requestData);exit();
        $validator = Validator::make($request->all(), [
            "first_name" => 'required|regex:/^[a-zA-Z]+$/u|max:255',
            "last_name" => 'required|regex:/^[a-zA-Z]+$/u|max:255',
            "city" => 'regex:/^[a-zA-Z]+$/u|max:255',
            "nationality" => 'regex:/^[a-zA-Z]+$/u|max:255',
            "postcode" => 'numeric',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            // print_r($requestData);exit();
            if (!empty($request->post('password'))) {
                $requestData['password'] = Hash::make($request->post('password'));
            }
            $signee = User::findOrFail($requestData['id']);
            if (Auth::user()->role == 'ORGANIZATION') {
                $signee->updated_by = Auth::user()->id;
            } else {
                $signee->updated_by = Auth::user()->id;
            }
            //print_r($signee->parent_id);exit();
            $signeeUpdated = $signee->update($requestData);
            if ($signeeUpdated) {
                $signeeDetailResult = SigneesDetail::where('user_id', '=', $requestData['id'])->firstOrFail();
                $result = $signeeDetailResult->update($requestData);
                if ($result) {
                    $speciality = new Speciality();
                    $speciality->addOrUpdateSpeciality($requestData['speciality'], $requestData['id'], $signee->parent_id);
                    $user = User::find($this->userId)->SigneesDetail;
                    return response()->json(['status' => true, 'message' => 'Candidate Updated Successfully', 'data' =>  $signee], $this->successStatus);
                }
            } else {
                return response()->json(['message' => 'Sorry, Candidate update failed!', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Delete candidate by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteSignee($id)
    {
        try {
            $user = User::find($id);
            $deleteUser = User::where('id', $id)->delete();
            $deleteSigneeOrg = SigneeOrganization::where(['user_id' => $id, 'organization_id' => $user['parent_id']])->delete();
            $deleteSigneeDetail = SigneesDetail::where(['user_id' => $id])->delete();
            $deleteSigneeSpec = SigneeSpecialitie::where(['user_id' => $id]);
            if ($user) {
                return response()->json(['status' => true, 'message' => 'Candidate deleted successfully.'], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'Sorry, Candidate not deleted.'], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Generate candidate id
     *
     * @return \Illuminate\Http\Response
     */
    public function getCandidate(Request $request)
    {
        try {
            $time = [];
            $time['candidate_id'] = date("ymdHis");
            if ($time) {
                return response()->json(['status' => true, 'message' => 'Candidate get successfully', 'data' => $time], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Candidate not available!', 'status' => false], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Get Candidate by id by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function getMySigneeById($id)  //get Candidate by id from org
    {
        try {
            $userObj = new User();
            $user = $userObj->getSigneeById($id);
            if ($user) {
                return response()->json(['status' => true, 'message' => 'Candidate get successfully', 'data' => $user], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Candidate not available!', 'status' => false], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Change shift status
     *
     * @return \Illuminate\Http\Response
     */
    public function changeShiftStatus(Request $request)
    {
        //print_r(Auth::user()->role);exit();
        $requestData = $request->all();
        //print_r($requestData);exit();
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'booking_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            if (Auth::user()->role == 'ORGANIZATION') {
                $booking = Booking::firstOrNew(['id' => $requestData['booking_id'], 'user_id' => Auth::user()->id]);
                $booking->status = $requestData['status'];
                $booking->save();
            } else {
                $booking = Booking::firstOrNew(['id' => $requestData['booking_id'], 'user_id' => Auth::user()->parent_id]);
                $booking->status = $requestData['status'];
                $booking->save();
            }

            // $objNotification = new Notification();
            // $notification = $objNotification->addNotificationV2($booking,'shift_confirm');

            if (!empty($booking)) {
                return response()->json(['status' => true, 'message' => 'Shift status changed successfully', 'data' => $booking], $this->successStatus);
            } else {
                return response()->json(['message' => 'Shift status not changed!', 'status' => false], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Change Candidate profile status
     *
     * @return \Illuminate\Http\Response
     */
    public function changeSigneeProfileStatus(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'signee_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            // $statusChanged = User::where(['id' => $requestData['signee_id'], 'parent_id'=> Auth::user()->id])->update([
            //     'status' => $requestData['status']
            // ]);

            $data = User::findOrFail($requestData['signee_id']);
            $data->status = $requestData['status'];
            $res = $data->save();

            SigneeOrganization::where(['user_id' => $requestData['signee_id'], 'organization_id' => (Auth::user()->role == 'ORGANIZATION') ? Auth::user()->id: Auth::user()->parent_id])->update([
                'profile_status' =>  $requestData['status']
            ]);

            // $objNotification = new Notification();
            // $notification = $objNotification->addNotificationV2($requestData,'profile_status');
            if (!empty($res)) {
                return response()->json(['status' => true, 'message' => 'Candidate profile status changed successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, status not change.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Change booking status by Candidate and organization/staff
     *
     * @return \Illuminate\Http\Response
     */
    public function bookingStatus(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'signee_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $objBooking = new Booking();
            //dd(Auth::user()->role);
            if ($requestData['status'] == 'CANCEL' || $requestData['status'] == 'DECLINE' || $requestData['status'] == 'PENDING') {
                //echo Auth::user()->role;exit;
                //$signee = $objBooking->getMetchByBookingId($requestData['booking_id']);
                if (Auth::user()->role == 'SIGNEE') {
                    //dd(Auth::user()->role);
                    return $this->cancelShiftBySignee($requestData);
                } else if (Auth::user()->role == 'STAFF' || Auth::user()->role == 'ORGANIZATION') {
                    //print_r(Auth::user()->role);exit();
                    return $this->cancelShiftBYStaffOrOrg($requestData);
                }
            } elseif ($requestData['status'] == 'ACCEPT') {
                $res =  $this->checkShiftBooking($requestData['booking_id'], $requestData['signee_id']);
                if (count($res) > 0) {
                    return response()->json(['message' => 'Sorry, You have already booked shift with same date.', 'status' => false], 404);
                }
                return $this->acceptShiftBySignee($requestData);
            } else if ($requestData['status'] == 'CONFIRMED') {
                $res =  $this->checkShiftBooking($requestData['booking_id'], $requestData['signee_id']);
                if (count($res) > 0) {
                    return response()->json(['message' => 'Sorry, Shift already booked with same date for this candidate.', 'status' => false], 200);
                }
                $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $requestData['signee_id'], 'booking_id' => $requestData['booking_id']]);
                $objBookingMatch->signee_booking_status = $requestData['status'];
                $objBookingMatch->save();

                $objBooking = new Booking();
                $matchSignee = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $requestData['signee_id']);

                $objNotification = new Notification();
                if (Auth::user()->role == "SIGNEE") {
                    $notification = $objNotification->addNotificationV2($matchSignee, 'candidate_accept');
                    if ($notification) {
                        return response()->json(['status' => true, 'message' => 'Candidate successfully accepted the shift'], $this->successStatus);
                    } else {
                        return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 200);
                    }
                } else if (Auth::user()->role == "ORGANIZATION" || Auth::user()->role == "STAFF") {
                    if ($matchSignee->signee_status == 'Interested') {
                        $notification = $objNotification->addNotificationV2($matchSignee, 'org_accept');
                        return response()->json(['status' => true, 'message' => 'Admin successfully accepted shift in which you applied'], $this->successStatus);
                    } else {
                        $notification = $objNotification->addNotificationV2($matchSignee, 'super_assign');
                    }
                } else {
                    $objBooking->sendBookingConfirmEmail($matchSignee);
                }

                if ($objBookingMatch) {
                    return response()->json(['status' => true, 'message' => 'Admin successfully assigned shift to you'], $this->successStatus);
                } else {
                    return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 404);
                }
            } else if ($requestData['status'] == 'OFFER') {
                return $this->offerToSignee($requestData);
            } else if ($requestData['status'] == 'INVITE') {
                return $this->offerToSignee($requestData);
            } else if ($requestData['status'] == 'REJECTED') {
                if (Auth::user()->role == "STAFF" || Auth::user()->role == "ORGANIZATION") {
                    $matchSignee = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $requestData['signee_id']);
                    if ($matchSignee->signee_status == 'Interested') {
                        return $this->rejectedToSignee($requestData);
                    } else {
                        return $this->rejectedToInvitedSignee($requestData);
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function checkShiftBooking($booking_id, $signee_id)
    {
        $bookRes = Booking::find($booking_id);
        $count = BookingMatch::where([
            'signee_id' => $signee_id,
            'signee_booking_status' => 'CONFIRMED', 'shift_id' => $bookRes['shift_id'],
            // 'booking_id' => $requestData['booking_id'],
            'booking_date' => $bookRes['date']
        ])->get();
        return $count;
    }

    public function acceptShiftBySignee($requestData)
    {
        try {
            $objBooking = new Booking();
            $update = BookingMatch::where(['signee_id' => $this->userId, 'booking_id' => $requestData['booking_id']])->update([
                'signee_booking_status' => $requestData['status']
            ]);
            $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $requestData['signee_id']);
            //print_r($signeeMatch);exit;
            $mailSent = $objBooking->sendBookingAcceptBySigneeEmail($signeeMatch);

            //send mail to candidate organization
            $orgDetail = User::where('id', $signeeMatch['organization_id'])->first()->toArray();
            $comArray = array_merge($signeeMatch->toArray(), $orgDetail);
            $orgMailSent = $objBooking->sendSigneeAccepBookingEmailToOrg($comArray);

            if ($update) {
                return response()->json(['status' => true, 'message' => 'Offer accepted successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function cancelShiftBySignee($requestData)
    {
        //print_r(Auth::user()->id);exit;
        try {
            $update = BookingMatch::where(['signee_id' => $this->userId, 'booking_id' => $requestData['booking_id']])->update([
                'signee_booking_status' => 'CANCEL', 'booking_cancel_date' => Carbon::now(),
            ]);
            $objBooking = new Booking();
            $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $this->userId);
            // print_r($signeeMatch);exit;
            // booking::where(['id' => $requestData['booking_id']])->update(['status' => 'CREATED']);
            $mailSent = $objBooking->sendBookingCancelBySigneeEmail($signeeMatch);

            // send mail to candidate organization
            $orgDetail = User::where('id', $signeeMatch['organization_id'])->first()->toArray();
            $comArray = array_merge($signeeMatch->toArray(), $orgDetail);
            $orgMailSent = $objBooking->sendSigneeCancelBookingEmailToOrg($signeeMatch);
            if ($update) {
                return response()->json(['status' => true, 'message' => 'Shift rejected by candidate successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    // public function cancelShiftBySignee($requestData)
    // {
    //     //print_r($requestData['booking_id']);exit;
    //     $objBooking = new Booking();
    //     try {
    //         $booking = $objBooking->getBooking($requestData['booking_id']);

    //         $deductedTime =  date('H', strtotime($booking['start_time'])) - 2;
    //         $var  = Carbon::now('Asia/Kolkata');
    //         $time = date('H',strtotime($var->toTimeString()));
    //         print_r($deductedTime);
    //         exit;

    //         if(date('H', strtotime($deductedTime)) < $time)
    //         {
    //             echo "sorry your time limit to cancel the booking is over";exit;
    //         }
    //         else{
    //             echo "success";exit;
    //             $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $requestData['signee_id']);
    //             BookingMatch::where(['signee_id' => $this->userId, 'booking_id' => $requestData['booking_id']])->update([
    //                 'signee_booking_status' => $requestData['status'], 'booking_cancel_date' => Carbon::now(),
    //             ]);
    //             // booking::where(['id' => $requestData['booking_id']])->update(['status' => 'CREATED']);
    //             $update = $objBooking->sendBookingCancelBySigneeEmail($signeeMatch);

    //             //send booking open mail to other signees
    //             //  $signeeList = $objBooking->getMetchByBookingId($requestData['booking_id']);
    //             //  $sendBookingOpenMail = $objBooking->sendBookingOpenEmail($signeeList);
    //             if ($update) {
    //                 return response()->json(['status' => true, 'message' => 'Booking cancelled successfully'], $this->successStatus);
    //             } else {
    //                 return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
    //             }
    //         }

    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
    //     }
    // }

    public function cancelShiftBYStaffOrOrg($requestData)
    {
        $objBooking = new Booking();
        try {

            $booking = booking::findOrFail($requestData['booking_id']);
            if ($booking['status'] == 'CREATED') {
                $update = BookingMatch::where(['booking_id' => $requestData['booking_id'], 'signee_id' => $requestData['signee_id']])->update([
                    'signee_booking_status' => $requestData['status'], 'booking_cancel_date' => Carbon::now(),
                ]);
                $signees = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $requestData['signee_id']);
                //$objBooking->sendBookingCancelByStaffEmail($signees);
                // $booking['status'] = $requestData['status'];
                // $booking->update();
            }
            // else {
            //     // dd(array_search($booking['status'], array('CONFIRMED', 'APPLY', 'OFFER'), true));
            //     if ($booking['status'] == 'CONFIRMED') {
            //         $booking['status'] = $requestData['status'];
            //         $bookingUpdate = $booking->update();
            //         // $update = BookingMatch::where(['booking_id' => $requestData['booking_id'], 'signee_id' => $requestData['signee_id']])->update([
            //         //     'signee_booking_status' => $requestData['status'], 'booking_cancel_date' => Carbon::now(),
            //         // ]);
            //         // $update = BookingMatch::where(['signee_id' => $requestData['signee_id'], 'booking_id' => $requestData['booking_id']])->update([
            //         //     'signee_booking_status' => $requestData['status'], 'booking_cancel_date' => Carbon::now(),
            //         // ]);
            //         $objBooking->sendBookingCancelByStaffEmail($signees);
            //     }
            //}
            if ($booking) {
                return response()->json(['status' => true, 'message' => 'Booking cancelled by admin successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function offerToSignee($postData)
    {
        //print_r($postData);exit;
        try {
            $objBooking = new Booking();
            // $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $postData['signee_id'], 'booking_id' => $postData['booking_id']]);
            // $objBookingMatch->signee_booking_status = $postData['status'];
            // $objBookingMatch->save();

            $objBookingMatch = BookingMatch::where(['booking_id' => $postData['booking_id'], 'signee_id' => $postData['signee_id']])->update([
                'signee_booking_status' => $postData['status']
            ]);

            $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($postData['booking_id'], $postData['signee_id']);
            $mailSent = $objBooking->sendOfferToSigneeEmail($signeeMatch);

            if ($objBookingMatch) {
                return response()->json(['status' => true, 'message' => 'Offer successfully send to candidate'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }
    public function rejectedToSignee($postData)
    {
        try {
            $objBooking = new Booking();
            $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $postData['signee_id'], 'booking_id' => $postData['booking_id']]);
            $objBookingMatch->signee_booking_status = $postData['status'];
            $objBookingMatch->save();

            $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($postData['booking_id'], $postData['signee_id']);
            if ($objBookingMatch) {
                $objNotification = new Notification();
                $notification = $objNotification->addNotificationV2($signeeMatch, 'REJECTED');
                return response()->json(['status' => true, 'message' => 'Candidate successfully rejected from shift'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function rejectedToInvitedSignee($postData)
    {
        try {
            $objBooking = new Booking();
            $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $postData['signee_id'], 'booking_id' => $postData['booking_id']]);
            $objBookingMatch->signee_booking_status = $postData['status'];
            $objBookingMatch->save();

            $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($postData['booking_id'], $postData['signee_id']);
            if ($objBookingMatch) {
                $objNotification = new Notification();
                if (Auth::user()->role == "ORGANIZATION") {
                    $notification = $objNotification->addNotificationV2($signeeMatch, 'invited_signee_rejected_byAdmin');
                } else {
                    $notification = $objNotification->addNotificationV2($signeeMatch, 'invited_signee_rejected_byStaff');
                }

                return response()->json(['status' => true, 'message' => 'Candidate successfully rejected from shift'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Change Candidate document status
     *
     * @return \Illuminate\Http\Response
     */
    public function changeDocStatus(Request $request)
    {
        $requestData = $request->all();
        //print_r($requestData);exit();
        $validator = Validator::make($request->all(), [
            'signee_id' => 'required',
            'organization_id' => 'required',
            'key' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            // echo 'dddd';exit;
            $objNotification = new Notification();
            $notification = $objNotification->addNotificationV2($requestData, 'DOCS', $requestData['key']);

            $signeeDocs = SigneeDocument::where(['signee_id' => $requestData['signee_id'], 'organization_id' => $requestData['organization_id'], 'key' => $requestData['key']])->get()->toArray();
            $idArrray = array_column($signeeDocs, 'id');
            $update = SigneeDocument::whereIn('id', $idArrray)->update(array('document_status' => $requestData['document_status'], 'updated_by' => Auth::user()->id));
            if ($update) {
                return response()->json(['status' => true, 'message' => 'Document  status updated successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Document status not updated.', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Export selected signees in pdf file
     *
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request)
    {
        // echo "123";exit;
        try {
            $downloadPath = Config::get('constants.path.pdf_download');
            $requestData = $request->all();
            $objBooking = new Booking();
            $data = [];
            $result['data']['signee'] = $objBooking->getSigneeForPDF($requestData);
            $result['data']['booking'] = $objBooking->getBooking($requestData['booking_id']);
            //print_r($result['data']);exit();
            if (!empty($result['data'])) {
                $result['title'] = 'Candidate Details';
                $result['date'] = date('m/d/Y');
                //print_r($result);exit();
                $pdf = App::make('dompdf.wrapper');
                // load from other pages use object or array by comma like (pdf-view,$user)
                $pdf->loadView('signee', $result);
                // return $pdf->stream();
                $filePath = public_path() . '/uploads/signee_pdf/';
                $time = date('Ymdhms');
                $file = $filePath . "$time-candidate.pdf";
                file_put_contents($file, $pdf->output());
                $data['pdf_path'] = $downloadPath . "$time-candidate.pdf";
                return response()->download($file,  "$time-candidate.pdf");
                // return response()->json(['status' => true, 'data' => $data, 'message' => 'pdf successfully generated'], $this->successStatus);
            } else {
                return response()->json(['message' => 'something will be wrong', 'status' => false], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
        unlink($file);
        // return response()->json(['status' => true, 'message' => $file], 200);
    }

    /*
     * Invite candidate for shift
     *
     * @return \Illuminate\Http\Response
     */
    public function inviteSigneeForTheShift(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'signee_id' => 'required',
            'booking_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $objBooking = new Booking();
            $result = $objBooking->getSigneeForInvite($requestData);
            $res = $objBooking->sendBookingInvitationMail($result);
            if ($res) {
                BookingMatch::where('booking_id', $requestData['booking_id'])->whereIn('signee_id', $requestData['signee_id'])->update(['signee_booking_status' => 'OFFER']);
                return response()->json(['status' => true, 'message' => 'Candidate offer send successfully.'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something went wrong.', 'status' => false], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * Change signee's payment status
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeSigneePaymentStatus(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'signee_id' => 'required',
            'booking_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $booking = booking::findOrFail($requestData['booking_id']);
            if ($booking['status'] == 'CONFIRMED') {
                $bookingMatch = BookingMatch::where(['signee_id' => $requestData['signee_id'], 'booking_id' => $requestData['booking_id']])->update([
                    'payment_status' => $requestData['payment_status'],
                ]);
                $requestData['organization_id'] = (Auth::user()->role == "ORGANIZATION") ? Auth::user()->id : Auth::user()->parent_id;
                $notificationObj = new Notification();
                $notificationObj->addNotificationV2($requestData, 'payment');
                if ($bookingMatch) {
                    return response()->json(['status' => true, 'message' => 'Payment status changed successfully'], $this->successStatus);
                } else {
                    return response()->json(['message' => 'Sorry, something went wrong.', 'status' => false], 400);
                }
            } else {
                return response()->json(['message' => 'Sorry, something went wrong.', 'status' => false], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function getAllNotifications(Request $request)
    {

        $perPage = Config::get('constants.pagination.perPage');
        //print_r($showing);exit;
        try {
            $showing = $request->get('showing');
            $query = Notification::select('*');

            if (Auth::user()->role == "SIGNEE") {
                $query->Where(['signee_id' => Auth::user()->id, 'organization_id' => Auth::user()->parent_id, 'is_showing_for' => "SIGNEE"]);
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(10));
                // $query->Where(['signee_id' => Auth::user()->id, 'organization_id' => Auth::user()->parent_id, 'is_showing_for' => $showing]);
            } else {
                $query->Where(['organization_id' => (Auth::user()->parent_id == null ? Auth::user()->id : Auth::user()->parent_id), 'is_showing_for' => 'ORGANIZATION']);
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(10));
                // $query->Where(['organization_id' => Auth::user()->parent_id, 'is_showing_for' => $showing]);
            }
            // $unread = $query->where('is_read',0)->count();
            $notification = $query->orderBy('id', 'desc')->paginate($perPage);

            $query2 = Notification::select('*');
            if (Auth::user()->role == "SIGNEE") {
                $query2->Where(['signee_id' => Auth::user()->id, 'organization_id' => Auth::user()->parent_id, 'is_showing_for' => "SIGNEE"]);
                $query2->whereDate('created_at', '>=', Carbon::now()->subDays(10));
                // $query->Where(['signee_id' => Auth::user()->id, 'organization_id' => Auth::user()->parent_id, 'is_showing_for' => $showing]);
            } else {
                $query2->Where(['organization_id' => (Auth::user()->parent_id == null ? Auth::user()->id : Auth::user()->parent_id), 'is_showing_for' => 'ORGANIZATION']);
                $query2->whereDate('created_at', '>=', Carbon::now()->subDays(10));
                // $query->Where(['organization_id' => Auth::user()->parent_id, 'is_showing_for' => $showing]);
            }
            $unread = $query2->where('is_read', 0)->count();
            // echo $notification['count'];exit;
            if (!empty($notification)) {
                return response()->json(['status' => true, 'message' => 'Notifications get Successfully', 'data' => $notification, 'unread' => $unread], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Notification not available!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function updateNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'signeeId' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $requestData = $request->all();

            $requestData['organization_id'] = $this->userId;
            if ($requestData['notification_id'] == 'All') {
                if (Auth::user()->role !== 'SIGNEE') {
                    Notification::where(['organization_id' => Auth::user()->id, 'is_showing_for' => 'ORGANIZATION'])->update([
                        'is_sent' =>  1,
                        'is_read' =>  1,
                    ]);
                } else {
                    Notification::where(['signee_id' => $this->userId, 'is_showing_for' => 'SIGNEE', 'organization_id' => (Auth::user()->parent_id == null ? Auth::user()->id : Auth::user()->parent_id)])->update([
                        'is_sent' =>  1,
                        'is_read' =>  1,
                    ]);
                }

                return response()->json(['status' => true, 'message' => 'Notifications clear successfully'], $this->successStatus);
            } else {
                $notification = Notification::where('id', $requestData['notification_id'])->first();
                // $res = Notification::find($notification['id']);
                $notification->is_sent = 1;
                $notification->is_read = 1;
                $update = $notification->update();
                if ($update) {
                    return response()->json(['status' => true, 'message' => 'Notifications Update Successfully'], $this->successStatus);
                } else {
                    return response()->json(['message' => 'Sorry, Notification Not Updated!', 'status' => false], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function getAppliedShift()
    {
        try {
            $booking = new Booking();
            $getAppliedShift = $booking->getAppliedShift();
            if ($getAppliedShift) {
                return response()->json(['status' => true, 'message' => 'Applied Shifts Get Successfully', 'data' => $getAppliedShift], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Something Went Wrong!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function getCompletedShift()
    {
        try {
            $booking = new Booking();
            $completedShifts = $booking->getCompletedShift();
            if ($completedShifts) {
                return response()->json(['status' => true, 'message' => 'Completed Shifts Get Successfully', 'data' => $completedShifts], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Something Went Wrong!', 'status' => false], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }
}
