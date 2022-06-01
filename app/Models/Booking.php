<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Http\Request;
use Config;
use Auth;
use App\Models\Trust;

class Booking extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bookings';

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
    protected $fillable = ['user_id', 'staff_id', 'hospital_id', 'reference_id', 'trust_id', 'ward_id', 'shift_id', 'shift_type_id', 'date', 'grade_id', 'status', 'payable', 'chargeable', 'start_time', 'end_time', 'created_by', 'updated_by'];
    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

    // public function getBooking($bookingId = null)
    // {
    //     $query = Booking::select(
    //         'bookings.*',
    //         'ward.ward_name',
    //         'trusts.name',
    //         'grade.grade_name',
    //         'hospitals.hospital_name',
    //         'shift_type.shift_type',
    //         'organization_shift.start_time',
    //         'organization_shift.end_time',
    //         'users.email',
    //         'users.postcode',
    //         'users.city',
    //         'users.address_line_1',
    //         'users.address_line_2',
    //         DB::raw('GROUP_CONCAT(signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
    //         DB::raw('GROUP_CONCAT(DISTINCT specialities.speciality_name SEPARATOR "  | ") AS speciality_name'),
    //         DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
    //         DB::raw('CONCAT(users.first_name," ", users.last_name) AS organization_name'),
    //     );
    //     $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
    //     $query->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
    //     $query->leftJoin('organization_shift',  'organization_shift.id', '=', 'bookings.shift_id');
    //     $query->leftJoin('users',  'users.id', '=', 'trusts.user_id');
    //     $query->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
    //     $query->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
    //     $query->leftJoin('grade',  'grade.id', '=', 'bookings.grade_id');

    //     $query->where('bookings.id', $bookingId);
    //     $query->whereNull('signee_speciality.deleted_at');
    //     $query->groupBy('booking_matches.signee_id');
    //     return $query->first();
    // }

    public function getBooking($bookingId = null)
    {
        $query = Booking::select(
            'bookings.*',
            'ward.ward_name',
            'trusts.name',
            'grade.grade_name',
            'hospitals.hospital_name',
            'shift_type.shift_type',
            // 'organization_shift.start_time',
            // 'organization_shift.end_time',
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS organization_name'),
        );
        $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $query->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $query->leftJoin('organization_shift',  'organization_shift.id', '=', 'bookings.shift_id');
        $query->leftJoin('users',  'users.id', '=', 'trusts.user_id');
        $query->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $query->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $query->leftJoin('grade',  'grade.id', '=', 'bookings.grade_id');
        $query->where('bookings.id', $bookingId);
        return $query->first();
    }

    public function getBookingByStatus($status = null)
    {
        $query = Booking::select(
            'bookings.*',
            'ward.ward_name',
            'trusts.name',
            // 'organization_shift.start_time',
            // 'organization_shift.end_time',
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS organization_name'),
        );
        $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $query->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $query->leftJoin('organization_shift',  'organization_shift.id', '=', 'bookings.shift_id');
        $query->leftJoin('users',  'users.id', '=', 'trusts.user_id');
        $query->where('bookings.status', $status);
        return $query->get();
    }

    public function getBookingByFilter(Request $request, $status = null)
    {
        $perPage = Config::get('constants.pagination.perPage');
        $keyword = $request->get('search');
        $status = $request->get('status');
        // echo '<pre>';
        // print_r($status); exit;
        $query = Booking::select(
            'bookings.*',
            'ward.ward_name',
            'trusts.name',
            'grade.grade_name',
            'shift_type.shift_type',
            'hospitals.hospital_name',
            //'organization_shift.start_time',
            //'organization_shift.end_time',
            // 'booking_matches.id as bmid',
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS organization_name'),
        );
        $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $query->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $query->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $query->leftJoin('organization_shift',  'organization_shift.id', '=', 'bookings.shift_id');
        $query->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $query->leftJoin('grade',  'grade.id', '=', 'bookings.grade_id');
        $query->leftJoin('users',  'users.id', '=', 'trusts.user_id');
        //$query->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');


        if (!empty($keyword)) {
            $query->where(function ($query2) use ($status, $keyword) {
                $query2->where('trusts.name', 'LIKE',  "%$keyword%")
                    ->orWhere('ward.ward_name', 'LIKE',  "%$keyword%")
                    ->orWhere('bookings.reference_id', 'LIKE',  "%$keyword%")
                    ->orWhere('bookings.status', 'LIKE',  "%$keyword%");
            });
        }

        //$query->where('bookings.status', $status);
        if ($status == 'CREATED') {
            $query->where('bookings.date', '>=', date('y-m-d'));
            $query->where('bookings.status', 'CREATED');
        } else if ($status == 'CONFIRMED') {
            $query->where('bookings.date', '>=', date('Y-m-d'));
            $query->where('bookings.status', 'CONFIRMED');
        } else if ($status == 'COMPLETED') {
            $query->where('bookings.date', '<', date('Y-m-d'));
            $query->where('bookings.status', 'CONFIRMED');
        } else if ($status == 'CANCEL') {
            $query->where('bookings.status', 'CANCEL');
        }


        // $query->where('bookings.user_id',Auth::user()->id);

        if (Auth::user()->role == 'ORGANIZATION') {
            $staff = User::select('id')->where('parent_id', Auth::user()->id)->get()->toArray();
            $staffIdArray = array_column($staff, 'id');
            $staffIdArray[] = Auth::user()->id;
            $query->whereIn('bookings.user_id', $staffIdArray);
        } else {
            // $query->where('bookings.user_id',Auth::user()->id);
            $query->whereIn('bookings.user_id', array(Auth::user()->id, Auth::user()->parent_id));
        }

        $query->whereNull('bookings.deleted_at');
        $query->orderBy('bookings.id', 'DESC');
        $query->groupBy('bookings.id');
        //$bookingList = $query->get();
        $bookingList = $query->latest()->paginate($perPage);
        //print_r($bookingList);exit;
        return $bookingList;
    }
    public function getStaffBooking(Request $request, $status = null)
    {

        $perPage = Config::get('constants.pagination.perPage');
        $keyword = $request->get('search');
        $status = $request->get('status');
        $query = Booking::select(
            'bookings.*',
            'ward.ward_name',
            'trusts.name',
            'grade.grade_name',
            'shift_type.shift_type',
            // 'organization_shift.start_time',
            // 'organization_shift.end_time',
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS organization_name'),
        );
        $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $query->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $query->leftJoin('organization_shift',  'organization_shift.id', '=', 'bookings.shift_id');
        $query->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $query->leftJoin('grade',  'grade.id', '=', 'bookings.grade_id');
        $query->leftJoin('users',  'users.id', '=', 'trusts.user_id');


        if (!empty($keyword)) {
            $query->where(function ($query2) use ($status, $keyword) {
                $query2->where('trusts.name', 'LIKE',  "%$keyword%")
                    ->orWhere('ward.ward_name', 'LIKE',  "%$keyword%")
                    ->orWhere('bookings.reference_id', 'LIKE',  "%$keyword%")
                    ->orWhere('bookings.status', 'LIKE',  "%$keyword%");
            });
        }

        $query->where('bookings.status', $status);
        $query->whereIn('bookings.user_id', array(Auth::user()->id, Auth::user()->parent_id));
        $query->whereNull('bookings.deleted_at');
        $query->groupBy('bookings.id');
        $bookingList = $query->latest()->paginate($perPage);
        return $bookingList;
    }

    public function getBookingByFilterV2(Request $request, $status = null)
    {
        $perPage = Config::get('constants.pagination.perPage');
        $keyword = $request->get('search');
        $status = $request->get('status');
        $query = Booking::select(
            'bookings.*',
            'ward.ward_name',
            'trusts.name',
            'grade.grade_name',
            'shift_type.shift_type',
            // 'organization_shift.start_time',
            // 'organization_shift.end_time',
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS organization_name'),
        );
        $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $query->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $query->leftJoin('organization_shift',  'organization_shift.id', '=', 'bookings.shift_id');
        $query->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $query->leftJoin('grade',  'grade.id', '=', 'bookings.grade_id');
        $query->leftJoin('users',  'users.id', '=', 'trusts.user_id');


        if (!empty($keyword)) {
            $query->where(function ($query2) use ($status, $keyword) {
                $query2->where('trusts.name', 'LIKE',  "%$keyword%")
                    ->orWhere('ward.ward_name', 'LIKE',  "%$keyword%")
                    ->orWhere('bookings.reference_id', 'LIKE',  "%$keyword%")
                    ->orWhere('bookings.status', 'LIKE',  "%$keyword%");
            });
        }

        $query->where('bookings.status', $status);
        $query->whereNull('bookings.deleted_at');
        $query->groupBy('bookings.id');
        $bookingList = $query->latest()->paginate($perPage);;

        $subArray = [];
        foreach ($bookingList as $key => $booking) {

            $subArray[$key] = $booking;
            $subQuery = BookingSpeciality::select(
                'users.id',
                'booking_specialities.booking_id',
                'specialities.speciality_name',
                'signees_detail.candidate_id',
                'signees_detail.phone_number',
                'signees_detail.mobile_number',
                'users.address_line_1',
                'users.address_line_2',
                'users.city',
                'users.postcode',
                'signees_detail.date_of_birth',
                'signees_detail.nationality',
                'signees_detail.candidate_referred_from',
                'signees_detail.date_registered',
                'signees_detail.nmc_dmc_pin',
                DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
                DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
                DB::raw('GROUP_CONCAT(DISTINCT specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
                DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
            );
            $subQuery->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
            $subQuery->Join('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
            $subQuery->Join('users',  'users.id', '=', 'signee_speciality.user_id');
            $subQuery->Join('signees_detail',  'signees_detail.user_id', '=', 'users.id');

            $subQuery->where('booking_specialities.booking_id', $booking['id']);
            $subQuery->groupBy('users.id');
            $subQuery->orderBy('signeeBookingCount', 'DESC');
            $res = $subQuery->get()->toArray();
            $subArray[$key]['user'] = $res;
        }
        return $subArray;
    }

    public function getMetchByBookingId($matchiId = null)
    {

        $subQuery = Booking::select(
            'users.email',
            'signee_preference.user_id as signeeId',
            'bookings.id as booking_id',
            'users.address_line_1',
            'users.address_line_2',
            'users.city',
            'users.role',
            'users.device_id',
            'users.platform',
            'users.deleted_at as userDelete',
            'bookings.user_id as organization_id',
            'bookings.*',
            // 'shift_type.shift_type',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
            'booking_matches.signee_status',
            'booking_matches.signee_booking_status',
            'booking_matches.id as bookingMatchId',
            DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
            DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
            DB::raw('GROUP_CONCAT(DISTINCT signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
            DB::raw('GROUP_CONCAT( DISTINCT(specialities.speciality_name) SEPARATOR ", ") AS speciality_name'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        );
        // $subQuery->leftJoin('bookings',  'bookings.id', '=', 'booking_matches.booking_id');
        // $subQuery->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id'); // Shailesh
        $subQuery->leftJoin('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $subQuery->leftJoin('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
        $subQuery->leftJoin('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $subQuery->leftJoin('users',  'users.id', '=', 'signee_speciality.user_id');
        $subQuery->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $subQuery->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $subQuery->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        $subQuery->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $subQuery->Join('signee_preference',  'signee_preference.user_id', '=', 'users.id');


        $subQuery->where('users.role', 'SIGNEE');
        $subQuery->where('bookings.id', $matchiId);
        $subQuery->whereNull('signee_speciality.deleted_at');
        $subQuery->whereNull('booking_specialities.deleted_at');
        $subQuery->whereNull('bookings.deleted_at');
        $subQuery->whereNull('users.deleted_at');
        $subQuery->groupBy('signee_preference.user_id');
        $subQuery->orderBy('signeeBookingCount', 'DESC');


        $subQuery->whereRaw("(
            IF(DAYOFWEEK(`bookings`.`date`) = 1, (`signee_preference`.`sunday_day` = 1 or `signee_preference`.`sunday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 2, (`signee_preference`.`monday_day` = 1 or `signee_preference`.`monday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 3, (`signee_preference`.`tuesday_day` = 1 or `signee_preference`.`tuesday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 4, (`signee_preference`.`wednesday_day` = 1 or `signee_preference`.`wednesday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 5, (`signee_preference`.`thursday_day` = 1 or `signee_preference`.`thursday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 6, (`signee_preference`.`friday_day` = 1 or `signee_preference`.`friday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 7, (`signee_preference`.`saturday_day` = 1 or `signee_preference`.`saturday_night` = 1),'')
        )");

        $res = $subQuery->get()->toArray();

        // print_r($res);exit();
        return $res;
    }

    public function getConfirmedCandidates($matchiId = null)
    {
        // print_r($matchiId);exit;
        $subQuery = Booking::select(
            'booking_matches.signee_id as signeeId',
            'booking_matches.signee_booking_status',
            'booking_matches.id as bookingMatchId',
            'bookings.*',
            'bookings.user_id as organization_id',
            'shift_type.shift_type',
            'hospitals.hospital_name',
            'ward.ward_name',
            'users.first_name',
            'users.last_name',
            'users.platform',
            'users.device_id',
            'booking_matches.booking_id',
        );
        $subQuery->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $subQuery->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $subQuery->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $subQuery->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $subQuery->leftJoin('users',  'users.id', '=', 'booking_matches.signee_id');
        $subQuery->where('booking_matches.signee_booking_status', 'CONFIRMED');
        $subQuery->where('bookings.id', $matchiId);
        $subQuery->whereNull('bookings.deleted_at');

        $res = $subQuery->get()->toArray();
        return $res;
    }

    public function getMetchByBookingIdAndSigneeId($bookingId = null, $signeeId = null)
    {
        $subQuery = Booking::select(
            'users.first_name',
            'users.last_name',
            'users.email',
            'signee_preference.user_id as signeeId',
            'bookings.id as booking_id',
            'users.address_line_1',
            'users.address_line_2',
            'users.city',
            'users.role',
            'users.parent_id as signee_org_id',
            'bookings.user_id as organization_id',
            'bookings.*',
            'booking_matches.signee_booking_status',
            'booking_matches.signee_status',
            'shift_type.shift_type',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
            'bookings.date as booking_date',
            DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
            DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
            DB::raw('GROUP_CONCAT(signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
            DB::raw('GROUP_CONCAT( distinct(specialities.speciality_name) SEPARATOR ", ") AS speciality_name'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        );
        //$subQuery->leftJoin('bookings',  'bookings.id', '=', 'booking_matches.booking_id');
        $subQuery->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $subQuery->leftJoin('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $subQuery->leftJoin('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
        $subQuery->leftJoin('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $subQuery->leftJoin('users',  'users.id', '=', 'signee_speciality.user_id');
        $subQuery->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $subQuery->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $subQuery->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        //$subQuery->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $subQuery->leftJoin('booking_matches', function ($join) {
            $join->on('booking_matches.booking_id', '=', 'bookings.id');
            $join->on('booking_matches.signee_id', '=', 'users.id');
        });
        $subQuery->Join('signee_preference',  'signee_preference.user_id', '=', 'users.id');

        $subQuery->where('users.role', 'SIGNEE');
        $subQuery->where('bookings.id', $bookingId);
        $subQuery->where('users.id', $signeeId);
        $subQuery->whereNull('signee_speciality.deleted_at');
        $subQuery->whereNull('booking_specialities.deleted_at');
        $subQuery->whereNull('bookings.deleted_at');
        $subQuery->groupBy('signee_preference.user_id');
        $subQuery->orderBy('signeeBookingCount', 'DESC');
        $subQuery->whereRaw("(
            IF(DAYOFWEEK(`bookings`.`date`) = 1, (`signee_preference`.`sunday_day` = 1 or `signee_preference`.`sunday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 2, (`signee_preference`.`monday_day` = 1 or `signee_preference`.`monday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 3, (`signee_preference`.`tuesday_day` = 1 or `signee_preference`.`tuesday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 4, (`signee_preference`.`wednesday_day` = 1 or `signee_preference`.`wednesday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 5, (`signee_preference`.`thursday_day` = 1 or `signee_preference`.`thursday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 6, (`signee_preference`.`friday_day` = 1 or `signee_preference`.`friday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 7, (`signee_preference`.`saturday_day` = 1 or `signee_preference`.`saturday_night` = 1),'')
        )");
        $res = $subQuery->first();
        //print_r($res);exit();
        return $res;
    }

    //booking confirm email
    public function sendBookingConfirmEmail($result)
    {
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'bookingConfirm',
                'subject' => 'Pluto: Your booking is confirm',
                'data' => $result
            ];
            $emailRes = \Mail::to($result['email'])
                // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
                // ->cc('maulik.kanhasoft@gmail.com')
                // ->bcc('suresh.kanhasoft@gmail.com')
                ->send(new \App\Mail\SendSmtpMail($details));

            //send notification
            $objNotification = new Notification();
            $notification = $objNotification->addNotification($result);
            return true;
        } else {
            return false;
        }
    }
    //booking confirmed by Candidate email to org
    public function sendBookingConfirmedEmailToOrg($result)
    {
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'bookingConfirmMailToOrg',
                'subject' => 'Pluto: booking confirm',
                'data' => $result
            ];
            $emailRes = \Mail::to($result['org_email'])
                // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
                // ->cc('maulik.kanhasoft@gmail.com')
                // ->bcc('suresh.kanhasoft@gmail.com')
                ->send(new \App\Mail\SendSmtpMail($details));

            //send notification
            $objNotification = new Notification();
            $notification = $objNotification->addNotification($result);
            return true;
        } else {
            return false;
        }
    }




    //booking canceled by staff/org email
    public function sendBookingCancelByStaffEmail($result)
    {

        // print_r($result);exit();
        if (isset($result) && !empty($result)) {

            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'bookingCancelByStaff',
                'subject' => 'Pluto: Your booking is cancelled by admin',
                'data' => $result
            ];
            // print_r($details['data']);exit();
            $emailRes = \Mail::to($result['email'])
                ->send(new \App\Mail\SendSmtpMail($details));

            $objNotification = new Notification();
            // $notification = $objNotification->addNotification($result);
            $notification = $objNotification->addNotificationV2($result, 'shift_cancel', '', '');
            return true;
        } else {
            return false;
        }
    }

    //send offer to Candidate email
    public function sendOfferToSigneeEmail($result)
    {
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {
            //print_r($result['email']);exit();
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'sendShiftOfferToSignee',
                'subject' => 'Pluto: Offer For Shift',
                'data' => $result
            ];

            $emailRes = \Mail::to($result['email'])->send(new \App\Mail\SendSmtpMail($details));

            $objNotification = new Notification();
            $notification = $objNotification->addNotification($result);
            return true;
        } else {
            return false;
        }
    }

    //booking accepted by Candidate email to org
    public function sendSigneeAccepBookingEmailToOrg($res)
    {
        //print_r($res);exit();
        if (isset($res) && !empty($res)) {
            //print_r($result['email']);exit();
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'signeeAccepBookingEmailToOrg',
                'subject' => 'Pluto: Booking Accepted By Candidate',
                'data' => $res
            ];

            // $emailRes = \Mail::to($res['email'])->send(new \App\Mail\SendSmtpMail($details));

            $objNotification = new Notification();
            $notification = $objNotification->addNotificationV2($res, 'shift_accept');
            return true;
        } else {
            return false;
        }
    }

    //booking created email to organization
    public function sendBookingCreatedEmailToOrg($res)
    {
        //print_r($res);exit();
        if (isset($res) && !empty($res)) {
            //print_r($result['email']);exit();
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'sendBookingCreatedEmailToOrg',
                'subject' => 'Pluto: Booking Created',
                'data' => $res
            ];
            //print_r($details);exit();
            // $emailRes = \Mail::to($res['org_email'])->send(new \App\Mail\SendSmtpMail($details));

            $objNotification = new Notification();
            $notification = $objNotification->addNotification($res);
            return true;
        } else {
            return false;
        }
    }

    //booking reject by Candidate email to org
    public function sendSigneeCancelBookingEmailToOrg($res)
    {
        //print_r($res);exit();
        if (isset($res) && !empty($res)) {
            //print_r($result['email']);exit();
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'signeeCancelBookingEmailToOrg',
                'subject' => 'Pluto: Booking Cancel By Candidate',
                'data' => $res
            ];
            $emailRes = \Mail::to($res['email'])->send(new \App\Mail\SendSmtpMail($details));
            $objNotification = new Notification();
            $notification = $objNotification->addNotification($res);
            return true;
        } else {
            return false;
        }
    }

    //booking accepted by Candidate email
    public function sendBookingAcceptBySigneeEmail($result)
    {
        //print_r($result);exit();



        if (isset($result) && !empty($result)) {
            //print_r($result['email']);exit();
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'bookingAcceptBySignee',
                'subject' => 'Pluto: Booking Accepted',
                'data' => $result
            ];
            // $emailRes = \Mail::to($result['email'])->send(new \App\Mail\SendSmtpMail($details));
            $objNotification = new Notification();
            $notification = $objNotification->addNotification($result);

            return true;
        } else {
            return false;
        }
    }


    //booking canceled by Candidate email
    public function sendBookingCancelBySigneeEmail($result)
    {
        // print_r($result);exit;
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {
            //print_r($result['email']);exit();
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'bookingCancelBySignee',
                'subject' => 'Pluto: Your booking is cancelled',
                'data' => $result
            ];

            // $emailRes = \Mail::to($result['email'])->send(new \App\Mail\SendSmtpMail($details));

            $objNotification = new Notification();
            $notification = $objNotification->addNotification($result);
            return true;
        } else {
            return false;
        }
    }

    //booking apply by signee
    public function sendBookingApplyBySigneeEmail($result)
    {
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {
            //print_r($val);exit();
            //print_r($val);exit();
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'signeeApplyShift',
                'subject' => 'Pluto: You applied for the shift',
                'data' => $result
            ];
            //print_r($details);exit();
            // $emailRes = \Mail::to($result['email'])->send(new \App\Mail\SendSmtpMail($details));

            $objNotification = new Notification();
            $notification = $objNotification->addNotification($result);
            return true;
        } else {
            return false;
        }
    }

    //booking apply by Candidate mail to org
    public function sendBookingApplyBySigneeEmailToOrg($result)
    {
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {
            //print_r($val);exit();
            //print_r($val);exit();
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'signeeApplyShiftMailToOrg',
                'subject' => 'Pluto: Shift Apply',
                'data' => $result
            ];
            //print_r($details);exit();
            // $emailRes = \Mail::to($result['email'])->send(new \App\Mail\SendSmtpMail($details));

            $objNotification = new Notification();
            $notification = $objNotification->addNotification($result);
            return true;
        } else {
            return false;
        }
    }

    public function sendBookingInvitationMail($result)
    {
        // print_r(Auth::user()->role);exit;
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {
            foreach ($result as $key => $val) {
                //print_r($val['email']);exit();
                $details = [
                    'title' => '',
                    'body' => 'Hello ',
                    'mailTitle' => 'bookingInvite',
                    'subject' => 'Pluto: Offer Mail',
                    'data' => $val
                ];
                $emailRes = \Mail::to($result)
                    // ->cc('maulik.kanhasoft@gmail.com')
                    // ->bcc('suresh.kanhasoft@gmail.com')
                    ->send(new \App\Mail\SendSmtpMail($details));
                $objNotification = new Notification();

                if (Auth::user()->role == "ORGANIZATION") {
                    $notification = $objNotification->addNotificationV2($val, 'org_invite_candidate');
                } else {
                    $notification = $objNotification->addNotificationV2($val, 'staff_invite_candidate');
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function editMetchBySigneeId($signeeId = null)
    {
        $subQuery = Booking::select(
            'users.email',
            'signee_preference.user_id as signeeId',
            'bookings.id as booking_id',
            'users.address_line_1',
            'users.address_line_2',
            'users.city',
            'users.role',
            'bookings.user_id as organization_id',
            'bookings.*',
            'shift_type.shift_type',
            'hospitals.hospital_name',
            'ward.ward_name',
            DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
            DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
            DB::raw('GROUP_CONCAT(signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
            DB::raw('GROUP_CONCAT(specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        );
        //$subQuery->leftJoin('bookings',  'bookings.id', '=', 'booking_matches.booking_id');
        $subQuery->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $subQuery->leftJoin('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $subQuery->leftJoin('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
        $subQuery->leftJoin('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $subQuery->leftJoin('users',  'users.id', '=', 'signee_speciality.user_id');
        $subQuery->leftJoin('signee_preference',  'signee_preference.user_id', '=', 'users.id');
        $subQuery->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $subQuery->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');

        $subQuery->where('users.role', 'SIGNEE');
        $subQuery->where('users.id', $signeeId);
        $subQuery->whereNull('signee_speciality.deleted_at');
        $subQuery->whereNull('booking_specialities.deleted_at');
        $subQuery->whereNull('bookings.deleted_at');
        // $subQuery->groupBy('signee_speciality.id','booking_specialities.id');
        $subQuery->groupBy('bookings.id');
        $subQuery->orderBy('signeeBookingCount', 'DESC');
        $subQuery->whereRaw("(
            IF(DAYOFWEEK(`bookings`.`date`) = 1, (`signee_preference`.`sunday_day` = 1 or `signee_preference`.`sunday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 2, (`signee_preference`.`monday_day` = 1 or `signee_preference`.`monday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 3, (`signee_preference`.`tuesday_day` = 1 or `signee_preference`.`tuesday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 4, (`signee_preference`.`wednesday_day` = 1 or `signee_preference`.`wednesday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 5, (`signee_preference`.`thursday_day` = 1 or `signee_preference`.`thursday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 6, (`signee_preference`.`friday_day` = 1 or `signee_preference`.`friday_night` = 1),'')
            or
            IF(DAYOFWEEK(`bookings`.`date`) = 7, (`signee_preference`.`saturday_day` = 1 or `signee_preference`.`saturday_night` = 1),'')
        )");
        $res = $subQuery->get()->toArray();
        return $res;
    }

    public function getSigneeByIdAndBookingId($bookingId = null, $signeeId = null)
    {
        $subQuery = Booking::select(
            // // $subQuery = BookingSpeciality::select(
            'users.id as signeeId',
            'bookings.id as booking_id',
            'users.address_line_1',
            'users.address_line_2',
            'users.city',
            'users.role',
            'bookings.user_id as organization_id',
            'bookings.*',
            DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
            DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
            DB::raw('GROUP_CONCAT(signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
            DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        );
        $subQuery->Join('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $subQuery->Join('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
        $subQuery->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');

        $subQuery->Join('users',  'users.id', '=', 'signee_speciality.user_id');
        $subQuery->where('users.role', 'SIGNEE');
        $subQuery->where('bookings.id', $bookingId);
        $subQuery->where('users.id', $signeeId);
        $subQuery->whereNull('signee_speciality.deleted_at');
        $subQuery->whereNull('booking_specialities.deleted_at');
        $subQuery->whereNull('bookings.deleted_at');
        $subQuery->groupBy('users.id');

        $subQuery->orderBy('signeeBookingCount', 'DESC');
        $res = $subQuery->get()->toArray();

        return $res;
    }

    public function getBookingSignee($bookingId = null)
    {
        $query = Booking::select(
            'bookings.*',
            'ward.ward_name',
            'trusts.name',
            // 'organization_shift.start_time',
            // 'organization_shift.end_time',
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS organization_name'),
        );
        $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $query->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $query->leftJoin('organization_shift',  'organization_shift.id', '=', 'bookings.shift_id');
        $query->leftJoin('users',  'users.id', '=', 'trusts.user_id');

        $query->where('bookings.id', $bookingId);
        $bookingList = $query->get();

        $subArray = [];
        foreach ($bookingList as $key => $booking) {

            $subArray[$key] = $booking;
            $subQuery = BookingSpeciality::select(
                'users.id',
                'booking_specialities.booking_id',
                'specialities.speciality_name',
                'signees_detail.candidate_id',
                'signees_detail.phone_number',
                'signees_detail.mobile_number',
                'users.address_line_1',
                'users.address_line_2',
                'users.city',
                'users.postcode',
                'signees_detail.date_of_birth',
                'signees_detail.nationality',
                'signees_detail.candidate_referred_from',
                'signees_detail.date_registered',

                'signees_detail.nmc_dmc_pin',
                DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
                DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
                DB::raw('GROUP_CONCAT(DISTINCT specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
                DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
            );
            $subQuery->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
            $subQuery->Join('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
            $subQuery->Join('users',  'users.id', '=', 'signee_speciality.user_id');
            $subQuery->Join('signees_detail',  'signees_detail.user_id', '=', 'users.id');

            $subQuery->where('booking_specialities.booking_id', $bookingId);
            $subQuery->groupBy('users.id');
            $subQuery->orderBy('signeeBookingCount', 'DESC');
            $res = $subQuery->get()->toArray();
            $subArray[$key]['user'] = $res;
        }
        return $subArray;
    }

    public function getMatchByBooking($bookingId, $status, $bookingStatus)
    {
        //print_r($status);
        // print_r($bookingStatus);exit;
        $subQuery = Booking::select(
            'users.id as signeeId',
            'users.address_line_1',
            'users.address_line_2',
            'users.contact_number',
            'users.city',
            'users.status as signee_activity_status',
            'users.first_name',
            'users.last_name',
            'users.email',
            'booking_matches.signee_booking_status',
            'booking_matches.payment_status',
            'booking_matches.id as booking_match_id',
            'signee_organization.status as compliance_status',
            'bookings.user_id as organization_id',
            'bookings.*',
        );
        $subQuery->Join('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $subQuery->Join('users',  'users.id', '=', 'booking_matches.signee_id');
        $subQuery->leftJoin('signee_organization',  'signee_organization.user_id', '=', 'users.id');


        if (Auth::user()->role == 'ORGANIZATION') {
            $subQuery->where('signee_organization.organization_id', Auth::user()->id);
        } else {
            //print_r(Auth::user()->role);exit;
            // $query->where('bookings.user_id',Auth::user()->id);
            $subQuery->where('signee_organization.organization_id', Auth::user()->parent_id);
        }

        $subQuery->where('signee_organization.status', 'COMPLIANT');
        $subQuery->where('users.role', 'SIGNEE');
        $subQuery->where('users.status', 'Active');
        $subQuery->where('bookings.id', $bookingId);

        // if($bookingStatus == 'CONFIRMED')
        // {
        //     $subQuery->where('booking_matches.signee_booking_status', 'CONFIRMED');
        // } else if($bookingStatus == 'CREATED')
        // {
        //     $subQuery->where('booking_matches.signee_status', $status);
        //     $subQuery->whereIn('booking_matches.signee_booking_status', array('CONFIRMED','PENDING','CANCEL','INVITE','APPLY','REJECTED','OFFER','DECLINE','ACCEPT'));
        // }
        if ($bookingStatus == 'CONFIRMED' && $status == 'Matching') {
            $subQuery->where('booking_matches.signee_booking_status', 'CONFIRMED');
            $subQuery->where('booking_matches.signee_status', 'Matching');
        } else if ($bookingStatus == 'CONFIRMED' && $status == 'CONFIRMED') {
            $subQuery->where('booking_matches.signee_booking_status', 'CONFIRMED');
        } else {
            $subQuery->where('booking_matches.signee_status', $status);
            $subQuery->whereNull('signee_organization.deleted_at');
            $subQuery->whereIn('booking_matches.signee_booking_status', array('CONFIRMED', 'PENDING', 'CANCEL', 'INVITE', 'APPLY', 'REJECTED', 'OFFER', 'DECLINE', 'ACCEPT'));
        }
        $subQuery->where('booking_matches.deleted_at');

        // $res = $subQuery->toSql();
        // print_r($res);exit;
        $res = $subQuery->get()->toArray();

        return $res;
    }

    public function getSigneeForPDF($postData)
    {
        //print_r($postData);exit;
        $query = Booking::select(
            'bookings.*',
            'users.contact_number',
            'users.email',
            'users.postcode',
            'users.city',
            'users.address_line_1',
            'users.address_line_2',
            DB::raw('GROUP_CONCAT(signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
            DB::raw('GROUP_CONCAT(DISTINCT specialities.speciality_name SEPARATOR "  | ") AS speciality_name'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        );
        $query->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $query->leftJoin('users',  'users.id', '=', 'booking_matches.signee_id');
        //$query->leftJoin('signee_speciality',  'signee_speciality.user_id', '=', 'booking_matches.signee_id');
        $query->join('signee_speciality', function ($join) {
            $join->on('signee_speciality.organization_id', '=', 'booking_matches.organization_id');
            $join->on('signee_speciality.user_id', '=', 'booking_matches.signee_id');
        });
        $query->leftJoin('specialities',  'specialities.id', '=', 'signee_speciality.speciality_id');
        $query->whereIn('booking_matches.signee_id', $postData['signee_id']);
        $query->whereNull('signee_speciality.deleted_at');
        $query->where('bookings.id', $postData['booking_id']);
        $query->groupBy('booking_matches.signee_id');
        //print_r($query->toSql());exit();
        return $query->get()->toArray();
    }

    public function getSigneeForInvite($postData)
    {
        $query = Booking::select(
            'bookings.*',
            'bookings.id as booking_id',
            'booking_matches.signee_id',
            'booking_matches.signee_booking_status',
            'booking_matches.organization_id',
            'users.contact_number',
            'users.email',
            'users.postcode',
            'users.city',
            'users.role',
            'users.id as signeeId',
            'users.address_line_1',
            'users.address_line_2',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
            DB::raw('GROUP_CONCAT(signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
            DB::raw('GROUP_CONCAT(DISTINCT specialities.speciality_name SEPARATOR "  | ") AS speciality_name'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        );
        $query->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $query->leftJoin('users',  'users.id', '=', 'booking_matches.signee_id');
        $query->leftJoin('signee_speciality',  'signee_speciality.user_id', '=', 'booking_matches.signee_id');
        $query->leftJoin('specialities',  'specialities.id', '=', 'signee_speciality.speciality_id');
        $query->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $query->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        $query->whereIn('booking_matches.signee_id', $postData['signee_id']);
        $query->whereNull('signee_speciality.deleted_at');
        $query->where('bookings.id', $postData['booking_id']);
        $query->groupBy('booking_matches.signee_id');
        //print_r($query->toSql());exit();
        return $query->get()->toArray();
    }

    public function addsigneeMatch($id)
    {
        try {
            $booking = $this->editMetchBySigneeId($id);
            $objBookingMatch = new BookingMatch();
            $bookingMatch = $objBookingMatch->editBookingMatchBySignee($booking, $id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id');
    }

    public function ward()
    {
        return $this->hasOne(Ward::class, 'id');
    }

    public function shift()
    {
        return $this->hasOne(OrganizationShift::class, 'id', 'shift_id');
    }

    public function hospital()
    {
        return $this->hasOne(Hospital::class, 'id', 'hospital_id');
    }

    public function shiftType()
    {
        return $this->hasOne(ShiftType::class, 'id', 'shift_type_id');
    }

    public function trust()
    {
        return $this->hasOne(Trust::class, 'id', 'trust_id');
    }

    public function getAppliedShift()
    {
        $query = Booking::select(
            'bookings.*',
            'booking_matches.signee_booking_status',
        );
        $query->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $query->where('booking_matches.signee_booking_status', 'APPLY');
        $query->where('booking_matches.signee_id', Auth::user()->id);
        return $query->get();
    }

    public function getCompletedShift()
    {
        $booking = Booking::select(
            'bookings.*',
            //'booking_matches.signee_booking_status',
        );
        //$booking->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $booking->where('bookings.date', '<', date('Y-m-d'));
        //$booking->where('booking_matches.signee_booking_status', 'CONFIRMED');
        $booking->where('bookings.status', 'CONFIRMED');
        return $booking->get();
    }


    public function getBookingCancelByAdminEmail($bookingId = null)
    {
        $subQuery = Booking::select(
            'users.first_name',
            'users.last_name',
            'users.email',
            'signee_preference.user_id as signeeId',
            'bookings.id as booking_id',
            'users.address_line_1',
            'users.address_line_2',
            'users.city',
            'users.role',
            'users.parent_id as signee_org_id',
            'bookings.user_id as organization_id',
            'bookings.*',
            'booking_matches.signee_booking_status',
            'booking_matches.signee_status',
            'booking_matches.id as aaaa',
            'shift_type.shift_type',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
            'bookings.date as booking_date',
            DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
            DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
            DB::raw('GROUP_CONCAT(signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
            DB::raw('GROUP_CONCAT( distinct(specialities.speciality_name) SEPARATOR ", ") AS speciality_name'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        );
        $subQuery->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $subQuery->leftJoin('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $subQuery->leftJoin('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
        $subQuery->leftJoin('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $subQuery->leftJoin('users',  'users.id', '=', 'signee_speciality.user_id');
        $subQuery->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $subQuery->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $subQuery->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        $subQuery->leftJoin('booking_matches', function ($join) {
            $join->on('booking_matches.booking_id', '=', 'bookings.id');
            $join->on('booking_matches.signee_id', '=', 'users.id');
            // $join->whereIn('booking_matches.signee_status', array('APPLY','ACCEPT','CONFIRMED','OFFER','INVITE'));
        });
        $subQuery->Join('signee_preference',  'signee_preference.user_id', '=', 'users.id');
        $subQuery->where('users.role', 'SIGNEE');
        $subQuery->where('bookings.id', $bookingId);
        $subQuery->whereNull('signee_speciality.deleted_at');
        $subQuery->whereNull('booking_specialities.deleted_at');
        $subQuery->whereNull('bookings.deleted_at');
        $subQuery->groupBy('signee_preference.user_id');
        $subQuery->orderBy('signeeBookingCount', 'DESC');
        $res = $subQuery->get()->toArray();
        return $res;
    }

    //Report APIs

    public function getCompletedBookingByDate(Request $request)
    {
        if ($request->get('act') == 'export_csv') {
            $export =  "yes";
        } else {
            $export =  "no";
        }

        //echo $export; exit;
        $perPage = Config::get('constants.pagination.perPage');
        $date_range = !empty($request->get('date_between')) ? $request->get('date_between') : "";
        $trust_id = !empty($request->get('trust_id')) ? $request->get('trust_id') : "";
        $status = 'COMPLETED';

        $query = Booking::select(
            'bookings.*',
            'ward.ward_name',
            'trusts.name',
            'trusts.code',
            'grade.grade_name',
            'shift_type.shift_type',
            'hospitals.hospital_name',
            'booking_matches.signee_booking_status',
            DB::raw('SUM(bookings.payable) as payableAmount'),
            DB::raw('bookings.payable - bookings.chargeable as payableAmount1'),
            DB::raw('SUM(bookings.chargeable) as totalChargeable'),
            //'organization_shift.start_time',
            //'organization_shift.end_time',
            // 'booking_matches.id as bmid',
            DB::raw('GROUP_CONCAT(CONCAT(u.first_name," ", u.last_name) SEPARATOR ", ") AS candidate'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS organization_name'),
            DB::raw('CONCAT(sc.first_name," ", sc.last_name) AS created_shift_org_name'),
        );
        $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $query->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $query->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $query->leftJoin('organization_shift',  'organization_shift.id', '=', 'bookings.shift_id');
        $query->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $query->leftJoin('grade',  'grade.id', '=', 'bookings.grade_id');
        $query->leftJoin('users',  'users.id', '=', 'trusts.user_id');
        $query->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $query->leftJoin('users as u',  'u.id', '=', 'booking_matches.signee_id');
        $query->leftJoin('users as sc',  'sc.id', '=', 'bookings.created_by');


        //$query->where('bookings.status', $status);
        //  if ($status == 'COMPLETED') {
        $query->where('bookings.date', '<', date('Y-m-d'));
        $query->where('bookings.status', 'CONFIRMED');
        $query->where('booking_matches.signee_booking_status', 'CONFIRMED');
        // }
        //print_r($query->toSql());exit;

        if (!empty($request->get('start_date')) && !empty($request->get('end_date'))) {
            // $date_range = explode("to", $request->get('date_between'));
            // $from_date = $date_range[0];
            // $to_date = $date_range[1];
            $date_range = explode("to", $request->get('date_between'));
            $from_date = $request->get('start_date');
            $to_date = $request->get('end_date');
            $query->whereBetween('bookings.date', [$from_date, $to_date]);
        }

        if (!empty($trust_id)) {
            $query->where('trusts.id', $trust_id);
        }

        if (Auth::user()->role == 'ORGANIZATION') {
            $staff = User::select('id')->where('parent_id', Auth::user()->id)->get()->toArray();
            $staffIdArray = array_column($staff, 'id');
            $staffIdArray[] = Auth::user()->id;
            // echo '<pre>';
            // print_r(implode(', ',$staffIdArray)); exit;
            $query->whereIn('bookings.user_id', $staffIdArray);
        } else {
            // $query->where('bookings.user_id',Auth::user()->id);
            $query->whereIn('bookings.user_id', array(Auth::user()->id, Auth::user()->parent_id));
        }

        $query->whereNull('bookings.deleted_at');
        $query->orderBy('bookings.date', 'ASC');
        $query->groupBy('bookings.id');
        // print_r($query->toSql());exit;

        if ($export == 'yes') {
            $bookingList = $query->get();
        } else {
            $bookingList = $query->latest()->paginate($perPage);
        }

        // $query = Booking::select();
        //print_r($query->toSql());exit;
        //print_r($bookingList);exit;
        return $bookingList;
    }


    public function makeCalculation($postData)
    {
        $trustResult = Trust::find($postData['trust_id']);
        // print_r($trustResult->user_id);
        // exit;
        $payable_day_rate = (isset($trustResult->payable_day_rate)) ? $trustResult->payable_day_rate : 0;
        $payable_night_rate = (isset($trustResult->payable_night_rate)) ? $trustResult->payable_night_rate : 0;
        $payable_saturday_rate = (isset($trustResult->payable_saturday_rate)) ? $trustResult->payable_saturday_rate : 0;
        $payable_holiday_rate = (isset($trustResult->payable_holiday_rate)) ? $trustResult->payable_holiday_rate : 0;

        $chargeable_day_rate = (isset($trustResult->chargeable_day_rate)) ? $trustResult->chargeable_day_rate : 0;
        $chargeable_night_rate = (isset($trustResult->chargeable_night_rate)) ? $trustResult->chargeable_night_rate : 0;
        $chargeable_saturday_rate = (isset($trustResult->chargeable_saturday_rate)) ? $trustResult->chargeable_saturday_rate : 0;
        $chargeable_holiday_rate = (isset($trustResult->chargeable_holiday_rate)) ? $trustResult->chargeable_holiday_rate : 0;

        $convertStartTime =  date('H:i:s', strtotime($postData['start_time']));
        $convertEndTime =  date('H:i:s', strtotime($postData['end_time']));

        $payableAmount = 0;
        $chargebleAmount = 0;
        $returnResult = [];
        $dayName = date('l', strtotime($postData['date']));
        if ($dayName == 'Sunday') {
            if ($convertStartTime > "13:00:00") {
                if ($convertStartTime >= "19:30:00") {
                    $dayTime = $this->getTimeDiff($convertStartTime, "23:60:00");

                    $payableAmount += $dayTime * $payable_holiday_rate;
                    $chargebleAmount += $dayTime * $chargeable_holiday_rate;

                    if ($convertEndTime > "06:00:00") {
                        $upto6 = $this->getTimeDiff("00:00:00", "06:00:00");
                        $above6 = $this->getTimeDiff("06:00:00", $convertEndTime);
                        $payableAmount += $above6 * $payable_day_rate;
                        $payableAmount += $upto6 * $payable_night_rate;
                        $chargebleAmount += $above6 * $chargeable_day_rate;
                        $chargebleAmount += $upto6 * $chargeable_night_rate;
                    } else {
                        $nightTime = $this->getTimeDiff("00:00:00", $convertEndTime);
                        $payableAmount += $nightTime * $payable_night_rate;
                        $chargebleAmount += $nightTime * $chargeable_night_rate;
                    }
                } else {
                    $dayTime = $this->getTimeDiff($convertStartTime, $convertEndTime);
                    $payableAmount += $dayTime * $payable_saturday_rate;
                    $chargebleAmount += $dayTime * $chargeable_saturday_rate;
                }
            } else {
                $dayTime = $this->getTimeDiff($convertStartTime, $convertEndTime);
                $payableAmount += $dayTime * $payable_holiday_rate;
                $chargebleAmount += $dayTime * $chargeable_holiday_rate;
            }
            // echo "=> $payableAmount payableAmount";
            // echo "=> $chargebleAmount chargebleAmount";
        } else if ($dayName == 'Saturday') {
            if ($convertStartTime > "13:00") {
                if ($convertStartTime >= "19:30") {
                    $dayTime = $this->getTimeDiff($convertStartTime, "23:60:00");
                    $nightTime = $this->getTimeDiff("00:00:00", $convertEndTime);
                    $payableAmount += $dayTime * $payable_saturday_rate;
                    $payableAmount += $nightTime * $payable_holiday_rate;
                    $chargebleAmount += $dayTime * $chargeable_saturday_rate;
                    $chargebleAmount += $nightTime * $chargeable_holiday_rate;
                } else {
                    $dayTime = $this->getTimeDiff($convertStartTime, $convertEndTime);
                    $payableAmount += $dayTime * $payable_saturday_rate;
                    $chargebleAmount += $dayTime * $chargeable_saturday_rate;
                }
            } else {
                $dayTime = $this->getTimeDiff($convertStartTime, $convertEndTime);
                $payableAmount += $dayTime * $payable_saturday_rate;
                $chargebleAmount += $dayTime * $chargeable_saturday_rate;
                //     echo "=> $payableAmount payableAmount";
                //     echo "=> $chargebleAmount chargebleAmount";
            }
        } else {
            if ($convertStartTime >= "00:00:00" && $convertEndTime <= "20:00:00") {
                if ($convertStartTime < "06:00:00") {
                    $nightTime = $this->getTimeDiff($convertStartTime, "06:00:00");
                    $dayTime = $this->getTimeDiff("06:00:00", $convertEndTime);
                    $payableAmount += $dayTime * $payable_day_rate;
                    $payableAmount += $nightTime * $payable_night_rate;
                    $chargebleAmount += $dayTime * $chargeable_day_rate;
                    $chargebleAmount += $nightTime * $chargeable_night_rate;
                } else if ($convertStartTime >= "06:00:00" && $convertEndTime > "08:00:00") {
                    if ($convertEndTime < $convertStartTime) {
                        $nightTime1 = 0;
                        $dayTime = $this->getTimeDiff($convertStartTime, "20:00:00");
                        $nightTime = $this->getTimeDiff("20:00:00", $convertEndTime);
                        if ($nightTime <= 4) {
                            $nightTime += $this->getTimeDiff("20:00:00", $convertEndTime);
                        }
                        if ($nightTime > 4) {
                            $nightTime = $this->getTimeDiff("20:00:00", "24:00:00");
                            $nightTime += $this->getTimeDiff("00:00:00", "06:00:00");
                            $dayTime += $this->getTimeDiff("06:00:00", $convertEndTime);
                        }
                        $payableAmount += ($dayTime * $payable_day_rate);
                        $payableAmount += ($nightTime) * $payable_night_rate;
                        $chargebleAmount += ($dayTime * $chargeable_day_rate);
                        $chargebleAmount += ($nightTime + $nightTime1)  * $chargeable_night_rate;
                    } else {
                        $timeDay = $this->getTimeDiff($convertStartTime, $convertEndTime);
                        $payableAmount += $timeDay * $payable_day_rate;
                        $chargebleAmount += $timeDay * $chargeable_day_rate;
                    }
                }elseif($convertEndTime == "00:00:00"){
                    $nightTime1 = 0;
                    $dayTime = $this->getTimeDiff($convertStartTime, "20:00:00");
                    $nightTime = $this->getTimeDiff("20:00:00", $convertEndTime);
                    $payableAmount += ($dayTime * $payable_day_rate);
                    $payableAmount += ($nightTime) * $payable_night_rate;
                    $chargebleAmount += ($dayTime * $chargeable_day_rate);
                    $chargebleAmount += ($nightTime )  * $chargeable_night_rate;
                }
            } elseif ($convertStartTime >= "12:00:00" && $convertEndTime >= "20:00:00") {
                $dayTime1 = $this->getTimeDiff($convertStartTime, "20:00:00");
                $nightTime = $this->getTimeDiff("20:00:00", $convertEndTime);
                $payableAmount += ($dayTime1 * $payable_day_rate);
                $payableAmount += $nightTime  * $payable_night_rate;
                $chargebleAmount += ($dayTime1 * $chargeable_day_rate);
                $chargebleAmount += $nightTime * $chargeable_night_rate;
            } elseif ($convertStartTime >= "00:00:00" && $convertEndTime <= "23:30:00") {
                $nightTime1 = $this->getTimeDiff($convertStartTime, "06:00:00");
                $checkTime = $this->getTimeDiff("06:00:00", $convertEndTime);
                $nightTime = ($checkTime - 14);
                $dayTime1 =  ($checkTime - $nightTime);
                $payableAmount += ($dayTime1 * $payable_day_rate);
                $payableAmount += ($nightTime + $nightTime1) * $payable_night_rate;
                $chargebleAmount += ($dayTime1 * $chargeable_day_rate);
                $chargebleAmount += ($nightTime + $nightTime1)  * $chargeable_night_rate;
            } 
            // // } else if ($dayName == 'Monday') {
            // if ($convertStartTime > 13) {
            //     if ($convertStartTime <= "20:00") {
            //         $dayTime = $this->getTimeDiff($convertStartTime, "20:00:00");
            //         $nightTime = $this->getTimeDiff("20:00:00", "23:60:00");
            //         $payableAmount += $dayTime * $payable_day_rate;
            //         $payableAmount += $nightTime * $payable_night_rate;
            //         $chargebleAmount += $dayTime * $chargeable_day_rate;
            //         $chargebleAmount += $nightTime * $chargeable_night_rate;
            //         // echo "$dayTime == $nightTime";
            //         // exit;
            //     } else {
            //         $timeDay = $this->getTimeDiff($convertStartTime, "23:60:00");
            //         $payableAmount += $timeDay * $payable_day_rate;
            //         $chargebleAmount += $timeDay * $chargeable_day_rate;
            //     }

            //     if ($convertEndTime > 6) {
            //         $upto6 = $this->getTimeDiff("00:00:00", "06:00:00");
            //         $above6 = $this->getTimeDiff("06:00:00", $convertEndTime);
            //         $payableAmount += $above6 * $payable_day_rate;
            //         $payableAmount += $upto6 * $payable_night_rate;
            //         $chargebleAmount += $above6 * $chargeable_day_rate;
            //         $chargebleAmount += $upto6 * $chargeable_night_rate;
            //         // echo "$upto6 == $above6";
            //     } else {
            //         $endTime = $this->getTimeDiff("00:00:00", $convertEndTime);
            //     }
            // } else {
            //     $dayShift = $this->getTimeDiff($convertStartTime, $convertEndTime);
            //     $payableAmount += $dayShift * $payable_day_rate;
            //     $chargebleAmount += $dayShift * $chargeable_day_rate;
            // }
            // // echo "=> $chargebleAmount chargebleAmount";
            // // echo "=> $payableAmount payableAmount";exit;
        }
        $returnResult['payableAmount'] = number_format($payableAmount, 2);
        $returnResult['chargebleAmount'] = number_format($chargebleAmount, 2);
        return $returnResult;
    }

    /**
     * [getTimeDiff description]
     *
     * @param   [time]  $dtime  [$dtime start time]
     * @param   [time]  $atime  [$atime end time]
     *
     * @return  [array]          [return calculation array]
     */
    function getTimeDiff($dtime, $atime)
    {
        $nextDay = $dtime > $atime ? 1 : 0;
        $dep = explode(':', $dtime);
        $arr = explode(':', $atime);
        $diff = abs(mktime($dep[0], $dep[1], 0, date('n'), date('j'), date('y')) - mktime($arr[0], $arr[1], 0, date('n'), date('j') + $nextDay, date('y')));
        $hours = floor($diff / (60 * 60));
        $mins = floor(($diff - ($hours * 60 * 60)) / (60));
        $secs = floor(($diff - (($hours * 60 * 60) + ($mins * 60))));
        if (strlen($hours) < 2) {
            $hours = "0" . $hours;
        }
        if (strlen($mins) < 2) {
            $mins = "0" . $mins;
        }
        if (strlen($secs) < 2) {
            $secs = "0" . $secs;
        }
        if ($mins == 30) {
            $mins = 5;
        }
        return $hours . '.' . $mins;
        // return $hours.':'.$mins.':'.$secs;
    }
}
