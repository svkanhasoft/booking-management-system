<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Http\Request;
use Config;
use Auth;
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
    protected $fillable = ['user_id', 'hospital_id','reference_id', 'trust_id', 'ward_id', 'shift_id', 'shift_type_id', 'date', 'grade_id', 'status', 'rate', 'start_time', 'end_time','created_by', 'updated_by'];
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
            'organization_shift.start_time',
            'organization_shift.end_time',
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
            'organization_shift.start_time',
            'organization_shift.end_time',
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
        $query = Booking::select(
            'bookings.*',
            'ward.ward_name',
            'trusts.name',
            'grade.grade_name',
            'shift_type.shift_type',
            'hospitals.hospital_name',
            //'organization_shift.start_time',
            //'organization_shift.end_time',
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS organization_name'),
        );
        $query->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $query->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
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
        if($status == 'CREATED'){
            $query->where('bookings.date', '>=', date('y-m-d'));
        }
        // $query->where('bookings.user_id',Auth::user()->id);

        if(Auth::user()->role == 'ORGANIZATION'){
            $staff = User::select('id')->where('parent_id', Auth::user()->id)->get()->toArray();
            $staffIdArray = array_column($staff, 'id');
            $staffIdArray[] = Auth::user()->id;
            $query->whereIn('bookings.user_id',$staffIdArray);
        }else{
            // $query->where('bookings.user_id',Auth::user()->id);
            $query->whereIn('bookings.user_id',array(Auth::user()->id,Auth::user()->parent_id));
        }

        $query->whereNull('bookings.deleted_at');
        $query->orderBy('bookings.id', 'DESC');
        $query->groupBy('bookings.id');
        $bookingList = $query->latest()->paginate($perPage);
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
            'organization_shift.start_time',
            'organization_shift.end_time',
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
        $query->whereIn('bookings.user_id',array(Auth::user()->id,Auth::user()->parent_id));
        $query->whereNull('bookings.deleted_at');
        $query->groupBy ('bookings.id');
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
            'organization_shift.start_time',
            'organization_shift.end_time',
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
        $query->groupBy ('bookings.id');
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
            'bookings.user_id as organization_id',
            'bookings.*',
            'shift_type.shift_type',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
            'booking_matches.signee_status',
            'booking_matches.signee_booking_status',
            'booking_matches.id as bookingMatchId',
            DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
            DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
            DB::raw('GROUP_CONCAT(DISTINCT signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
            DB::raw('GROUP_CONCAT( distinct(specialities.speciality_name) SEPARATOR ", ") AS speciality_name'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        );
        // $subQuery->leftJoin('bookings',  'bookings.id', '=', 'booking_matches.booking_id');
        $subQuery->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
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
        $subQuery->groupBy('signee_preference.user_id');
        $subQuery->orderBy('signeeBookingCount', 'DESC');

        // $subQuery->whereRaw("(
        //     IF(DAYOFWEEK(`bookings`.`date`) = 1, (`signee_preference`.`sunday_day` = 1 or `signee_preference`.`sunday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 2, (`signee_preference`.`monday_day` = 1 or `signee_preference`.`monday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 3, (`signee_preference`.`tuesday_day` = 1 or `signee_preference`.`tuesday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 4, (`signee_preference`.`wednesday_day` = 1 or `signee_preference`.`wednesday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 5, (`signee_preference`.`thursday_day` = 1 or `signee_preference`.`thursday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 6, (`signee_preference`.`friday_day` = 1 or `signee_preference`.`friday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 7, (`signee_preference`.`saturday_day` = 1 or `signee_preference`.`saturday_night` = 1),'')
        // )");

        $res = $subQuery->get()->toArray();

        //print_r($res);exit();
        return $res;
    }

    public function getMetchByBookingIdAndSigneeId($bookingId = null, $signeeId = null)
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
            'booking_matches.signee_booking_status',
            'shift_type.shift_type',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward_type.ward_type',
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
        $subQuery->leftJoin('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $subQuery->Join('signee_preference',  'signee_preference.user_id', '=', 'users.id');

        $subQuery->where('users.role', 'SIGNEE');
        $subQuery->where('bookings.id', $bookingId);
        $subQuery->where('users.id', $signeeId);
        $subQuery->whereNull('signee_speciality.deleted_at');
        $subQuery->whereNull('booking_specialities.deleted_at');
        $subQuery->whereNull('bookings.deleted_at');
        $subQuery->groupBy('signee_preference.user_id');
        $subQuery->orderBy('signeeBookingCount', 'DESC');
        // $subQuery->whereRaw("(
        //     IF(DAYOFWEEK(`bookings`.`date`) = 1, (`signee_preference`.`sunday_day` = 1 or `signee_preference`.`sunday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 2, (`signee_preference`.`monday_day` = 1 or `signee_preference`.`monday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 3, (`signee_preference`.`tuesday_day` = 1 or `signee_preference`.`tuesday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 4, (`signee_preference`.`wednesday_day` = 1 or `signee_preference`.`wednesday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 5, (`signee_preference`.`thursday_day` = 1 or `signee_preference`.`thursday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 6, (`signee_preference`.`friday_day` = 1 or `signee_preference`.`friday_night` = 1),'')
        //     or
        //     IF(DAYOFWEEK(`bookings`.`date`) = 7, (`signee_preference`.`saturday_day` = 1 or `signee_preference`.`saturday_night` = 1),'')
        // )");
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
                'subject' => 'Booking Management System: Your booking is confirm',
                'data' => $result
            ];
            $emailRes = \Mail::to($result['email'])
                // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
            ->cc('maulik.kanhasoft@gmail.com')
            ->bcc('suresh.kanhasoft@gmail.com')
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
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {

                //print_r($result[$key]['email']);exit();
                //print_r($val);exit();
                $details = [
                    'title' => '',
                    'body' => 'Hello ',
                    'mailTitle' => 'bookingCancelByStaff',
                    'subject' => 'Booking Management System: Your booking is canceled',
                    'data' => $result
                ];
                // print_r($details['data']);exit();
                $emailRes = \Mail::to($result['email'])
                    // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
                ->cc('maulik.kanhasoft@gmail.com')
                ->bcc('suresh.kanhasoft@gmail.com')
                ->send(new \App\Mail\SendSmtpMail($details));

                $objNotification = new Notification();
                $notification = $objNotification->addNotification($result);
            return true;
        } else {
            return false;
        }
    }

    //send offer to signee email
    public function sendOfferToSigneeEmail($result)
    {
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {
            //print_r($result['email']);exit();
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'sendShiftOfferToSignee',
                'subject' => 'Booking Management System: Offer For Shift',
                'data' => $result
            ];
        // print_r($details['data']);exit();
            $emailRes = \Mail::to($result['email'])
                // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
            ->cc('maulik.kanhasoft@gmail.com')
            ->bcc('suresh.kanhasoft@gmail.com')
            ->send(new \App\Mail\SendSmtpMail($details));

            $objNotification = new Notification();
            $notification = $objNotification->addNotification($result);
            return true;
        } else {
            return false;
        }
    }


    //booking accepted by signee email
    public function sendBookingAcceptBySigneeEmail($result)
    {
        //print_r($result);exit();
        if (isset($result) && !empty($result)) {
                //print_r($result['email']);exit();
                $details = [
                    'title' => '',
                    'body' => 'Hello ',
                    'mailTitle' => 'bookingAcceptBySignee',
                    'subject' => 'Booking Management System: Booking Accepted',
                    'data' => $result
                ];
            // print_r($details['data']);exit();
                $emailRes = \Mail::to($result['email'])
                    // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
                ->cc('maulik.kanhasoft@gmail.com')
                ->bcc('suresh.kanhasoft@gmail.com')
                ->send(new \App\Mail\SendSmtpMail($details));

                // $objNotification = new Notification();
                // $notification = $objNotification->addNotification($result);
                return true;
        } else {
            return false;
        }
    }


    //booking canceled by signee email
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
                    'subject' => 'Booking Management System: Your booking is canceled',
                    'data' => $result
                ];
               // print_r($details['data']);exit();
                $emailRes = \Mail::to($result['email'])
                    // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
                ->cc('maulik.kanhasoft@gmail.com')
                ->bcc('suresh.kanhasoft@gmail.com')
                ->send(new \App\Mail\SendSmtpMail($details));

                $objNotification = new Notification();
                $notification = $objNotification->addNotification($result);
                return true;
        } else {
            return false;
        }
    }

    // public function sendBookingOpenEmail($result)
    // {
    //     //print_r($result);exit();

    //     if (isset($result) && !empty($result)) {
    //         foreach($result as $key=>$val)
    //         {
    //             //print_r($val);exit();
    //             if($val['signeeId'] != Auth::user()->id)
    //             {
    //                 //print_r($val);exit();
    //                 $details = [
    //                     'title' => '',
    //                     'body' => 'Hello ',
    //                     'mailTitle' => 'bookingOpened',
    //                     'subject' => 'Booking Management System: Your booking is opened',
    //                     'data' => $val
    //                 ];
    //                 //print_r($details);exit();
    //                 $emailRes = \Mail::to($val['email'])
    //                     // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
    //                 ->cc('maulik.kanhasoft@gmail.com')
    //                 ->bcc('suresh.kanhasoft@gmail.com')
    //                 ->send(new \App\Mail\SendSmtpMail($details));

    //                 $objNotification = new Notification();
    //                 $notification = $objNotification->addNotification($val);
    //                 return true;
    //             }
    //         }
    //     } else {
    //         return false;
    //     }
    // }

    //booking invitation email
    public function sendBookingInvitationMail($result)
    {
       //print_r($result);exit();
        if (isset($result) && !empty($result)) {
            foreach($result as $key=>$val)
            {
                //print_r($val['email']);exit();
                $details = [
                    'title' => '',
                    'body' => 'Hello ',
                    'mailTitle' => 'bookingInvite',
                    'subject' => 'Booking Management System: Invitation Mail',
                    'data' => $val
                ];
                //print_r($details);exit();
                $emailRes = \Mail::to($result)
                    // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
                ->cc('maulik.kanhasoft@gmail.com')
                ->bcc('suresh.kanhasoft@gmail.com')
                ->send(new \App\Mail\SendSmtpMail($details));
                return true;
            }
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
            'organization_shift.start_time',
            'organization_shift.end_time',
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

    public function getMatchByBooking($bookingId,$status)
    {
        $subQuery = Booking::select(
            'users.id as signeeId',
            'users.address_line_1',
            'users.address_line_2',
            'users.contact_number',
            'users.city',
            'users.first_name',
            'users.last_name',
            'users.email',
            'booking_matches.signee_booking_status',
            'signee_organization.status as compliance_status',
            'bookings.user_id as organization_id',
            'bookings.*',
        );
        $subQuery->Join('booking_matches',  'booking_matches.booking_id', '=', 'bookings.id');
        $subQuery->Join('users',  'users.id', '=', 'booking_matches.signee_id');
        $subQuery->Join('signee_organization',  'signee_organization.user_id', '=', 'users.id');
        if(Auth::user()->role == 'ORGANIZATION'){

            $subQuery->where('signee_organization.organization_id', Auth::user()->id);
        }else{
            //print_r(Auth::user()->role);exit;
            // $query->where('bookings.user_id',Auth::user()->id);
            $subQuery->where('signee_organization.organization_id', Auth::user()->parent_id);
        }

        //$subQuery->where('signee_organization.organization_id', Auth::user()->id);
        $subQuery->where('users.role', 'SIGNEE');
        $subQuery->where('bookings.id', $bookingId);
        $subQuery->where('booking_matches.signee_status',$status );
        $subQuery->where('booking_matches.deleted_at');
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
            'booking_matches.signee_id',
            'users.contact_number',
            'users.email',
            'users.postcode',
            'users.city',
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

    public function users(){
        return $this->hasOne(User::class,'id');
    }

    public function ward(){
        return $this->hasOne(Ward::class,'id');
    }

    public function shift(){
        return $this->hasOne(OrganizationShift::class,'id','shift_id');
    }

    public function hospital(){
        return $this->hasOne(Hospital::class,'id','hospital_id');
    }

    public function shiftType(){
        return $this->hasOne(ShiftType::class,'id','shift_type_id');
    }

    public function trust(){
        return $this->hasOne(Trust::class,'id','trust_id');
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
}
