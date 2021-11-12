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
        try {
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





    //////////////////// Signee CRUD By Organisation ///////////////////////

    /*
     * add new signee by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function addSignee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|unique:users',
            "first_name" => 'required',
            "last_name" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        // print_r($requestData);exit();
        // if ($request->hasFile('cv')) {
        //     $files1 = $request->file('cv');
        //     $name = time() . '_signee_' . $files1->getClientOriginalName();
        //     $files1->move(public_path() . '/uploads/signee_docs/', $name);
        //     $requestData['cv'] = $name;
        // }
        try {
            $requestData['password'] = Hash::make($request->post('password'));
            $requestData['parent_id'] = $this->userId;
            $requestData['role'] = 'SIGNEE';
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
                    return response()->json(['status' => true, 'message' => 'Signee added Successfully', 'data' => $userCreated], $this->successStatus);
                }
            } else {
                return response()->json(['message' => 'Sorry, Signee added failed!', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * View signee by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function viewSignee(Request $request)    //get all signee by organisation
    {
        //print_r($this->userId);exit();
        $UserObj = new User();
        $user = $UserObj->getSignee($request, $this->userId);
        //print_r($user);exit();
        if (!empty($user)) {
            return response()->json(['status' => true, 'message' => 'Signee Get Successfully', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Something is Wrong!', 'status' => false], 409);
        }
    }

    /*
     * Edit signee by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function editSignee(Request $request)
    {
        $requestData = $request->all();
        //print_r($requestData);exit();
        $validator = Validator::make($request->all(), [
            "first_name" => 'required',
            "last_name" => 'required',
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
            //print_r($signee->parent_id);exit();
            $signeeUpdated = $signee->update($requestData);
            if ($signeeUpdated) {
                $signeeDetailResult = SigneesDetail::where('user_id', '=', $requestData['id'])->firstOrFail();
                $result = $signeeDetailResult->update($requestData);
                if ($result) {
                    $speciality = new Speciality();
                    $speciality->addOrUpdateSpeciality($requestData['speciality'], $requestData['id'], $signee->parent_id);
                    $user = User::find($this->userId)->SigneesDetail;
                    return response()->json(['status' => true, 'message' => 'Signee update Successfully', 'data' =>  $signee], $this->successStatus);
                }
            } else {
                return response()->json(['message' => 'Sorry, Signee update failed!', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Delete signee by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteSignee($id)
    {
        try {
            $userDelete = User::find($id);
            $delete = SigneeOrganization::where(['user_id' => $id, 'organization_id' => $userDelete['parent_id']])->delete();
            if ($delete) {
                return response()->json(['status' => true, 'message' => 'Signee deleted successfully.'], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'Sorry, Signee not deleted.'], 409);
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
     * Get signee by id by organization
     *
     * @return \Illuminate\Http\Response
     */
    public function getMySigneeById($id)  //get signee by id from org
    {
        try {
            $userObj = new User();
            $user = $userObj->getSigneeById($id);
            if ($user) {
                return response()->json(['status' => true, 'message' => 'Signee get successfully', 'data' => $user], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Signee not available!', 'status' => false], 404);
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
     * Change signee profile status
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
            $data = User::findOrFail($requestData['signee_id']);
            $data->status = $requestData['status'];
            $res = $data->save();
            if (!empty($res)) {
                return response()->json(['status' => true, 'message' => 'Signee profile status changed successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, status not change.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Change booking status by signee and organization/staff
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
                //echo "123";exit;
                //$signee = $objBooking->getMetchByBookingId($requestData['booking_id']);
                if (Auth::user()->role == 'SIGNEE') {
                    // dd($requestData);
                    return $this->cancelShiftBySignee($requestData);
                } else if (Auth::user()->role == 'STAFF' || Auth::user()->role == 'ORGANIZATION') {
                    //print_r(Auth::user()->role);exit();
                    return $this->cancelShiftBYStaffOrOrg($requestData);
                }
            } elseif ($requestData['status'] == 'ACCEPT') {
                // echo "123";exit;
                return $this->acceptShiftBySignee($requestData);
            } else if ($requestData['status'] == 'CONFIRMED') {
                $objBooking = new Booking();
                $matchSignee = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $requestData['signee_id']);
                $booking = booking::findOrFail($requestData['booking_id']);
                $booking['status'] = $requestData['status'];
                $bookingUpdate = $booking->update($requestData);

                $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $requestData['signee_id'], 'booking_id' => $requestData['booking_id']]);
                $objBookingMatch->signee_booking_status = $requestData['status'];
                $objBookingMatch->save();

                $objBooking->sendBookingConfirmEmail($matchSignee);

                if ($objBookingMatch) {
                    return response()->json(['status' => true, 'message' => 'Booking confirmed successfully'], $this->successStatus);
                } else {
                    return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
                }
            } else if ($requestData['status'] == 'OFFER') {
                // print_r('123');exit;
                return $this->offerToSignee($requestData);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function acceptShiftBySignee($requestData)
    {
        try {
            $objBooking = new Booking();
            $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $requestData['signee_id']);
            //print_r($signeeMatch);exit;
            $update = BookingMatch::where(['signee_id' => $this->userId, 'booking_id' => $requestData['booking_id']])->update([
                'signee_booking_status' => $requestData['status']
            ]);

            $mailSent = $objBooking->sendBookingAcceptBySigneeEmail($signeeMatch);
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
        // print_r($requestData);exit;
        $objBooking = new Booking();
        try {
            $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $requestData['signee_id']);
            BookingMatch::where(['signee_id' => $this->userId, 'booking_id' => $requestData['booking_id']])->update([
                'signee_booking_status' => $requestData['status'], 'booking_cancel_date' => Carbon::now(),
            ]);
            // booking::where(['id' => $requestData['booking_id']])->update(['status' => 'CREATED']);
            $update = $objBooking->sendBookingCancelBySigneeEmail($signeeMatch);

            //send booking open mail to other signees
            //  $signeeList = $objBooking->getMetchByBookingId($requestData['booking_id']);
            //  $sendBookingOpenMail = $objBooking->sendBookingOpenEmail($signeeList);
            if ($update) {
                return response()->json(['status' => true, 'message' => 'Booking cancelled successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function cancelShiftBYStaffOrOrg($requestData)
    {
        $objBooking = new Booking();
        try {
            $signees = $objBooking->getMetchByBookingId($requestData['booking_id']);
            $booking = booking::findOrFail($requestData['booking_id']);
            if ($booking['status'] == 'CREATED') {
                $booking['status'] = $requestData['status'];
                $booking->update();
            } else {
                // dd(array_search($booking['status'], array('CONFIRMED', 'APPLY', 'OFFER'), true));
                if ($booking['status'] == 'CONFIRMED') {
                    $booking['status'] = $requestData['status'];
                    $bookingUpdate = $booking->update();
                    $update = BookingMatch::where(['booking_id' => $requestData['booking_id'], 'signee_id' => $requestData['signee_id']])->update([
                        'signee_booking_status' => $requestData['status'], 'booking_cancel_date' => Carbon::now(),
                    ]);
                    // $update = BookingMatch::where(['signee_id' => $requestData['signee_id'], 'booking_id' => $requestData['booking_id']])->update([
                    //     'signee_booking_status' => $requestData['status'], 'booking_cancel_date' => Carbon::now(),
                    // ]);
                    $objBooking->sendBookingCancelByStaffEmail($signees);
                }
            }
            if ($booking) {
                return response()->json(['status' => true, 'message' => 'Booking canceled successfully'], $this->successStatus);
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
            $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($postData['booking_id'], $postData['signee_id']);
            $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $postData['signee_id'], 'booking_id' => $postData['booking_id']]);
            $objBookingMatch->signee_booking_status = $postData['status'];

            $mailSent = $objBooking->sendOfferToSigneeEmail($signeeMatch);
            $objBookingMatch->save();
            if ($objBookingMatch) {
                return response()->json(['status' => true, 'message' => 'Offer successfully send to candidate'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /*
     * Change signee document status
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
            $signeeDocs = SigneeDocument::where(['signee_id' => $requestData['signee_id'], 'organization_id' => $requestData['organization_id'], 'key' => $requestData['key']])->get()->toArray();
            $idArrray = array_column($signeeDocs, 'id');
            $update = SigneeDocument::whereIn('id', $idArrray)->update(array('document_status' => $requestData['document_status']));
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
                $result['title'] = 'Signee Details';
                $result['date'] = date('m/d/Y');
                //print_r($result);exit();
                $pdf = App::make('dompdf.wrapper');
                // load from other pages use object or array by comma like (pdf-view,$user)
                $pdf->loadView('signee', $result);
                // return $pdf->stream();
                $filePath = public_path() . '/uploads/signee_pdf/';
                $time = date('Ymdhms');
                $file = $filePath . "$time-signee.pdf";
                file_put_contents($file, $pdf->output());
                $data['pdf_path'] = $downloadPath . "$time-signee.pdf";
                return response()->download($file,  "$time-signee.pdf");
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
     * Invite signee for shift
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
            // print_r($requestData);exit();
            $objBooking = new Booking();
            $result = $objBooking->getSigneeForInvite($requestData);
            $res = $objBooking->sendBookingInvitationMail($result);
            if ($res) {
                return response()->json(['status' => true, 'message' => 'Signee Invited Successfully for the shift'], $this->successStatus);
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
                if ($bookingMatch) {
                    return response()->json(['status' => true, 'message' => 'Payment status changed successfully'], $this->successStatus);
                } else {
                    return response()->json(['message' => 'Sorry, something went wrong.', 'status' => false], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function getAllNotifications(Request $request)
    {
        $requestData = $request->all();
        try{
            $notifications = Notification::where('signee_id', $requestData['signee_id'])->orderBy('id','DESC')->get();
            // $res=$notifications->get();
           // print_r($notifications);exit;
            if ($notifications) {
                return response()->json(['status' => true, 'message' => 'Notifications get Successfully', 'data' => $notifications], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Notification not available!', 'status' => false], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function updateNotifications(Request $request)
    {
        // dd($this->userId);
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            //'signeeId' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try{
            $requestData = $request->all();
            $requestData['organization_id'] = $this->userId;
            $notification = Notification::where('id', $requestData['notification_id'])->first();
            // $res = Notification::find($notification['id']);
            $notification->is_read = $requestData['is_read'];
            $update = $notification->update();
            if($update)
            {
                return response()->json(['status' => true, 'message' => 'Notifications Update Successfully'], $this->successStatus);
            }
            else {
                return response()->json(['message' => 'Sorry, Notification Not Updated!', 'status' => false], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function getAppliedShift()
    {
        try{
            $booking = new Booking();
            $getAppliedShift = $booking->getAppliedShift();
            if($getAppliedShift)
            {
                return response()->json(['status' => true, 'message' => 'Applied Shifts Get Successfully', 'data'=>$getAppliedShift], $this->successStatus);
            } else{
                return response()->json(['message' => 'Sorry, Something Went Wrong!', 'status' => false], 404);
            }
        } catch(\Exception $e)
        {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }
    public function getCompletedShift()
    {
        echo "test";
    }
}
