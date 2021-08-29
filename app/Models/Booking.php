<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Http\Request;
use Config;
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
    protected $fillable = ['user_id', 'hospital_id','reference_id', 'trust_id', 'ward_id', 'shift_id', 'shift_type_id', 'date', 'grade_id', 'status', 'rate'];
    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

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
        $bookingList = $query->latest()->paginate($perPage);
        return $bookingList;
        // $bookingList = $query->get();
        //  print_r($bookingList);
        // exit;
        // $subArray = [];
        // foreach ($bookingList as $key => $booking) {

        //     $subArray[$key] = $booking;
        //     $subQuery = BookingSpeciality::select(
        //         'users.id',
        //         'booking_specialities.booking_id',
        //         'specialities.speciality_name',
        //         'signees_detail.candidate_id',
        //         'signees_detail.phone_number',
        //         'signees_detail.mobile_number',
        //         'users.address_line_1',
        //         'users.address_line_2',
        //         'users.city',
        //         'users.postcode',
        //         'signees_detail.date_of_birth',
        //         'signees_detail.nationality',
        //         'signees_detail.candidate_referred_from',
        //         'signees_detail.date_registered',
        //         'signees_detail.cv',
        //         'signees_detail.nmc_dmc_pin',
        //         DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
        //         DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
        //         DB::raw('GROUP_CONCAT(DISTINCT specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        //         DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        //     );
        //     $subQuery->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        //     $subQuery->Join('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
        //     $subQuery->Join('users',  'users.id', '=', 'signee_speciality.user_id');
        //     $subQuery->Join('signees_detail',  'signees_detail.user_id', '=', 'users.id');

        //     $subQuery->where('booking_specialities.booking_id', $booking['id']);
        //     $subQuery->groupBy('users.id');
        //     $subQuery->orderBy('signeeBookingCount', 'DESC');
        //     $res = $subQuery->get()->toArray();
        //     $subArray[$key]['user'] = $res;
        // }
        // return $subArray;
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
        // $bookingList = $query->get();
        //  print_r($bookingList);
        // exit;
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
                'signees_detail.cv',
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
            // // $subQuery = BookingSpeciality::select(
            'users.email',
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
        $subQuery->where('bookings.id', $matchiId);
        $subQuery->whereNull('signee_speciality.deleted_at');
        $subQuery->whereNull('booking_specialities.deleted_at');
        $subQuery->whereNull('bookings.deleted_at');
        $subQuery->groupBy('users.id');
        // $subQuery->groupBy('specialities.id');
        $subQuery->orderBy('signeeBookingCount', 'DESC');
        $res = $subQuery->get()->toArray();
        // $subQuery->join('kg_shops', function ($join) {
        //     $join->on('kg_shops.id', '=', 'kg_feeds.shop_id');
        // });
        return $res;
    }

    public function editMetchBySigneeId($signeeId = null)
    {

        $subQuery = Booking::select(
            'users.id as signeeId',
            'bookings.id as booking_id',
            'users.role',
            'bookings.user_id as organization_id',
            'bookings.*',
            DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
            DB::raw('COUNT(signee_speciality.speciality_id)  as signeeBookingCount'),
            DB::raw('GROUP_CONCAT(signee_speciality.id SEPARATOR ", ") AS signeeSpecialityId'),
            DB::raw('GROUP_CONCAT(booking_specialities.id SEPARATOR ", ") AS bookingSpecialityId'),
            DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
            DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
        );
        
        $subQuery->Join('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $subQuery->Join('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
        $subQuery->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $subQuery->Join('users',  'users.id', '=', 'signee_speciality.user_id');
        $subQuery->where('users.id', $signeeId);
        $subQuery->whereNull('signee_speciality.deleted_at');
        $subQuery->whereNull('booking_specialities.deleted_at');
        $subQuery->whereNull('bookings.deleted_at');
        $subQuery->groupBy('bookings.id');
        $subQuery->orderBy('signeeBookingCount', 'DESC');
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
        //  print_r($bookingList);
        // exit;
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
                'signees_detail.cv',
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
}
