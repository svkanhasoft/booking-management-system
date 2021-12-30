<?php

namespace App\Models;

use Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
    protected $fillable = ['signee_id', 'organization_id', 'booking_id', 'message', 'status', 'is_read', 'is_sent', 'is_showing_for', 'created_by', 'updated_by'];

    public function addNotification($postData)
    {
        //print_r($postData);exit;
        //print_r($postData['signee_booking_status']);exit;
        // echo $postData['signeeId'] . " signe <br/>";
        // echo Auth::user()->id . " login user <br/>";
        // echo $postData['signee_booking_status'] . " signee_booking_status <br/>";
        // exit;
        $msg = '';
        $bookingDetails = Booking::findOrFail($postData['booking_id']);

        $date = date("d-m-Y", strtotime($bookingDetails['date']));
        if (isset($postData['status']) && $postData['status'] != 'CREATED' && $postData['status'] != 'CONFIRMED' && $postData['status'] != 'CANCEL' && $postData['status'] != 'Active') {
            // echo "home";exit;
            // dd($postData['signee_booking_status']);
            //$data = SigneeOrganization::where(['user_id'=> $postData['signeeId'], 'organization_id'=> Auth::user()->id])->first();
            if (Auth::user()->role == 'ORGANIZATION') {
                $postData['organization_id'] = Auth::user()->id;
            } else {
                $postData['organization_id'] = Auth::user()->parent_id;
            }
            $msg = 'Your profile status has been changed to ' . $postData['status'];
        } else {
            // print_r($postData);
            // exit;
            if ((isset($postData['role']) && $postData['role'] == 'SIGNEE') || (isset($postData['org_role']) && $postData['org_role'] != 'ORGANIZATION')) {

                // $bookingDetails = Booking::findOrFail($postData['booking_id']);

                // $date = date("d-m-Y", strtotime($bookingDetails['date']));
                $time = date("h:i A", strtotime($bookingDetails['start_time'])) . ' ' . 'To' . ' ' . date("h:i A", strtotime($bookingDetails['end_time']));
                //Candidate reject shift
                if ((isset($postData['signeeId']) && isset($postData['signee_booking_status'])) && $postData['signeeId'] == Auth::user()->id && $postData['signee_booking_status'] == "CANCEL") {
                    $msg = $postData['first_name'] . ' '. $postData['last_name']. ' rejected your shift offer for' . ' ' . $postData['hospital_name'] .' '.'hospital ('. $postData['ward_name'] .' '.'ward ) on the day of '. $date;
                } else  if (isset($postData['signeeId']) && isset($postData['signee_booking_status']) && $postData['signeeId'] == Auth::user()->id && $postData['signee_booking_status'] == "ACCEPT") { //Candidate accepts shift
                    $msg = 'You accepted shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward sent by admin';
                } else if (isset($postData['signee_booking_status']) && isset($postData['organization_id']) && $postData['signee_booking_status'] == "CANCEL" && $postData['organization_id'] == Auth::user()->id) { //Staff/Org reject shift
                    $msg = 'Shift you applied in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward at ' . $date . ' ' . $time . ' ' . 'has been rejected by admin';
                } elseif (isset($postData['signee_booking_status']) && isset($postData['organization_id']) && $postData['signee_booking_status'] == "OFFER") {
                    $msg = 'You got offer from' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward by admin';
                } else if ($postData['signeeId'] == Auth::user()->id && $postData['signee_booking_status'] && $postData['signee_booking_status'] == "CANCEL") {
                    $msg = 'Shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ward at ' . $date . ' ' . $time . ' ' . 'has been canceled';
                } else if (($postData['status'] == "CONFIRMED" || $postData['status'] == "CREATED") && isset($postData['updated_by']) && $postData['updated_by'] != '') {
                    $msg = 'Shift ' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward at ' . $date . ' ' . $time . ' ' . 'has been updated by admin';
                } else  if ($postData['status'] == "CREATED" && ($postData['signee_booking_status'] == '' || $postData['signee_booking_status'] == 'PENDING')) {
                    $msg = 'Shift ' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward at ' . $date . ' ' . $time . ' ' . 'has been created by admin';
                } else if (isset($postData['signee_booking_status']) && $postData['signee_booking_status'] == "CONFIRMED") {
                    $msg = 'Shift you applied in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward at ' . $date . ' ' . $time . ' ' . 'has been confirmed';
                } else if (isset($postData['signee_booking_status']) && isset($postData['role']) && $postData['signee_booking_status'] === "APPLY" && $postData['role'] === 'SIGNEE') {
                    $msg = 'Your have applied in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital of' . ' ' . $postData['ward_name'] . ' ' . 'ward';
                }
            } else if (isset($postData['role']) && $postData['role'] === 'ORGANIZATION' && isset($postData['signee_booking_status']) && $postData['signee_booking_status'] == "ACCEPT") {
                $msg = $postData['user_name'] . ' ' . 'accepted your offer at' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward created by you';
            } else if (isset($postData['signee_booking_status']) && isset($postData['signeeId']) && $postData['signeeId'] == Auth::user()->id && $postData['signee_booking_status'] == "CANCEL") {
                $msg = $postData['user_name'] . ' ' . 'rejected your offer at' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward created by you';
            } else if (isset($postData['signee_booking_status']) && isset($postData['role']) && $postData['role'] == 'ORGANIZATION' && $postData['signee_booking_status'] == "APPLY") {
                $msg = $postData['user_name'] . ' ' . 'applied in your shift' . ' ' . $postData['hospital_name'] . ' ' . 'hospital ('. $postData['ward_name'] . ' ' . 'ward) on the day of'.' '. $date;
            } else if (isset($postData['org_role']) && $postData['org_role'] == 'ORGANIZATION' && isset($postData['status']) && $postData['status'] === 'CREATED') {
                $msg = 'You created a new shift in' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward';
            } else if (isset($postData['org_role']) && $postData['org_role'] == 'ORGANIZATION' && isset($postData['status']) && $postData['status'] === 'CONFIRMED' && isset($postData['signee_booking_status']) && $postData['signee_booking_status'] === 'CONFIRMED') {
                $msg = 'Your shift is confirmed in ' . ' ' . $postData['hospital_name'] . ' ' . 'hospital in' . ' ' . $postData['ward_name'] . ' ' . 'ward';
            }
        }

        if (!empty($postData['signeeId']) && Auth::user()->role !== 'SIGNEE') {
            $userResult = User::find($postData['signeeId']);
            $bookingId = '';
            $status = '';
            if (isset($postData['role']) || isset($postData['org_role']) && ($postData['role'] == 'ORGANIZATION' || $postData['org_role'] == 'ORGANIZATION')) {
                $bookingId = $postData['booking_id'];
            } else if (isset($postData['id']) && $postData['id']) {
                $bookingId = $postData['id'];
            }

            if (isset($postData['signee_booking_status'])) {
                $status = $postData['signee_booking_status'];
            } else {
                $status = $postData['status'];
            }
            if ($userResult->device_id != '' && $userResult->platform == 'Android') {
                $this->sendAndroidNotification($msg, $userResult->device_id, $bookingId, $status);
            } else if ($userResult->device_id != '' && $userResult->platform == 'Iphone') {
                $this->sendIOSNotification($msg, $userResult->device_id, $bookingId, $status);
            }
        }

        $notification = new Notification();
        $notification->signee_id = isset($postData['signeeId']) ? $postData['signeeId'] : NULL;
        $notification->organization_id = isset($postData['organization_id']) ? $postData['organization_id'] : $postData['id'];
        // $notification->booking_id = (isset($postData['id']) || isset($postData['booking_id'])) ? $postData['id'] : NULL;
        if (isset($postData['role']) || isset($postData['org_role']) && ($postData['role'] == 'ORGANIZATION' || $postData['org_role'] == 'ORGANIZATION')) {
            $notification->booking_id = $postData['booking_id'];
        } else if (isset($postData['id']) && $postData['id']) {
            $notification->booking_id = $postData['id'];
        } else {
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
        $notification->created_by = Auth::user()->id;

        if (Auth::user()->role == 'SIGNEE') {
        // if ((isset($postData['role']) && $postData['role'] == "ORGANIZATION")) {
            $notification->is_showing_for = "ORGANIZATION";
        } else {
            $notification->is_showing_for = "SIGNEE";
        }
        $notification->save();
        //print_r($notification);exit;
        return true;
    }

    public function sendAndroidNotification($message, $token, $bookingId, $status)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $token = $token;
        $serverKey = 'AAAAQTG-TuM:APA91bGshsDaQvEHRTxNG8ikpOjIgPhaq6BTIIjQ0TECZ_aRfY59w3-AAT8msqeleYNtfBdt1Q2eS1X_KXqSGtp9AfPZ8ud4wkltowSnxnIrym3UiOAVIEZzDM7VCwUaUelaYQn58ZkR';
        $title = "Pluto";
        $customData = array('bookingId' => $bookingId, 'status' => $status, 'title' => $title, 'text' => $message, 'sound' => 'default', 'badge' => '1');
        $notification = array('title' => $title, 'text' => $message, 'sound' => 'default', 'badge' => '1');
        // $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high');
        $arrayToSend = array('to' => $token, 'data' => $customData, 'notification' => $notification, 'priority' => 'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;

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

    public function sendIOSNotification($message, $token, $bookingId, $status)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $token = $token;
        $serverKey = 'AAAAQTG-TuM:APA91bGshsDaQvEHRTxNG8ikpOjIgPhaq6BTIIjQ0TECZ_aRfY59w3-AAT8msqeleYNtfBdt1Q2eS1X_KXqSGtp9AfPZ8ud4wkltowSnxnIrym3UiOAVIEZzDM7VCwUaUelaYQn58ZkR';
        $title = "Pluto";
        $body = $message;
        $notification = array('title' => $title, 'status' => $status, 'bookingId' => $bookingId, 'subtitle' => $message, 'body' => $body, 'sound' => 'default', 'badge' => '1');
        // $notification = array('title' => $title,'bookingId' => $bookingId,'subtitle' => $message, 'body' => array('bookingId'=>$bookingId,"message"=>$body), 'sound' => 'default', 'badge' => '1');
        // $arrayToSend = array('data' => $notification,'body' => $notification, 'notification' => $notification, 'priority' => 'high');
        $arrayToSend = array('to' => $token, 'subtitle' => $message, 'data' => $notification, 'body' => $notification, 'notification' => $notification, 'priority' => 'high');
        $json = json_encode($arrayToSend);

        // $notification = array('title' => $title,'bookingId' => $bookingId,'subtitle' => $message, 'body' => array('bookingId'=>$bookingId,"message"=>$body), 'sound' => 'default', 'badge' => '1');
        //  //         $notification = array('title' => $title,'bookingId' => $bookingId,'subtitle' => $message, 'body' => array('bookingId'=>$bookingId,"message"=>$body), 'sound' => 'default', 'badge' => '1');
        //           $arrayToSend = array('to' => $token, 'subtitle' => $message,'data' => $notification,'body' => $notification, 'notification' => $notification, 'priority' => 'high');
        //          $json = json_encode($arrayToSend);

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
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        $reeed = json_decode($response);
        return $reeed->success;
    }

    public function addNotificationV2($postData, $type,$key = '')
    {
        //print_r($postData);exit;
        $signeeId = null;
        if (isset($postData['signeeId']) && !empty($postData['signeeId'])) {
            $signeeId = $postData['signeeId'];
        } else if (isset($postData['signee_id']) && !empty($postData['signee_id'])) {
            $signeeId = $postData['signee_id'];
        }
        $bookingDetails = [];
        if(isset($postData['booking_id']) && !empty($postData['booking_id'])){
            $bookingDetails = Booking::findOrFail($postData['booking_id']);
            $date = date("d-m-Y", strtotime($bookingDetails['date']));
        }
        // dd($bookingDetails);



        $msg = '';
        if ($type == 'payment') {
            $msg = 'Your booking ' . $bookingDetails['reference_id'] . ' payment status has been changed to ' . ' ' . $postData['payment_status'];
        } else if ($type == 'REJECTED') {//staff / org rejected shift applied by candidate
            //$msg = 'Shift you applied in ' . $bookingDetails['reference_id']. ' for date ' . $date . ' has been rejected ';
            $msg = 'Shift you applied in'.' '. $postData['hospital_name'] . ' hospital (' . $postData['ward_name'] . ') ward has been rejected by admin';
        } else if ($type == 'DOCS') {
            // $docsContent = array
            // (array('key'=>$postData['key'],'name'=>'Copy of Passport in Colour including front cover.'),
            // );
            if($postData['document_status'] == 'SUCCESS'){
                $customeDocsMsg = 'Accepted';
            }else if($postData['document_status'] == 'PENDING'){
                $customeDocsMsg = 'Pending';
            }else{
                $customeDocsMsg = 'Rejected';
            }
            $msg = 'Your '.str_replace("_"," ",$postData['key']). " document status has been changed to $customeDocsMsg";
        } else if ($type == 'shift_edit'){ //Notification for shift edit
            $msg = 'Shift ' . ' ' . $postData['hospital_name'] . ' ' . 'hospital ('.$postData['ward_name']. ') ward has been updated by admin';
        } else if ($type == 'shift_create'){ //Notification for shift create
            $msg = 'Shift ' . ' ' . $postData['hospital_name'] . ' ' . 'hospital ('.$postData['ward_name']. ') ward has been created by admin';
        } else if ($type == 'shift_accept'){ //Notification for shift accept by candidate
            $msg = $postData['user_name'] . ' ' . 'accepted your shift offer for' . ' ' . $postData['hospital_name'] .' '.'hospital ('. $postData['ward_name'] .' '.'ward) on the day of '. $date;
        } else if ($type == 'invite_candidate'){ //Notification for staff or org invite candidate for shift
            $msg = 'Admin invited you for the shift '. $postData['hospital_name'] .' '.'hospital ('. $postData['ward_name'] .' '.'ward) on the day of '. $date;
        } else if ($type == 'super_assign'){ //Notification for staff or org super assign any candidate
            $msg = 'Admin has assigned shift of '. $postData['hospital_name'] .' '.'hospital ('. $postData['ward_name'] .' '.'ward) on the day of '. $date . ' to you';
        // } else if ($type == 'shift_confirm'){ //Notification for staff or org confirmed any shift
        //     $msg = 'Shift '. $postData['reference_id'] .' status has been changed to '.$postData['status'];
        // }

        if (!empty($signeeId) && Auth::user()->role !== 'SIGNEE') {
            $userResult = User::find($signeeId);
            $bookingId = '';
            $status = $type;
            if (isset($postData['booking_id']) && $postData['booking_id']) {
                $bookingId = $postData['booking_id'];
            } else {
                $bookingId = NULL;
            }
            if ($userResult->device_id != '' && $userResult->platform == 'Android') {
                $this->sendAndroidNotification($msg, $userResult->device_id, $bookingId, $status);
            } else if ($userResult->device_id != '' && $userResult->platform == 'Iphone') {
                $this->sendIOSNotification($msg, $userResult->device_id, $bookingId, $status);
            }
        }

        $notification = new Notification();
        $notification->signee_id = $signeeId;
        //$notification->organization_id = Auth::user()->id;
        $notification->organization_id = $postData['organization_id'];
        if (isset($postData['booking_id']) && $postData['booking_id']) {
            $notification->booking_id = $postData['booking_id'];
        } else {
            $notification->booking_id = NULL;
        }
        $notification->message = $msg;
        $notification->status = $type;
        $notification->is_read = 0;
        $notification->is_sent = 0;
        $notification->created_by = Auth::user()->id;
        $notification->updated_by = Auth::user()->id;
        if (Auth::user()->role == 'SIGNEE') {
            // if ((isset($postData['role']) && $postData['role'] == "ORGANIZATION")) {
            $notification->is_showing_for = "ORGANIZATION";
        } else {
            $notification->is_showing_for = "SIGNEE";
        }
        //$notification->is_showing_for = "SIGNEE";
        // print_r($notification);exit;
        $notification->save();
        return true;
    }
}
