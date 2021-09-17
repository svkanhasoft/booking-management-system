<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Config;
use Carbon\Carbon;

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
    protected $fillable = ['organization_id', 'signee_id', 'booking_id', 'trust_id', 'match_count', 'booking_date', 'booking_status', 'shift_id'];

    public function addBookingMatch($bookingArray, $bookingId)
    {
        $signeeidArray = array_column($bookingArray, 'signeeId');
        $objBookingMatchDelete = BookingMatch::where('booking_id', '=', $bookingId)->whereNotIn('signee_id', $signeeidArray)->delete();
        foreach ($bookingArray as $keys => $values) {
            // print_r($values);
            // exit;
            // $this->sendMatchEmail($values);
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
            $objBookingMatch->booking_status = 'OPEN';
            $objBookingMatch->save();
            $objBookingMatch = '';
        }
        return true;
    }

    public function editBookingMatchByUser($bookingArray, $bookingId)
    {

        $signeeidArray = array_column($bookingArray, 'signeeId');
        $objBookingMatchDelete = BookingMatch::where('booking_id', '=', $bookingId)->whereNotIn('signee_id', $signeeidArray)->delete();
        foreach ($bookingArray as $keys => $values) {
            // print_r($values);
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
            // $objBookingMatch->booking_status = 'OPEN';
            $objBookingMatch->save();
            $objBookingMatch = '';
        }
        return true;
    }

    public function sendMatchEmail($result)
    {
        if (isset($result) && !empty($result)) {
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'addMatch',
                'subject' => 'Booking Management System: Your match found',
                'data' => $result,
            ];
            $emailRes = \Mail::to($result['email'])
                // $emailRes = \Mail::to('shaileshv.kanhasoft@gmail.com')
                ->cc('shaileshv.kanhasoft@gmail.com')
                // ->bcc('suresh.kanhasoft@gmail.com')
                ->send(new \App\Mail\SendSmtpMail($details));
            return true;
        } else {
            return true;
        }
    }
    public function getShiftList()
    {

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
            DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $booking->leftJoin('booking_specialities',  'booking_specialities.booking_id', '=', 'bookings.id');
        $booking->Join('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $booking->leftJoin('trusts',  'trusts.id', '=', 'bookings.trust_id');
        $booking->leftJoin('hospitals',  'hospitals.id', '=', 'bookings.hospital_id');
        $booking->leftJoin('ward',  'ward.id', '=', 'bookings.ward_id');
        $booking->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id');
        $booking->leftJoin('shift_type',  'shift_type.id', '=', 'bookings.shift_type_id');
        $booking->where('bookings.status', 'OPEN');
        $booking->whereNull('bookings.deleted_at');
        $booking->groupBy('booking_specialities.booking_id');
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
        $booking->where('bookings.id', $id);
        $booking->groupBy('bookings.id');
        $res = $booking->first()->toArray();
        return $res;
    }
}
