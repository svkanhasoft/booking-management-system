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
       //print_r($postData);exit;
        //print_r($postData['signee_booking_status']);exit;
        // echo $postData['signeeId'] . " signe <br/>";
        // echo Auth::user()->id . " login user <br/>";
        // echo $postData['signee_booking_status'] . " signee_booking_status <br/>";
        // exit;
        $msg = '';
        if (isset($postData['status']) && $postData['status'] != 'CREATED' && $postData['status'] != 'CONFIRMED' && $postData['status'] != 'CANCEL' && $postData['status'] != 'Active') {
            // echo "home";exit;
            // dd($postData['signee_booking_status']);
            //$data = SigneeOrganization::where(['user_id'=> $postData['signeeId'], 'organization_id'=> Auth::user()->id])->first();
            if(Auth::user()->role == 'ORGANIZATION')
            {
                $postData['organization_id'] = Auth::user()->id;
            }else{
                $postData['organization_id'] = Auth::user()->parent_id;
            }

            //print_r($postData);exit();
            $msg = 'Your compliant status has been changed to ' . $postData['status'];
        } else {
            //echo "in else ";exit;
            if((isset($postData['role']) && $postData['role'] == 'SIGNEE') || (isset($postData['org_role']) && $postData['org_role'] != 'ORGANIZATION'))
            {
                //echo "in if";exit;
                $bookingDetails = Booking::findOrFail($postData['booking_id']);
                $date = date("d-m-Y", strtotime($bookingDetails['date']));
                $time = date("h:i A", strtotime($bookingDetails['start_time'])) . ' ' . 'To' . ' ' . date("h:i A", strtotime($bookingDetails['end_time']));

                //signee reject shift
                if ((isset($postData['signeeId']) && isset($postData['signee_booking_status'])) && $postData['signeeId'] == Auth::user()->id && $postData['signee_booking_status'] == "CANCEL") {
                    //echo "hi";exit;
                    $msg = 'You reject shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward';
                } else  if (isset($postData['signeeId']) && isset($postData['signee_booking_status']) && $postData['signeeId'] == Auth::user()->id && $postData['signee_booking_status'] == "ACCEPT") { //signee accepts shift
                    //echo "123";exit;
                    $msg = 'You accepted shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward sent by admin';
                } else if (isset($postData['signee_booking_status']) && isset($postData['organization_id']) && $postData['signee_booking_status'] == "CANCEL" && $postData['organization_id'] == Auth::user()->id) { //Staff/Org reject shift
                    //echo "456";exit;
                    $msg = 'Your shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward at ' . $date . ' ' . $time . ' ' . 'has been rejected by admin';
                } elseif (isset($postData['signee_booking_status']) && isset($postData['organization_id']) && $postData['organization_id'] == Auth::user()->id && $postData['signee_booking_status'] == "OFFER") {
                    //echo "933";exit;
                    $msg = 'You got offer from' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward';
                } else if ($postData['signeeId'] == Auth::user()->id && $postData['signee_booking_status'] && $postData['signee_booking_status'] == "CANCEL") {
                    //echo "333";exit;
                    $msg = 'Your shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward at ' . $date . ' ' . $time . ' ' . 'has been canceled';
                } else  if ($postData['status'] == "CREATED" && ($postData['signee_booking_status'] == '' || $postData['signee_booking_status'] == 'PENDING')) {
                    //echo "666";exit;
                    $msg = 'Your shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward at ' . $date . ' ' . $time . ' ' . 'has been created';
                } else if (isset($postData['signee_booking_status']) && $postData['signee_booking_status'] == "CONFIRMED") {
                    //echo "712";exit;
                    $msg = 'Your shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward at ' . $date . ' ' . $time . ' ' . 'has been confirmed';
                }else if (isset($postData['signee_booking_status']) && isset($postData['role']) && $postData['signee_booking_status'] === "APPLY" && $postData['role'] === 'SIGNEE') {
                    //echo "748";exit;
                    $msg = 'Your have applied in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward';
                }
            } else if (isset($postData['role']) && $postData['role'] === 'ORGANIZATION' && isset($postData['signee_booking_status']) && $postData['signee_booking_status'] == "ACCEPT") {
                //echo "789";exit;
                $msg = 'You have applied in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward has been accepted by signee';
            } else if (isset($postData['signee_booking_status']) && isset($postData['role']) && $postData['role'] == 'SIGNEE' && $postData['signee_booking_status'] == "CANCEL") {
                //echo "963";exit;
                $msg = 'Your shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward has been rejected by signee';
            } else if (isset($postData['signee_booking_status']) && isset($postData['role']) && $postData['role'] == 'ORGANIZATION' && $postData['signee_booking_status'] == "APPLY") {
               // echo "159";exit;
                $msg = 'Signee applied in your shift' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward added by you';
            } else if (isset($postData['org_role']) && $postData['org_role'] == 'ORGANIZATION' && isset($postData['status']) && $postData['status'] === 'CREATED') {
                //echo "458";exit;
                $msg = 'You created a new shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward';
            } else if (isset($postData['org_role']) && $postData['org_role'] == 'ORGANIZATION' && isset($postData['status']) && $postData['status'] === 'CONFIRMED' && isset($postData['signee_booking_status']) && $postData['signee_booking_status'] === 'CONFIRMED') {
                //echo "797979";exit;
                $msg = 'Your shift is confirmed in ' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward';
            }
        }
        //echo "dfgfdgfdgdfg";exit;
        $notification = new Notification();
        $notification->signee_id = isset($postData['signeeId']) ? $postData['signeeId'] : NULL;
        $notification->organization_id = isset($postData['organization_id']) ? $postData['organization_id'] : $postData['signee_org_id'];
        // $notification->booking_id = (isset($postData['id']) || isset($postData['booking_id'])) ? $postData['id'] : NULL;
        if(isset($postData['role']) || isset($postData['org_role']) && ($postData['role'] == 'ORGANIZATION' || $postData['org_role'] == 'ORGANIZATION')){
            $notification->booking_id = $postData['booking_id'];
        }else if(isset($postData['id']) && $postData['id']){
            $notification->booking_id = $postData['id'];
        }else{
            $notification->booking_id = NULL;
        }
        $notification->message = $msg;
        if (isset($postData['signee_booking_status'])) {
            $notification->status = $postData['signee_booking_status'];
        } else {
            $notification->status = $postData['status'];
        }
        $notification->is_read = 0;
        $notification->is_sent = 0;
        $notification->save();
        //print_r($notification);exit;
        return true;
    }

    public static function sendAndroidNotification($message, $token, $orderId)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $token = $token;
        $serverKey = 'AAAAQTG-TuM:APA91bGshsDaQvEHRTxNG8ikpOjIgPhaq6BTIIjQ0TECZ_aRfY59w3-AAT8msqeleYNtfBdt1Q2eS1X_KXqSGtp9AfPZ8ud4wkltowSnxnIrym3UiOAVIEZzDM7VCwUaUelaYQn58ZkR';
        $title = "Pluto";
        $customData = array('orderID' => $orderId, 'title' => $title, 'text' => $message, 'sound' => 'default', 'badge' => '1');
        $notification = array('title' => $title, 'text' => $message, 'orderId' => $orderId, 'sound' => 'default', 'badge' => '1');
        $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high');
        // $arrayToSend = array('to' => $token, 'data' => $customData, 'notification' => $notification, 'priority' => 'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER,false);
        // curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        // $response = curl_exec($ch);
        // $result = json_encode($response);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $response = curl_exec($ch);
        if ($response === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        } else {
            return $response;
        }
        curl_close($ch);
    }

    public static function sendIOSNotification($message, $token, $orderId)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $token = $token;
        $serverKey = 'AAAAQTG-TuM:APA91bGshsDaQvEHRTxNG8ikpOjIgPhaq6BTIIjQ0TECZ_aRfY59w3-AAT8msqeleYNtfBdt1Q2eS1X_KXqSGtp9AfPZ8ud4wkltowSnxnIrym3UiOAVIEZzDM7VCwUaUelaYQn58ZkR';
        $title = "Pluto";
        $body = $message;
        $notification = array('title' => $title, 'text' => $body, 'orderId' => $orderId, 'sound' => 'default', 'badge' => '1');
        $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === FALSE) {
            // die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        $reeed = json_decode($response);
        return $reeed->success;
    }
}
