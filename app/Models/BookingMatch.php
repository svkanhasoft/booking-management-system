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
    use SoftDeletes;
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
    protected $fillable = ['organization_id', 'signee_id', 'booking_id', 'trust_id', 'match_count', 'booking_date', 'booking_status', 'shift_id', 'signee_status'];

    public function addBookingMatch($bookingArray, $bookingId)
    {
        //print_r($bookingArray);exit();
        $signeeidArray = array_column($bookingArray, 'signeeId');
        $objBookingMatchDelete = BookingMatch::where('booking_id', '=', $bookingId)->whereNotIn('signee_id', $signeeidArray)->delete();
        foreach ($bookingArray as $keys => $values) {
            //print_r($values);
            // exit;
            //$this->sendMatchEmail($values);
            $objBookingMatch = BookingMatch::where([
                'organization_id' => $values['organization_id'],
                'signee_id' =>  $values['signeeId'],
                'booking_id' => $values['booking_id'],
                'trust_id' => $values['trust_id'],
            ])->firstOrNew();
            $objBookingMatch->organization_id = $values['organization_id'];
            $objBookingMatch->signee_id = $values['signeeId'];
            $objBookingMatch->booking_id = $values['booking_id'];
            $objBookingMatch->trust_id = $values['trust_id'];
            $objBookingMatch->match_count = $values['signeeBookingCount'];
            $objBookingMatch->booking_date = $values['date'];
            $objBookingMatch->shift_id = $values['shift_id'];
            $objBookingMatch->booking_status = 'CREATED';
            $objBookingMatch->save();
            $objBookingMatch = '';
        }
        return true;
    }

    public function editBookingMatchByUser($bookingArray, $bookingId)
    {
        //print_r($bookingArray);exit();
        $signeeidArray = array_column($bookingArray, 'signeeId');
        $objBookingMatchDelete = BookingMatch::where('booking_id', '=', $bookingId)->whereNotIn('signee_id', $signeeidArray)->delete();
        foreach ($bookingArray as $keys => $values) {
            // print_r($values);
            // echo "";exit();
            // exit;
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
            $objBookingMatch->booking_status = 'CREATED';
            $objBookingMatch->save();
            $objBookingMatch = '';
        }
        return true;
    }

    public function editBookingMatchBySignee($bookingArray, $signeeId)
    {
        //print_r($bookingArray);exit();
        $bookingIdArray = array_column($bookingArray, 'booking_id');
        $objBookingMatchDelete = BookingMatch::where('signee_id', '=', $signeeId)->whereNotIn('booking_id', $bookingIdArray)->delete();
        foreach ($bookingArray as $keys => $values) {
            // print_r($values);
            // echo "";exit();
            // exit;
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
            $objBookingMatch->booking_status = 'CREATED';
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
                'subject' => 'Booking Management System: Your match found',
                'data' => $result
            ];
            $emailRes = \Mail::to($result['email'])
                // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
            ->cc('maulik.kanhasoft@gmail.com')
            ->bcc('suresh.kanhasoft@gmail.com')
            ->send(new \App\Mail\SendSmtpMail($details));
            return true;
        } else {
            return false;
        }
    }
    public function getShiftList()
    {
        $staff = User::select('id')->where('parent_id', Auth::user()->parent_id)->get()->toArray();
        $staffIdArray = array_column($staff, 'id');
        $staffIdArray[] = Auth::user()->parent_id;
        //print_r($staffIdArray);exit();
        $perPage = Config::get('constants.pagination.perPage');
        $booking = Booking::select(
            'bookings.*',
            // 'bookings.id',
            // 'bookings.date',
            // 'bookings.start_time',
            // 'bookings.end_time',
            //'bookings.rate',
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
            DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $booking->leftJoin('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $booking->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $booking->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $booking->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $booking->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $booking->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        $booking->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');

        $booking->Join('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        // $booking->Join('signee_organization',  'signee_organization.user_id', '=', 'booking_matches.signee_id');
        // $booking->Join('signee_organization', 'signee_organization.user_id', '=', 'booking_matches.signee_id')->on('signee_organization.organization_id', '=', 'booking_matches.organization_id');
        $booking->join('signee_organization', function($join)
        {
            $join->on('signee_organization.user_id', '=', 'booking_matches.signee_id');
            $join->on('signee_organization.organization_id', '=', 'booking_matches.organization_id');
        });
        $booking->Join('users',  'users.id', '=', 'booking_matches.signee_id');

        $booking->where('bookings.status', 'CREATED');
        $booking->where('bookings.date', '>=', date('y-m-d'));
        $booking->whereIn('bookings.user_id', $staffIdArray);

        $booking->whereNull('bookings.deleted_at');
        $booking->whereNull('booking_specialities.deleted_at');
        $booking->groupBy('bookings.id');
        $booking->orderBy('bookings.date');
        $res = $booking->get();
        $res = $booking->latest('bookings.created_at')->paginate($perPage);
        return $res;
    }

    public function viewShiftDetails($id = null)
    {
        $booking = Booking::select(
            'bookings.*',
            // 'bookings.id',
            // 'bookings.date',
            // 'bookings.start_time',
            // 'bookings.end_time',
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
            'booking_matches.signee_status',
            'signee_organization.status as compliance_status',
            DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
            //'bookings.rate',
        );
        $booking->Join('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $booking->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $booking->Join('trusts',  'trusts.id', '=', 'bookings.trust_id');

        $booking->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $booking->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $booking->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        $booking->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $booking->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $booking->Join('signee_organization',  'signee_organization.user_id', '=', 'booking_matches.signee_id');
        $booking->Join('users',  'users.id', '=', 'booking_matches.signee_id');
        $booking->where('bookings.id', $id);
        $booking->whereNull('booking_specialities.deleted_at');
        $booking->groupBy('bookings.id');
        $booking->groupBy('specialities.id');
        $res = $booking->first()->toArray();
        return $res;
    }

    public function getFilterBookings($request,$userId)
    {
        //echo $userId;exit();
        $requestData = $request->all();

        // $dayArray = $requestData['day'];
        // $hospitalIdArray = $requestData['hospital_id'];
        // $specialityIdArray = $requestData['speciality_id'];

        //print_r($hospitalIdArray);exit();
        //print_r(gettype($requestData));exit();
        $perPage = Config::get('constants.pagination.perPage');
        //print_r($parameter);exit();
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
        $booking->where('bookings.status', 'OPEN');
        $booking->where('bookings.date', '>=', date('y-m-d'));
        $booking->whereNull('bookings.deleted_at');
        $booking->whereNull('booking_specialities.deleted_at');
        $booking->whereNull('hospitals.deleted_at');
        $booking->where('users.parent_id', Auth::user()->parent_id);
        $booking->groupBy('bookings.id');
        // $res = $booking->get()->toArray();
        // $res = $booking->toSql();
        // print_r($res);die;
        $res = $booking->latest('bookings.created_at')->paginate($perPage);
        //print_r($res);exit();
        return $res;

        // ---------------------------------------------------------------------------------------------------

        // $perPage = Config::get('constants.pagination.perPage');
        // $booking = Booking::select(
        //     'bookings.*',
        //     'hospitals.hospital_name',
        //     'ward.ward_name',
        //     'ward_type.ward_type',
        //     'shift_type.shift_type',
        //     'trusts.trust_portal_url',
        //     'trusts.address_line_1',
        //     'trusts.address_line_2',
        //     'trusts.city',
        //     'trusts.post_code',
        //     DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        // );
        // $booking->Join('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        // $booking->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        // $booking->Join('trusts',  'trusts.id', '=', 'bookings.trust_id');
        // $booking->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        // $booking->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        // $booking->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        // $booking->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        // $booking->whereIn();
        // $booking->whereNull('bookings.deleted_at');
        // $booking->whereNull('booking_specialities.deleted_at');
        // $booking->orderBy('bookings.date');
        // $booking->groupBy('booking_specialities.booking_id');
        // $res = $booking->get();
        // $res = $booking->latest('bookings.created_at')->paginate($perPage);
        // return $res;
    }

    public function getMyShift($shiftType)
    {
        $staff = User::select('id')->where('parent_id', Auth::user()->parent_id)->get()->toArray();
        $staffIdArray = array_column($staff, 'id');
        $staffIdArray[] = Auth::user()->parent_id;
        //print_r($staffIdArray);exit();
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
            'signee_organization.status as compliance_status',
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
        $booking->join('signee_organization', function($join)
        {
            $join->on('signee_organization.user_id', '=', 'booking_matches.signee_id');
            $join->on('signee_organization.organization_id', '=', 'booking_matches.organization_id');
        });
        $booking->Join('users',  'users.id', '=', 'booking_matches.signee_id');
        $booking->where('bookings.status', 'CREATED');
        if($shiftType == 'past'){
            $booking->where('bookings.date', '<', date('y-m-d'));
        }else{
            $booking->where('bookings.date', '>=', date('y-m-d'));
        }
        $booking->whereIn('bookings.user_id', $staffIdArray);
        $booking->whereNull('bookings.deleted_at');
        $booking->whereNull('booking_specialities.deleted_at');
        $booking->groupBy('bookings.id');
        $booking->orderBy('bookings.date');
        $res = $booking->get();
        $res = $booking->latest('bookings.created_at')->paginate($perPage);
        return $res;
    }

}
