<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingSpeciality extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'booking_specialities';

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
    protected $fillable = ['booking_id', 'speciality_id'];
    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

    function addSpeciality($postData, $bookingId, $isDelete = false)
    {
            BookingSpeciality::where('booking_id', '=', $bookingId)->whereNotIn('speciality_id', $postData)->delete();
            foreach ($postData as $key => $val) {
                $objBookingSpeciality = BookingSpeciality::where(['speciality_id' => $val, 'booking_id' => $bookingId])->firstOrNew();
                $objBookingSpeciality->booking_id = $bookingId;
                $objBookingSpeciality->speciality_id = $val;
                $objBookingSpeciality->save();
                $objBookingSpeciality = '';
                // BookingSpeciality::where(['booking_id' => $bookingId])->delete();
            }
            return true;
    }

    public function getBookingSpeciality($bookingId = null)
    {
        $query = BookingSpeciality::select(
            'booking_specialities.booking_id',
            'booking_specialities.speciality_id',
            'specialities.speciality_name',
        );
        $query->leftJoin('specialities',  'specialities.id', '=', 'booking_specialities.speciality_id');
        $query->where('booking_specialities.booking_id', $bookingId);
        return $query->get();
    }
}
