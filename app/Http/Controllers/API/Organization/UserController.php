<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests;
use App\Models\Booking;
use App\Models\BookingMatch;
use App\Models\User;
use App\Models\OrganizationUserDetail;
use App\Models\SigneesDetail;
use App\Models\SigneeOrganization;

use Hash;
use App\Models\Role;
use App\Models\SigneeSpecialitie;
use App\Models\Speciality;

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
            "contact_number" => 'required',
            "role_id" => 'required',
            "designation_id" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        try{
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
        }
        catch(\Exception $e)
        {
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
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        try
        {
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
        catch(\Exception $e)
        {
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
            return response()->json(['status' => false, 'message' => $error], 422);
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
        try
        {
            $UserObj = new User();
            $user = $UserObj->getOrganizationDetails($this->userId);
            if (!empty($user)) {
                return response()->json(['status' => true, 'message' => 'User details get successfully.', 'data' => $user], $this->successStatus);
            } else {
                return response()->json(['message' => 'something will be wrong', 'status' => false], 409);
            }
        }
        catch(\Exception $e)
        {
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
     * Get User list
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
            return response()->json(['status' => false, 'message' => $error], 422);
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
            return response()->json(['status' => false, 'message' => $error], 422);
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
            return response()->json(['status' => false, 'message' => $error], 422);
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
            return response()->json(['status' => false, 'message' => $error], 422);
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

    public function addSignee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|unique:users',
            "first_name" => 'required',
            "last_name" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 422);
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

    public function viewSignee(Request $request)    //get all signee by organisation
    {
        $UserObj = new User();
        $user = $UserObj->getSignee($request, $this->userId);
        //print_r($user);exit();
        if (!empty($user)) {
            return response()->json(['status' => true, 'message' => 'Signee Get Successfully', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Something is Wrong!', 'status' => false], 409);
        }
    }

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
            return response()->json(['status' => false, 'message' => $error], 422);
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

    public function changeShiftStatus(Request $request)
    {
        //print_r(Auth::user()->role);exit();
        $requestData = $request->all();
        //print_r($requestData);exit();
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'booking_id' => 'required',
            'signee_id' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        try{
            if(Auth::user()->role == 'ORGANIZATION'){
                //$data = SigneeOrganization::firstOrNew(['user_id' => $requestData['signee_id'], 'organization_id' => Auth::user()->id]);
                $booking = Booking::firstOrNew(['id'=>$requestData['booking_id'], 'user_id'=> Auth::user()->id]);
                $booking->status = $requestData['status'];
                $booking->save();
                // $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $requestData['signee_id'], 'booking_id' => $requestData['booking_id'], 'organization_id' => Auth::user()->id]);
                // $objBookingMatch->booking_status = $requestData['status'];
                // $objBookingMatch->save();
            }
            else{
                //$data = SigneeOrganization::firstOrNew(['user_id' => $requestData['signee_id'], 'organization_id' => Auth::user()->parent_id]);
                $booking = Booking::firstOrNew(['id'=>$requestData['booking_id'], 'user_id'=> Auth::user()->parent_id]);
                $booking->status = $requestData['status'];
                $booking->save();
                // $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $requestData['signee_id'], 'booking_id' => $requestData['booking_id'], 'organization_id' => Auth::user()->parent_id]);
                // $objBookingMatch->booking_status = $requestData['status'];
                // $objBookingMatch->save();
            }
            //$booking = Booking::firstOrNew(['id'=>$requestData['booking_id'], 'user_id'=>$this->userId]);
            //print_r($booking);exit();
            if(!empty($booking))
            {
                return response()->json(['status' => true, 'message' => 'Shift status changed successfully', 'data' => $booking], $this->successStatus);
            } 
            else {
                return response()->json(['message' => 'Shift status not changed!', 'status' => false], 404);
            }
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function changeSigneeProfileStatus(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            'signee_id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        try
        {
            $data = User::findOrFail($requestData['signee_id']);
            $data->status = $requestData['status'];
            $res = $data->save();
            if(!empty($res))
            {
                return response()->json(['status' => true, 'message' => 'Signee profile status changed successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, status not change.', 'status' => false], 409);
            }
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function confirmBooking(Request $request)
    {
        $requestData = $request->all();
        
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'signee_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 422);
        }
        try{
            $objBooking = new Booking();
            $matchSignee = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $requestData['signee_id']);
            
            $objBooking->sendBookingConfirmEmail($matchSignee);

            $booking = booking::findOrFail($requestData['booking_id']);
            $booking['status'] = $requestData['status'];
            $bookingUpdate = $booking->update($requestData);

            $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $requestData['signee_id'], 'booking_id' => $requestData['booking_id']]);
            $objBookingMatch->booking_status = $requestData['status'];
            $objBookingMatch->save();
            if($objBookingMatch)
            {
                return response()->json(['status' => true, 'message' => 'Booking confirmed successfully'], $this->successStatus);
            }
            else
            {
                return response()->json(['message' => 'Sorry, something is wrong.', 'status' => false], 409);
            }
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }
}
