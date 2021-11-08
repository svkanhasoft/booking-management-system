<?php

namespace App\Models;

use Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class Notification extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notification';

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
    protected $fillable = ['signee_id', 'organization_id', 'booking_id', 'message', 'status', 'is_read', 'is_sent'];

    public function addNotification($postData)
    {
        //print_r($postData);exit();

        if($postData['status'] != 'COMPLIANT')
        {
            $data = User::where('id', $postData['signee_id'])->first();
            $postData['organization_id'] = $data->parent_id;
            //print_r($postData);exit();
            $complientMsg = 'Your compliant status has been changed to '. $postData['status'];
        }

        $bookingDetails = Booking::findOrFail($postData['id']);
        //print_r($bookingDetails);exit();
        $date = date("d-m-Y", strtotime($bookingDetails['date']));
        $time = date("h:i A", strtotime($bookingDetails['start_time'])).' '.'To'.' '.date("h:i A", strtotime($bookingDetails['end_time']));

        //signee cancel his shift
        if($postData['signeeId'] == Auth::user()->id && $postData['status'] == "CREATED")
        {
            //echo "hi";exit();
            $signeeShiftCancelMsg = 'Your shift in'.' '.$postData['hospital_name'].' '.'hospital of'.' '.$postData['ward_name'].' '.'ward at '.$date.' '.$time.' '.'has been canceled';
        }
        elseif($bookingDetails['status'] == "CREATED")
        {
            $shiftCreatedMsg = 'Your shift in'.' '.$postData['hospital_name'].' '.'hospital of'.' '.$postData['ward_name'].' '.'ward at '.$date.' '.$time.' '.'has been created';
        }
        elseif($bookingDetails['status'] == "CONFIRMED")
        {
            $shiftConfirmedMsg = 'Your shift in'.' '.$postData['hospital_name'].' '.'hospital of'.' '.$postData['ward_name'].' '.'ward at '.$date.' '.$time.' '.'has been confirmed';
        }
        elseif($bookingDetails['status'] == "CANCEL")
        {
            $shiftCanceledMsg = 'Your shift in'.' '.$postData['hospital_name'].' '.'hospital of'.' '.$postData['ward_name'].' '.'ward at '.$date.' '.$time.' '.'has been canceled by admin';
        }

        $notification = new Notification();
        $notification->signee_id = $postData['signeeId'];
        $notification->organization_id = $postData['organization_id'];
        $notification->booking_id = isset($postData['id']) ? $postData['id'] : 0;
        $notification->message = isset($shiftCreatedMsg) ? $shiftCreatedMsg : (isset($shiftConfirmedMsg) ? $shiftConfirmedMsg : (isset($shiftCanceledMsg) ? $shiftCanceledMsg : (isset($signeeShiftCancelMsg) ? $signeeShiftCancelMsg : $complientMsg)));
        $notification->status = isset($shiftCreatedMsg) ? "CREATED" : (isset($shiftConfirmedMsg) ? "CONFIRMED" : (isset($shiftCanceledMsg) ? "CANCELED" : (isset($complientMsg) ? $postData['status'] : "CANCELED")));
        $notification->is_read = 0;
        $notification->is_sent = 0;
        $notification->save();
        $notification = '';
    }
}
