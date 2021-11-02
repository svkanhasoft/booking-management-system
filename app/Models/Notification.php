<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notifications';

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
    protected $fillable = ['user_id', 'text', 'is_read'];

    public function addNotification($bookingId, $userId)
    {
        $notification = new Notification();
        $booking = Booking::findOrFail($bookingId);
        if($booking['status'] == "CREATED")
        {
            $notification->user_id = $userId;
            $notification->text = "Shift Created Successfully";
            $notification->save();
        }
        else if($booking['status'] == "CONFIRMED")
        {
            $notification->user_id = $userId;
            $notification->text = "Shift Confirmed Successfully";
            $notification->save();
        }
    }
}
