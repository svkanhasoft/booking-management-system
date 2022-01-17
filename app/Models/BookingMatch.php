<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Config;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class BookingMatch extends Model
{
    // use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'booking_matches';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['organization_id', 'signee_id', 'booking_id', 'trust_id', 'match_count', 'booking_date', 'signee_booking_status', 'shift_id', 'signee_status'];

    public function addBookingMatch($bookingArray, $bookingId)
    {
        //print_r($bookingArray);exit();
        $signeeidArray = array_column($bookingArray, 'signeeId');
        //print_r($signeeidArray);exit;
        $objBookingMatchDelete = BookingMatch::where('booking_id', '=', $bookingId)->whereNotIn('signee_id', $signeeidArray)->delete();
        foreach ($bookingArray as $keys => $values) {
            //print_r($values); exit;
            //$this->sendMatchEmail($values);
            $objBookingMatch = BookingMatch::where([
                'organization_id' => $values['organization_id'],
                'signee_id' =>  $values['signeeId'],
                'booking_id' => $values['booking_id'],
                'trust_id' => $values['trust_id'],
            ])->firstOrNew();
            // $objBookingMatch->organization_id = $values['organization_id'];
            if(Auth::user()->role == 'ORGANIZATION')
            {
                $objBookingMatch->organization_id = $values['organization_id'];
            }else{
                $objBookingMatch->organization_id = Auth::user()->parent_id;
            }

            $objBookingMatch->signee_id = $values['signeeId'];
            $objBookingMatch->booking_id = $values['booking_id'];
            $objBookingMatch->trust_id = $values['trust_id'];
            $objBookingMatch->match_count = $values['signeeBookingCount'];
            $objBookingMatch->booking_date = $values['date'];
            $objBookingMatch->shift_id = $values['shift_id'];
            // $objBookingMatch->signee_booking_status = 'PENDING';
            $objBookingMatch->save();
            //print_r($objBookingMatch);exit;
            //$objBookingMatch = '';

            $objNotification = new Notification();
            if($values['updated_by'] == NULL)
            {
                $notification = $objNotification->addNotificationV2($values, 'shift_create');
            } else {
                $notification = $objNotification->addNotificationV2($values, 'shift_edit');
            }

            // $objBooking = new Booking();
            // $org = User::select(
            //     'id', 'status as org_status', 'email as org_email', 'first_name', 'last_name', 'role as org_role'
            // );
            // $org->where('id', $values['organization_id']);
            // $orgDetail = $org->first()->toArray();
            // $comArray = array_merge($values, $orgDetail);
            // $orgMailSent = $objBooking->sendBookingCreatedEmailToOrg($comArray);
        }
        return true;
    }

    public function editBookingMatchByUser($bookingArray, $bookingId)
    {
        //print_r($bookingArray);exit();
        $signeeidArray = array_column($bookingArray, 'signeeId');
        $objBookingMatchDelete = BookingMatch::where('booking_id', '=', $bookingId)->whereNotIn('signee_id', $signeeidArray)->delete();
        foreach ($bookingArray as $keys => $values) {
            $objBookingMatch = BookingMatch::where([
                'organization_id' => $values['organization_id'], 'signee_id' =>  $values['signeeId'],
                'booking_id' => $values['booking_id'], 'trust_id' => $values['trust_id'],
            ])->firstOrNew();
            $objBookingMatch->organization_id = $values['organization_id'];
            $objBookingMatch->signee_id = $values['signeeId'];
            $objBookingMatch->booking_id = $values['booking_id'];
            $objBookingMatch->trust_id = $values['trust_id'];
            $objBookingMatch->match_count = $values['signeeBookingCount'];
            $objBookingMatch->booking_date = $values['date'];
            $objBookingMatch->shift_id = $values['shift_id'];
            // $objBookingMatch->signee_booking_status = 'PENDING';
            $objBookingMatch->save();
            $objBookingMatch = '';
            $objNotification = new Notification();
            $notification = $objNotification->addNotification($values);
        }
        return true;
    }

    public function editBookingMatchBySignee($bookingArray, $signeeId)
    {
        //print_r($bookingArray);exit();
        $bookingIdArray = array_column($bookingArray, 'booking_id');
        $objBookingMatchDelete = BookingMatch::where('signee_id', '=', $signeeId)->whereNotIn('booking_id', $bookingIdArray)->delete();
        foreach ($bookingArray as $keys => $values) {
            // print_r($values);exit;
            $objBookingMatch = BookingMatch::where([
                'organization_id' => $values['organization_id'], 'signee_id' =>  $values['signeeId'],
                'booking_id' => $values['booking_id'], 'trust_id' => $values['trust_id'],
            ])->firstOrNew();
            $objBookingMatch->organization_id = $values['organization_id'];
            $objBookingMatch->signee_id = $values['signeeId'];
            $objBookingMatch->booking_id = $values['booking_id'];
            $objBookingMatch->trust_id = $values['trust_id'];
            $objBookingMatch->match_count = $values['signeeBookingCount'];
            $objBookingMatch->booking_date = $values['date'];
            $objBookingMatch->shift_id = $values['shift_id'];
            // $objBookingMatch->signee_booking_status = 'PENDING';
            $objBookingMatch->save();
            $objBookingMatch = '';
        }
        return true;
    }
    public function sendMatchEmail($result)
    {
        // print_r($result);exit();
        if (isset($result) && !empty($result)) {
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'addMatch',
                'subject' => 'Booking Management System: Review your match',
                'data' => $result
            ];
            // $emailRes = \Mail::to($result['email'])->send(new \App\Mail\SendSmtpMail($details));
            return true;
        } else {
            return false;
        }
    }

    public function getShiftList($request, $userId)
    {
        //print_r(Auth::user()->id);exit;
        $staff = User::select('id')->where('parent_id', Auth::user()->parent_id)->get()->toArray();
        $staffIdArray = array_column($staff, 'id');
        $staffIdArray[] = Auth::user()->parent_id;
        $requestData = $request->all();
        //print_r($staffIdArray);exit();
        $perPage = Config::get('constants.pagination.perPage');
        $booking = Booking::select(
            'bookings.*',
            'bookings.id as bid',
            // 'bookings.date',
            // 'bookings.start_time',
            // 'bookings.end_time',
            // 'bookings.rate',
            //'specialities.speciality_name',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
            'shift_type.shift_type',
            'trusts.trust_portal_url',
            'trusts.address_line_1',
            'trusts.address_line_2',
            'trusts.city',
            'trusts.post_code',
            'users.status as profile_status',
            'signee_organization.status as compliance_status',
            'signee_organization.organization_id as signee_organization_organization_Id',
            'signee_organization.id as signee_organization_Id',
            DB::raw('GROUP_CONCAT(DISTINCT specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
            // DB::raw('GROUP_CONCAT(DISTINCT signee_speciality.id SEPARATOR ", ") AS saaaaa'),
        );
        $booking->leftJoin('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        // $booking->leftJoin('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
        $booking->leftJoin('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');

        // $booking->whereRaw("(
        //     (bookings.id = booking_specialities.booking_id AND  or `booking_specialities`.`deleted_at` = null),'')
        //      and
        //      (signee_speciality.speciality_id = booking_specialities.speciality_id AND  or `booking_specialities`.`deleted_at` = null),'')
        //      and
        //      (specialities.id = booking_specialities.speciality_id AND  or `specialities`.`deleted_at` = null),'')
        // )");

        $booking->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $booking->leftJoin('hospitals', 'hospitals.id', '=', 'bookings.hospital_id');
        $booking->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $booking->leftJoin('ward_type', 'ward_type.id', '=', 'ward.ward_type_id');
        $booking->leftJoin('shift_type', 'shift_type.id', '=', 'bookings.shift_type_id');
        $booking->leftJoin('users',  'users.parent_id', '=', 'bookings.user_id');
        $booking->leftJoin('booking_matches', 'booking_matches.booking_id', '=', 'bookings.id');
        //$booking->leftJoin('signee_organization', 'signee_organization.organization_id', '=', 'users.parent_id');
        // $booking->leftJoin('signee_organization', function ($join) {
        //     $join->on('signee_organization.user_id', '=', 'users.id');
        //     $join->on('signee_organization.organization_id', '=', 'users.parent_id');
        //     //$join->orOn('users as staff',  'staff.id', '=', 'bookings.user_id');
        //     //$join->orOn('signee_organization as signee_org',  'signee_org.organization_id', '=', 'staff.parent_id');
        // });
        $booking->leftJoin('signee_organization', function ($join) {
            $join->on('signee_organization.user_id', '=', 'users.id');
            $join->on('signee_organization.organization_id', '=', 'users.parent_id');
            //$join->on('bookings.user_id', '=', 'users.parent_id');
            //$join->on('users.parent_id', '=', 'signee_organization.organization_id');
            //$join->orOn('users as staff',  'staff.id', '=', 'bookings.user_id');
            //$join->orOn('signee_organization as signee_org',  'signee_org.organization_id', '=', 'staff.parent_id');
        });

        $booking->where('bookings.status', 'CREATED');
        $booking->where('users.id', Auth::user()->id);
        $booking->where('bookings.date', '>=', date('y-m-d'));
        if (!empty($requestData['day'])) {
            $booking->whereIn(DB::raw('DAYOFWEEK(date)'), $requestData['day']);
            $booking->where('users.parent_id', Auth::user()->parent_id);
        }
        if (!empty($requestData['speciality_id'])) {
            $booking->whereIn('booking_specialities.speciality_id', $requestData['speciality_id']);
            $booking->where('users.parent_id', Auth::user()->parent_id);
        }
        if (!empty($requestData['hospital_id'])) {
            $booking->whereIn('bookings.hospital_id', $requestData['hospital_id']);
            $booking->where('users.parent_id', Auth::user()->parent_id);
        }

        $booking->whereIn('bookings.user_id', $staffIdArray);
        // $booking->whereIn('specialities.user_id',  $staffIdArray);
        // $booking->where('specialities.user_id', Auth::user()->parent_id);
        // $booking->whereNull('booking_matches.deleted_at');
        $booking->whereNull('bookings.deleted_at');
        // $booking->whereNull('signee_speciality.deleted_at');
        $booking->whereNull('booking_specialities.deleted_at');
        $booking->groupBy('bookings.id');
        $booking->orderBy('bookings.date');
        // $res = $booking->toSql();
        // print_r($res);exit;
        $res = $booking->latest('bookings.created_at')->paginate($perPage);
        foreach ($res as $keys => $values) {
            $res[$keys]['booking_record_perm_for_signees'] = $this->managePermission($values['compliance_status'],$values['profile_status']);
        }
        return $res;
    }
    public function viewShiftDetails($id = null)
    {
        //print_r(Auth::user()->id);exit;
        $booking = Booking::select(
            'bookings.*',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
            'shift_type.shift_type',
            'trusts.trust_portal_url',
            'trusts.address_line_1',
            'trusts.address_line_2',
            'trusts.city',
            'trusts.post_code',
            'users.status as profile_status',
            'users.email',
            'booking_matches.signee_status',
            'booking_matches.signee_booking_status',
            'signee_organization.status as compliance_status',
            'signee_organization.user_id as signeeid',
            'signee_organization.organization_id as orgid',
            'signee_organization.id as signee_organization_id',
            'booking_matches.id as booking_matched_id',
            DB::raw('GROUP_CONCAT( DISTINCT specialities.id SEPARATOR ", ") AS speciality_id'),
            DB::raw('GROUP_CONCAT( DISTINCT specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
            'bookings.rate',
        );

        $booking->Join('signee_organization',  'signee_organization.organization_id', '=', 'bookings.user_id');
        $booking->Join('users',  'users.id', '=', 'signee_organization.user_id');
        $booking->leftJoin('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $booking->leftJoin('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $booking->Join('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $booking->Join('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $booking->Join('ward',  'ward.id', '=', 'bookings.ward_id');
        $booking->Join('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        $booking->Join('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $booking->leftJoin('booking_matches', function($join)
        {
            $join->on('booking_matches.booking_id', '=', 'bookings.id');
            $join->on('booking_matches.signee_id','=', 'users.id');
        });
        // $booking->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        // $booking->leftJoin('booking_matches as signeetable',  'booking_matches.signee_id', '=', 'users.id');

        $booking->where('bookings.id', $id);
        $booking->where('users.id', Auth::user()->id);
        $booking->whereNull('booking_specialities.deleted_at');
        // $booking->groupBy('specialities.id'); // 15 Desc shailesh
        //$booking->groupBy('bookings.id');
        $res = $booking->first();

        if(!empty($res) && $res->id > 0){
            $result = BookingMatch::where('deleted_at')->where('signee_id', '=', $res['signeeid'])->where('booking_id', $id)->first();
            if(!$result){
                $res['signee_booking_status'] = '';
                $res['booking_record_perm_for_signees'] = $this->managePermission('',$res['profile_status']);
            }else{
                $res['booking_record_perm_for_signees'] = $this->managePermission($res['compliance_status'],$res['profile_status']);
            }
            return $res;
        }else{
            return false;
        }
    }


    public function getFilterBookings($request, $userId)
    {
        $requestData = $request->all();
        $perPage = Config::get('constants.pagination.perPage');
        $booking = Booking::select(
            'bookings.*',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
            'shift_type.shift_type',
            'trusts.trust_portal_url',
            'trusts.address_line_1',
            'trusts.address_line_2',
            'trusts.city',
            'trusts.post_code',
            DB::raw('GROUP_CONCAT( distinct(specialities.speciality_name) SEPARATOR ", ") AS speciality_name'),
        );
        //$booking->leftJoin('bookings',  'bookings.id', '=', 'booking_matches.booking_id');
        $booking->leftJoin('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $booking->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $booking->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $booking->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $booking->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $booking->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        $booking->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $booking->leftJoin('users',  'users.parent_id', '=', 'bookings.user_id');
        if (!empty($requestData['day'])) {
            $booking->whereIn(DB::raw('DAYOFWEEK(date)'), $requestData['day']);
            $booking->where('users.parent_id', Auth::user()->parent_id);
        }
        if (!empty($requestData['speciality_id'])) {
            $booking->whereIn('booking_specialities.speciality_id', $requestData['speciality_id']);
            $booking->where('users.parent_id', Auth::user()->parent_id);
        }
        if (!empty($requestData['hospital_id'])) {
            $booking->whereIn('bookings.hospital_id', $requestData['hospital_id']);
            $booking->where('users.parent_id', Auth::user()->parent_id);
        }
        $booking->where('bookings.status', 'CREATED');
        $booking->where('bookings.date', '>=', date('y-m-d'));
        $booking->whereNull('bookings.deleted_at');
        $booking->whereNull('booking_specialities.deleted_at');
        $booking->whereNull('hospitals.deleted_at');
        $booking->where('users.parent_id', Auth::user()->parent_id);
        $booking->groupBy('bookings.id');
        $booking->orderBy('bookings.date');
        $res = $booking->latest('bookings.created_at')->paginate($perPage);
        return $res;
    }

    public function getMyShift($shiftType)
    {
        //print_r(Auth::user());exit;
        $staff = User::select('id')->where('parent_id', Auth::user()->parent_id)->get()->toArray();
        $staffIdArray = array_column($staff, 'id');
        $staffIdArray[] = Auth::user()->parent_id;

        $perPage = Config::get('constants.pagination.perPage');
        $booking = Booking::select(
            'bookings.*',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
            'shift_type.shift_type',
            'trusts.trust_portal_url',
            'trusts.address_line_1',
            'trusts.address_line_2',
            'trusts.city',
            'trusts.post_code',
            'users.status as profile_status',
            'signee_organization.user_id as sid',
            "signee_organization.organization_id as orgid",
            'signee_organization.status as compliance_status',
            'booking_matches.id as bmid',
            DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $booking->Join('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $booking->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $booking->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $booking->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $booking->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $booking->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        $booking->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $booking->Join('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $booking->join('signee_organization', function ($join) {
            $join->on('signee_organization.user_id', '=', 'booking_matches.signee_id');
            $join->on('signee_organization.organization_id', '=', 'booking_matches.organization_id');
        });
        $booking->Join('users',  'users.id', '=', 'booking_matches.signee_id');

        if ($shiftType === 'past') {
            $booking->where('bookings.date', '<', date('Y-m-d'));
            $booking->where('booking_matches.signee_booking_status', 'CONFIRMED');
            $booking->where('bookings.status', 'CONFIRMED');
        }else if ($shiftType === 'apply') {
            $booking->where('booking_matches.signee_booking_status', 'APPLY');
            $booking->where('bookings.date', '>=', date('Y-m-d'));
            $booking->where('bookings.status','<>', 'CANCEL');
        } else if ($shiftType === 'upcoming') {
            // $booking->where('bookings.status', 'CONFIRMED');
            $booking->where('bookings.status','<>', 'CANCEL');
            $booking->where('booking_matches.signee_booking_status', 'CONFIRMED');
            $booking->where('bookings.date', '>=', date('Y-m-d'));
        }else if ($shiftType === 'offer') {
            // $booking->where('bookings.status', 'CONFIRMED');
            $booking->where('booking_matches.signee_booking_status', 'OFFER');
            $booking->where('bookings.date', '>=', date('Y-m-d'));
            $booking->where('bookings.status','<>', 'CANCEL');
        }

        $booking->whereIn('bookings.user_id', $staffIdArray);
        $booking->whereNull('bookings.deleted_at');
        $booking->whereNull('booking_specialities.deleted_at');
        $booking->groupBy('bookings.id');
        $booking->orderBy('bookings.date');
        $booking->where('users.id', Auth::user()->id);
        $res = $booking->get();
        $res = $booking->latest('bookings.created_at')->paginate($perPage);
        foreach ($res as $keys => $values) {
            $res[$keys]['booking_record_perm_for_signees'] = $this->managePermission($values['compliance_status'],$values['profile_status']);
        }
        return $res;
    }

    public function managePermission($compliance_status,$profile_status)
    {
        //echo $compliance_status, $profile_status;exit();
        $booking_record_perm_for_signees = array(
            "view_new_shifts" => false,
            "review_shifts" => false, "book_shifts" => false, 'cancel_shifts' => false
        );
        if ($compliance_status == "NEW SIGNUP" && $profile_status == "Active") {
            $booking_record_perm_for_signees['view_new_shifts'] = true;
        } else if ($compliance_status == "COMPLIANCE REVIEW" && $profile_status == "Active") {
            $booking_record_perm_for_signees['view_new_shifts'] = true;
        }
        else if ($compliance_status == "COMPLIANT" && $profile_status == "Active") {
           // echo "hi";exit;
            $booking_record_perm_for_signees['view_new_shifts'] = true;
            $booking_record_perm_for_signees['review_shifts'] = true;
            $booking_record_perm_for_signees['book_shifts'] = true;
            $booking_record_perm_for_signees['cancel_shifts'] = true;
        } else if ($compliance_status == "NOT COMPLIANT" && $profile_status == "Active") {
            $booking_record_perm_for_signees['view_new_shifts'] = true;
            $booking_record_perm_for_signees['review_shifts'] = true;
        } else if ($compliance_status == "ON HOLD" && $profile_status == "Active") {
            $booking_record_perm_for_signees['view_new_shifts'] = true;
        } else if ($compliance_status == "New Signup" && ($profile_status == "Inactive" || $profile_status == "Dormant")) {
            $booking_record_perm_for_signees['view_new_shifts'] = true;
            $booking_record_perm_for_signees['review_shifts'] = true;
        } else if ($compliance_status == "COMPLIANCE REVIEW" && ($profile_status == "Inactive" || $profile_status == "Dormant")) {
            $booking_record_perm_for_signees['view_new_shifts'] = true;
            $booking_record_perm_for_signees['review_shifts'] = true;
        } else if ($compliance_status == "COMPLIANT" && ($profile_status == "Inactive" || $profile_status == "Dormant")) {
            $booking_record_perm_for_signees['view_new_shifts'] = true;
            $booking_record_perm_for_signees['review_shifts'] = true;
        } else if ($compliance_status == "NOT COMPLIANT" && ($profile_status == "Inactive" || $profile_status == "Dormant")) {
            $booking_record_perm_for_signees['view_new_shifts'] = true;
            $booking_record_perm_for_signees['review_shifts'] = true;
        } else if ($compliance_status == "ON HOLD" && ($profile_status == "Inactive" || $profile_status == "Dormant")) {
            $booking_record_perm_for_signees['view_new_shifts'] = false;
            $booking_record_perm_for_signees['review_shifts'] = false;
            $booking_record_perm_for_signees['book_shifts'] = false;
            $booking_record_perm_for_signees['cancel_shifts'] = false;
        }
        return $booking_record_perm_for_signees;
    }
}
