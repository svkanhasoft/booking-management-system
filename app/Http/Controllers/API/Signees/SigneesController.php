<?php

namespace App\Http\Controllers\API\Signees;

use App\Http\Controllers\API\Organization\BookingController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Booking;
use App\Models\SigneesDetail;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SigneeOrganization;
use App\Models\SigneePreferences;
use App\Models\SigneeSpecialitie;
use App\Models\BookingMatch;
use App\Models\CandidateReferredFrom;
use App\Models\Notification;
use App\Models\Speciality;
use App\Models\SigneeDocument;
use Hash;
use Validator;
use Config;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Log;

class SigneesController extends Controller
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
            "address_line_1" => 'required',
            "city" => 'required',
            "postcode" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $requestData = $request->all();
            //print_r($requestData);exit();
            if ($request->hasFile('cv')) {
                $files1 = $request->file('cv');
                $name = time() . '_signee_' . $files1->getClientOriginalName();
                $files1->move(public_path() . '/uploads/signee_docs/', $name);
                $requestData['cv'] = $name;
            }
            if ($request->hasFile('profile_pic')) {
                $files1 = $request->file('profile_pic');
                $name2 = time() . '_signee_' . $files1->getClientOriginalName();
                $files1->move(public_path() . '/uploads/signee_docs/', $name2);
                $requestData['profile_pic'] = $name2;
            }

            $requestData['password'] = Hash::make($request->post('password'));
            $requestData['parent_id'] = $request->post('organization_id');
            $requestData['role'] = 'SIGNEE';
            $userCreated = User::create($requestData);
            if ($userCreated) {
                $requestData['user_id'] = $userCreated['id'];
                $orgResult = SigneesDetail::create($requestData);

                $objSpeciality = new SigneeSpecialitie();
                $objSpeciality->updateSpeciality($requestData['speciality'], $userCreated['id'], $requestData['parent_id'], false);

                $requestData['organization_id'] = $request->post('organization_id');
                $requestData['user_id'] = $userCreated['id'];
                $sing = SigneeOrganization::create($requestData);
                if ($orgResult) {
                    $UserObj = new User();
                    $mailRes =  $UserObj->sendRegisterEmail($request);
                    $this->addsigneeMatch($userCreated['id']);
                    return response()->json(['status' => true, 'message' => 'User added Successfully', 'data' => $userCreated], $this->successStatus);
                }
            } else {
                return response()->json(['message' => 'Sorry, User added failed!', 'status' => false], 200);
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
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            "email" => 'required',
            "password" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 400);
        }

        $checkRecord = User::where('email', $request->all('email'))->where('role', 'SIGNEE')->first();
        // dd($checkRecord->id);
        if (empty($checkRecord)) {
            return response()->json(['message' => "Sorry, your account does't exists", 'status' => false], 400);
        }
        if ($checkRecord->status != 'Active') {
            return response()->json(['message' => 'Sorry, Your account is Inactive, contact to organization admin', 'status' => false], 200);
        }
        // dd($checkRecord->id, $request->organization_id);
        $checkRecordOrg = User::where('id', $request->organization_id)->first();
        if ($checkRecordOrg->status != 'Active') {
            return response()->json(['message' => 'Sorry, Your organization is inactive, contact to organization admin', 'status' => false], 200);
        }
        $orgResult = SigneeOrganization::where(['user_id' => $checkRecord->id, 'organization_id' => $request->organization_id])->first();
        //print_r($orgResult);exit();
        if (empty($orgResult)) {
            return response()->json(['message' => 'Your account does not exist with a selected organization!', 'status' => false], 400);
        }
        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'role' => 'SIGNEE'])) {
            $checkRecord->parent_id =  request('organization_id');
            $checkRecord->last_login_date =  date('Y-m-d H:i:s');
            $checkRecord->device_id =  !empty($request->header('deviceId')) ? $request->header('deviceId') : '';
            $checkRecord->platform =  !empty($request->header('platform')) ? $request->header('platform') : 'Android';
            $checkRecord->save();


            $userResult = Auth::user();
            $this->userId = Auth::user()->id;
            $this->organizationId = Auth::user()->parent_id;
            $userObj = new User();
            //print_r(Auth::user()->id);exit();
            $user = $userObj->getSigneeDetails(Auth::user()->id, $checkRecord->parent_id);
            // $user['is_password_change'] =  ($user['is_password_change']==1)?true:false;
            $user['token'] =  $userResult->createToken('User')->accessToken;
            return response()->json(['status' => true, 'message' => 'Login Successfully done', 'data' => $user], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Email or password does not match', 'status' => false], 200);
        }
    }

    /**
     * Get User details
     *
     * @return \Illuminate\Http\Response
     */
    public function getDetails()
    {
        //print_r($this->userId);exit();
        $userObj = new User();
        $user = $userObj->getSigneeDetails($this->userId, Auth::user()->parent_id);
        if (!empty($user)) {
            return response()->json(['status' => true, 'message' => 'Signee details get successfully.', 'data' => $user], $this->successStatus);
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
            'old_password' => 'required',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        if (!(Hash::check($request->old_password, Auth::user()->password))) {
            return response()->json(['status' => false, 'message' => "Your old password can't be match"], 200);
        }
        $user = User::where('role', 'SIGNEE')->where('id', $this->userId)->first();
        //print_r($user);exit();
        if (!empty($user)) {
            $userObj = User::find($this->userId);
            // print_r($userObj);exit();
            $userObj['password'] = Hash::make($request->post('password'));
            $userObj['password_change'] = true;
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
            "first_name" => 'required',
            "last_name" => 'required',
            "contact_number" => 'required',
            "address_line_1" => 'required',
            "city" => 'required',
            "postcode" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
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
        $userObj = new User();
        $mailRes =  $userObj->sendForgotEmail($request);
        if ($mailRes) {
            return response()->json(['message' => 'Please check your email and change your password', 'status' => true], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Invalid Email address.', 'status' => false], 200);
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
     * delete Password
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
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
     * Candidate Referred From
     *
     * @return \Illuminate\Http\Response
     */
    public function getCandidateReferredFrom(Request $request)
    {
        $candidateReferredFromObj = CandidateReferredFrom::all();

        if ($candidateReferredFromObj) {
            return response()->json(['status' => true, 'message' => 'Candidate Referred From get successfully', 'data' => $candidateReferredFromObj], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Candidate Referred From.', 'status' => false], 200);
        }
    }

    /**
     * This function is used for get list of all the organizations added by super admin and it is used when new signee registeres.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function getOrganisation()   //while register signee
    {
        $data = [];
        $query = User::select(
            "users.*",
            'org.organization_name'
        );
        $query->join('organizations as org', 'org.user_id', '=', 'users.id');
        $query->where('users.role', '=', 'ORGANIZATION');
        $data = $query->get()->toArray();
        // print_r($data);exit();
        foreach ($data as $key => $value) {
            $orgSpeciality = Speciality::select(
                'id',
                'speciality_name'
            );
            $orgSpeciality->where('user_id', $value['id']);
            $res = $orgSpeciality->get()->toArray();
            $data[$key]['speciality'] = $res;
        }

        if ($data) {
            return response()->json(['status' => true, 'message' => 'Organizations listed successfully', 'data' => $data], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, organizations not available.', 'status' => false], 200);
        }
    }

    /**
     * This function is used for get organization list in which signee wants to register except signee already register.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function getOrganisationListAddOrg()  //while signee add multiple org
    {
        $sigOrg = SigneeOrganization::where('user_id', $this->userId)->get()->toArray();
        $orgId = array_column($sigOrg, 'organization_id');
        // print_r($orgId);exit();
        $query = User::select(
            "users.*",
            'org.organization_name'
        );
        $query->join('organizations as org', 'org.user_id', '=', 'users.id');
        $query->where('users.role', '=', 'ORGANIZATION');
        $query->whereNotIn('users.id', $orgId);
        $res = $query->get()->toArray();
        //print_r($res);exit();
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Organizations listed successfully', 'data' => $res], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, organizations not available.', 'status' => false], 200);
        }
    }


    /**
     * This function is used to get specialities of particular organization.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function getOrgSpecialities($id)
    {
        $speciality = Speciality::select('id', 'speciality_name')->where('user_id', $id)->get()->toArray();
        if ($speciality) {
            return response()->json(['status' => true, 'message' => 'Specialities get successfully', 'data' => $speciality], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, No specialities available.', 'status' => false], 200);
        }
    }

    /**
     * This function is used for logging out the logged-in signee.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        // print_r($user);exit;
        try {
            if (Auth::user()) {
                $user->revoke();
                $userDetail = User::findOrFail($user['user_id']);
                $userDetail['device_id'] = NULL;
                $userDetail->save();
                return response()->json([
                    'status' => true,
                    'message' => 'You have Successfully logout',
                ], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'Sorry, logout failed'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' =>  $e->getMessage()], 200);
        }
    }

    /**
     * This function generates unique candidate id.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function getCandidateId(Request $request)
    {
        try {
            $time = [];
            $time['candidate_id'] = date("ymdHis");
            if ($time) {
                return response()->json(['status' => true, 'message' => 'Candidate get successfully', 'data' => $time], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Candidate not available!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * This function is for display shift list created by organization and staff user.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function shiftList(Request $request)
    {
        $bookingMatching = new BookingMatch();
        $result = $bookingMatching->getShiftList($request, $this->userId);
        if ($result) {
            return response()->json(['status' => true, 'message' => 'shift listed successfully', 'data' => $result], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, shift not available.', 'status' => false], 200);
        }
    }

    /**
     * GET SIGNEE LOGIN MY SHIFT LIST.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function myshift()
    {
        $result = [];
        $bookingMatching = new BookingMatch();
        $result['upcoming'] = $bookingMatching->getMyShift('upcoming');
        $result['past'] = $bookingMatching->getMyShift('past');
        $result['apply'] = $bookingMatching->getMyShift('apply');
        //print_r($result);exit();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Your shift listed successfully', 'data' => $result], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Your shift is not available.', 'status' => false], 200);
        }
    }

    /**
     * This function is used for view shift details by signee
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function viewShiftDetails($id)
    {
        $bookingMatch = new BookingMatch();
        $result = $bookingMatch->viewShiftDetails($id);
       // print_r($result);exit();
        $start_time = strtotime($result['start_time']);
        $end_time = strtotime($result['end_time']);
        $diff = gmdate('H:i:s', $end_time - $start_time);
        $result['duration'] = $diff;
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Booking get successfully', 'data' => $result], $this->successStatus);
        } else {
            return response()->json(['message' => 'Something is wrong.', 'status' => false], 200);
        }
    }

    /**
     * This function is used for filter shifts by signee.
     * Signee can filter shifts by day of shfts, hospital wise and speciality wise.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function filterBookings(Request $request)
    {
        $bookingMatch = new BookingMatch();
        $result = $bookingMatch->getFilterBookings($request, $this->userId);
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Booking get successfully', 'data' => $result], $this->successStatus);
        } else {
            return response()->json(['message' => 'Something is wrong.', 'status' => false], 200);
        }
    }

    /**
     * This function is used for change the compliance status of the signee by staff user.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function changeSigneeComplianceStatus(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            //'signeeId' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            //$user = User::where('id', $requestData['signeeId'])->first();
            if (Auth::user()->role == 'ORGANIZATION') {
                $signeeOrg = SigneeOrganization::firstOrNew(['user_id' => $requestData['signeeId'], 'organization_id' => Auth::user()->id]);
            } else {
                $signeeOrg = SigneeOrganization::firstOrNew(['user_id' => $requestData['signeeId'], 'organization_id' => Auth::user()->parent_id]);
            }

            $signeeOrg->status = $requestData['status'];
            $res = $signeeOrg->save();
            $objNotification = new Notification();
            $notification = $objNotification->addNotification($requestData);

            if (!empty($res)) {
                return response()->json(['status' => true, 'message' => 'Signee status changed successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, status not change.', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * This function is used to register a signee to multiple organization at a time.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function addOrg(Request $request)
    {
        $requestData = $request->all();

        try {
            $res = SigneeOrganization::where([
                'organization_id' => $requestData['organization'][0]['organization_id'],
                'user_id' => $this->userId
            ])->count();
            if ($res > 0) {
                return response()->json(['status' => false, 'message' => 'Sorry, you have already register with this organization'], $this->successStatus);
            }

            $requestData['user_id'] = $this->userId;

            $signeeOrg = new SigneeOrganization();
            $signeeOrg->addOrganisation($requestData['organization'], $this->userId, false);

            $objSpeciality = new SigneeSpecialitie();
            $objSpeciality->addSpeciality($requestData['organization'], $this->userId, false);
            return response()->json(['status' => true, 'message' => 'Organisation added Successfully'], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }
    /**
     * This function is for signee to upload his complience documents.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function documentUpload(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($request->all(), [
            // 'passport[]' => 'mimes:jpeg,jpg,png,gif,csv,txt,pdf|max:2048',
            'files[]' => 'mimes:jpg,png,jpeg,pdf,docs|size:10048',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $user = User::where('id', $this->userId)->first();
            if ($request->hasfile('files')) {
                if ($request->file('files')) {
                    $files = $request->file('files');
                    // $size = $request->file('files')->getClientSize();
                    // echo $size;exit();
                    foreach ($files as $key => $file) {
                        $name = $file->getClientOriginalName();
                        $filename = pathinfo($name, PATHINFO_FILENAME);
                        $extension = pathinfo($name, PATHINFO_EXTENSION);
                        $new_filename = $filename . '_' . time() . '.' . $extension;
                        $new_name = preg_replace('/[^A-Za-z0-9\-._]/', '', $new_filename);
                        $file->move(public_path() . '/uploads/signee_docs/', $new_name);
                        $image = new SigneeDocument();
                        $image->signee_id = $this->userId;
                        $image->key = $requestData['key'];
                        $image->file_name = $new_name;
                        $image->organization_id = $user->parent_id;
                        $docUpload = $image->save();
                    }
                    if ($docUpload) {
                        $document = $image->getDocument($image->signee_id, $image->key, $user->parent_id);
                        return response()->json(['status' => true, 'message' => 'Document Uploaded Successfully', 'data' => $document], $this->successStatus);
                    }
                }
            } else {
                return response()->json(['message' => 'No file selected', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * This function is used for signee to update his specialities.
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function updateSpeciality(Request $request, $userId)
    {
        //dd(Auth::user()->parent_id);
        try {
            $requestData = $request->all();
            $objSpeciality = new SigneeSpecialitie();
            $objSpeciality->updateSpeciality($requestData['speciality_id'], $userId, Auth::user()->parent_id, false);

            $bookingArray = new Booking();
            $booking = $bookingArray->editMetchBySigneeId($userId);
            // print_r($booking);exit();

            $objBookingMatch = new BookingMatch();
            $bookingMatch = $objBookingMatch->editBookingMatchBySignee($booking, $userId);
            //print_r($bookingMatch);exit();

            if ($objSpeciality) {
                return response()->json(['status' => true, 'message' => 'Speciality updated successfully'], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Speciality updation failed!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }
    /**
     * This function is used to get the all the specialities of the signee.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSigneeSpeciality()
    {
        $query = SigneeSpecialitie::select(
            // DB::raw('GROUP_CONCAT( specialities.id SEPARATOR ",") AS speciality_id'),
            'specialities.id as speciality_id'
        );
        $query->leftJoin('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');

        $query->where('signee_speciality.user_id', Auth::user()->id);
        $query->whereNull('signee_speciality.deleted_at');
        $res = $query->get()->toArray();

        if ($res) {
            return response()->json(['status' => true, 'message' => 'Speciality get successfully', 'data' => $res], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Speciality getting error!', 'status' => false], 200);
        }
    }

    /**
     * This function is for fetching the list of organization in which the signee is registered.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getEmailOrganisation(Request $request)
    {
        //working
        $data = [];
        $requestData = $request->all();
        $email = $requestData['email'];

        $query = SigneeOrganization::select(
            //"users.*",
            DB::raw('DISTINCT(organization_id)'),
            // 'organization_id',
            'organizations.organization_name'
        );
        $query->leftJoin('users', 'users.id', '=', 'signee_organization.user_id');
        $query->leftJoin('organizations', 'organizations.user_id', '=', 'signee_organization.organization_id');
        $query->where('users.email', $email);
        $data = $query->get()->toArray();
        foreach ($data as $key => $value) {
            $orgSpeciality = Speciality::select(
                'id',
                'speciality_name'
            );
            $orgSpeciality->where('user_id', $value['organization_id']);
            $res = $orgSpeciality->get()->toArray();
            $data[$key]['speciality'] = $res;
        }
        //return $data;
        $count = count($data);
        if ($count == 0) {
            return response()->json(['message' => 'Invalid email address!', 'status' => false], 200);
        } else {
            return response()->json(['status' => true, 'message' => 'Organisation listed successfully', 'data' => $data], $this->successStatus);
        }
    }

     /**
     * This function is for get signee's documents according to the keys or without key.
     * If key is passed then documents are fetched by that key only.
     * If key is not passed then all the documents for that signee will be fetched.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSigneeDocument(Request $request)
    {
        try {
            $key = $request->get('key');
            // $signeeDocument = SigneeDocument::where('key', $key)->get()->toArray();
            $signeeDocument = SigneeDocument::select(
                "id",
                "signee_id",
                "key",
                "file_name",
                "organization_id",
                "document_status",
                DB::raw('date(created_at) as date_added'),
            );
            $signeeDocument->where(['signee_id' => $this->userId, 'organization_id' => Auth::user()->parent_id]);
            if (!empty($key)) {
                $signeeDocument->Where(['key' => $key, 'signee_id' => $this->userId, 'organization_id' => Auth::user()->parent_id]);
            }

            $data = $signeeDocument->get();
            $count =  $data->count();
            if ($count) {
                return response()->json(['status' => true, 'message' => 'Documents get successfully', 'data' => $data], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Documents not found!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * This function is used for delete signee documents.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteDocument($id)
    {
        try {
            $document = SigneeDocument::find($id);
            unlink(public_path() . "/uploads/signee_docs/" . $document->file_name);
            $document = SigneeDocument::where("id", $document->id)->delete();
            if ($document) {
                return response()->json(['status' => true, 'message' => 'Image deleted successfully'], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'Image deleting failed'], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }


    /*
        This function is for signee who will switch to the org which they are already registered.
        Once the signee is logged in to one org then they can switch to an another organization.
    */
    public function multiOrgLogin(Request $request)
    {
        try {
            $organization_id = $request->get('organization_id');
            $signee_id = $this->userId;

            //to check signee is present in org or not
            $data = SigneeOrganization::where(['user_id' => $signee_id, 'organization_id' => $organization_id])->first();
            if (!empty($data)) {
                $signee = User::where('id', $this->userId)->first();

                if (Auth::guard('web')->loginUsingId($signee_id)) {
                    $signee->parent_id = $organization_id;
                    $signee->save();
                    $userResult = Auth::user();
                    $userObj = new User();
                    $user = $userObj->getSigneeDetails($signee_id, $organization_id);
                    $user['token'] =  $userResult->createToken('User')->accessToken;
                    return response()->json(['status' => true, 'message' => 'Organization successfully changed', 'data' => $user], $this->successStatus);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'No user found in selected organization'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * This function is used for signee who will apply for the shift..is is use for signee.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function applyShift(Request $request)
    {
        //print_r(Auth::user()->id);exit;
        $requestData = $request->all();

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'signee_status' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $res =  BookingMatch::where(['signee_id' => $this->userId, 'booking_id' => $requestData['booking_id']])->update([
                'signee_status'=>  $requestData['signee_status'],
                'signee_booking_status'=>  'APPLY',
            ]);
            $objBooking = new Booking();
            $signeeMatch = $objBooking->getMetchByBookingIdAndSigneeId($requestData['booking_id'], $this->userId);
            // $mailSent = $objBooking->sendBookingApplyBySigneeEmail($signeeMatch);

            $orgDetail = User::where('id', $signeeMatch['organization_id'])->first()->toArray();
            $comArray = array_merge($signeeMatch->toArray(), $orgDetail);
            //print_r($comArray);exit;
            $orgMailSent = $objBooking->sendBookingApplyBySigneeEmailToOrg($comArray);
            if ($orgMailSent) {
                return response()->json(['status' => true, 'message' => 'You have successfully applied for this shift'], $this->successStatus);
            } else {
                return response()->json(['status' => false, 'message' => 'Oops, Something went wrong'], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function addsigneeMatch($id)
    {
        // echo $request->get('id');
        // echo "fdfsd" . $id;
        // exit;
        // print_r($_SERVER['HTTP_HOST']);exit;

        // echo "sdas";
        // $url = $_SERVER['HTTP_HOST'] . "/api/signee/add-signee-match/$id";
        // $headers = array();
        // $headers[] = 'Content-Type: application/json';
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "get");
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // $response = curl_exec($ch);
        // if ($response === FALSE) {
        //     die('FCM Send Error: ' . curl_error($ch));
        // }
        // curl_close($ch);
        try {
            $bookingArray = new Booking();
            $booking = $bookingArray->editMetchBySigneeId($id);
// dd($booking);
            $objBookingMatch = new BookingMatch();
            $bookingMatch = $objBookingMatch->editBookingMatchBySignee($booking, $id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }
}
