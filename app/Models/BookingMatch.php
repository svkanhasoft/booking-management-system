<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        $objBookingMatchDelete = BookingMatch::where('booking_id', '=' , $bookingId)->whereNotIn('signee_id', $signeeidArray)->delete();
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
}
