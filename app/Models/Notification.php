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
        // print_r($postData);exit();
        //return true;
        //print_r($postData);exit;
        if($postData['status'] != 'COMPLIANT' && $postData['status'] != 'CREATED' && $postData['status'] != 'CANCEL' && $postData['status'] != 'CONFIRMED' )
        {
            // dd($postData['signee_booking_status']);
            $data = User::where('id', $postData['signeeId'])->first();
            $postData['organization_id'] = $data->parent_id;
            //print_r($postData);exit();
            $complientMsg = 'Your compliant status has been changed to '. $postData['status'];
        }
        else
        {
            $bookingDetails = Booking::findOrFail($postData['id']);
            //print_r($bookingDetails);exit();
            $date = date("d-m-Y", strtotime($bookingDetails['date']));
            $time = date("h:i A", strtotime($bookingDetails['start_time'])).' '.'To'.' '.date("h:i A", strtotime($bookingDetails['end_time']));
            // dd($postData['status']);
            //signee cancel his shift
            if($postData['signeeId'] == Auth::user()->id && $postData['status'] == "CREATED")
            {
                //echo "hi";exit();
                $msg = 'Your shift in'.' '.$postData['hospital_name'].' '.'hospital of'.' '.$postData['ward_name'].' '.'ward at '.$date.' '.$time.' '.'has been canceled';
            }
            elseif($postData['status'] == "CREATED")
            {
                $msg = 'Your shift in'.' '.$postData['hospital_name'].' '.'hospital of'.' '.$postData['ward_name'].' '.'ward at '.$date.' '.$time.' '.'has been created';
            }
            elseif(!isset($postData['signee_booking_status']) && $postData['status'] == "CONFIRMED")
            {
                //echo "456";exit;
                $msg = 'Your shift in'.' '.$postData['hospital_name'].' '.'hospital of'.' '.$postData['ward_name'].' '.'ward at '.$date.' '.$time.' '.'has been confirmed';
            }
            elseif(isset($postData['signee_booking_status']) == "CANCEL")
            {
               // echo "123";exit;
                // dd('hi');
                $msg = 'Your shift in'.' '.$postData['hospital_name'].' '.'hospital of'.' '.$postData['ward_name'].' '.'ward at '.$date.' '.$time.' '.'has been cancelled by admin';
            }
        }
        //print_r($postData['signee_booking_status'] ? $postData['signee_booking_status'] : $postData['status']);exit();

        $notification = new Notification();
        $notification->signee_id = $postData['signeeId'];
        $notification->organization_id = $postData['organization_id'];
        $notification->booking_id = isset($postData['id']) ? $postData['id'] : NULL;
        $notification->message = isset($msg) ? $msg :  $complientMsg;
        if(isset($postData['signee_booking_status'])){
            $notification->status=$postData['signee_booking_status'] ;
        }else{
            $notification->status=$postData['status'];
         }
        //  1 ?? 0
        // $notification->status =$postData['signee_booking_status']) ? $postData['signee_booking_status'] : $postData['status'];
        $notification->is_read = 0;
        $notification->is_sent = 0;
               // print_r($notification);exit();

        // print_r(isset($msg) ? "CREATED" : (isset($shiftConfirmedMsg) ? "CONFIRMED" : (isset($shiftCanceledMsg) ? "CANCELED" : (isset($complientMsg) ? $postData['status'] : "CANCELED"))));exit();

        $notification->save();
      //  print_r('123');exit;

        //$notification = '';
        return true;
    }
}
