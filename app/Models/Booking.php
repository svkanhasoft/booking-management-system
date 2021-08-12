<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

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
    protected $fillable = ['user_id', 'reference_id', 'trust_id', 'ward_id', 'shift_id', 'date', 'grade_id','rate'];
    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

    public function getBooking($bookingId = null)
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

    public function getBookingByFilter($status = null)
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
        $bookingList = $query->get();
        //  print_r($bookingList);
        // exit;
        $subArray = [];
        foreach ($bookingList as $key => $booking) {

            $subArray[$key] = $booking;
            $subQuery = BookingSpeciality::select(
                'users.id','booking_specialities.booking_id','specialities.speciality_name',
                'signees_detail.candidate_id','signees_detail.phone_number',
                'signees_detail.mobile_number','users.address_line_1',
                'users.address_line_2',
                // 'users.address_line_3',
                'users.city','users.postcode','signees_detail.date_of_birth',
                'signees_detail.nationality','signees_detail.candidate_referred_from',
                'signees_detail.date_registered','signees_detail.cv','signees_detail.nmc_dmc_pin',
                DB::raw('COUNT(booking_specialities.id)  as bookingCount'),
                DB::raw('COUNT(signee_speciality.id)  as signeeBookingCount'),
                DB::raw('GROUP_CONCAT(DISTINCT specialities.speciality_name SEPARATOR ", ") AS speciality'),
                DB::raw('CONCAT(users.first_name," ", users.last_name) AS user_name'),
            );
            $subQuery->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
            $subQuery->Join('signee_speciality',  'signee_speciality.speciality_id', '=', 'booking_specialities.speciality_id');
            $subQuery->Join('users',  'users.id', '=', 'signee_speciality.user_id');
            $subQuery->Join('signees_detail',  'signees_detail.user_id', '=', 'users.id');
            $subQuery->where('booking_specialities.booking_id', $booking['id']);
            $subQuery->groupBy('users.id');
            $subQuery->orderBy('signeeBookingCount','DESC');
            // $subQuery->groupBy('booking_specialities.booking_id');
            $res = $subQuery->get()->toArray();
            $subArray[$key]['user'] = $res;
            // print_r( $bookingList);
            // exit;
        }
        // print_r($subArray);exit;
        return $subArray;
    }
}
