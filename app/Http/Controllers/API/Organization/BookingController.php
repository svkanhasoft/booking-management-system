<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests;
use App\Models\User;
use Hash;
use App\Models\Booking;
use App\Models\Ward;
use App\Models\Grade;
use App\Models\BookingMatch;
use App\Models\BookingSpeciality;
use App\Models\Hospital;
use App\Models\Notification;
use App\Models\OrganizationShift;
use DB;

use function PHPUnit\Framework\isNull;

//use App\Storage;

class BookingController extends Controller
{
    public $successStatus = 200;
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    protected $userId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;
            return $next($request);
        });
    }

    /**
     * Used to create new booking/shifts
     *
     * @return \Illuminate\View\View
     */
    public function add(Request $request)
    {
        // echo "hi";exit;
        $validator = Validator::make($request->all(), [
            'reference_id' => 'unique:bookings,reference_id,NULL,id,user_id,' . $this->userId,
            // 'reference_id' => 'required',
            'trust_id' => 'required',
            'ward_id' => 'required',
            'grade_id' => 'required',
            'date' => 'required',
            'shift_id' => 'required',
            'hospital_id' => 'required',
            'shift_type_id' => 'required',
            'speciality' => 'required:speciality,[]',
            'rate' => 'required',
            'commission' => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $requestData = $request->all();

            $shift = OrganizationShift::where('id', $requestData['shift_id'])->first();
            //print_r($shift);exit();
            // print_r($requestData);exit();
            if ($requestData['date'] < date('Y-m-d')) {
                return response()->json(['message' => 'booking date must be greater then or equal to today\'s date', 'status' => false], 200);
            }
            if (Auth::user()->role == 'ORGANIZATION') {
                $requestData['user_id'] = Auth::user()->id;
                $requestData['created_by'] = Auth::user()->id;
            } else {
                $requestData['staff_id'] = Auth::user()->id;
                $requestData['user_id'] = Auth::user()->parent_id;
                $requestData['created_by'] = Auth::user()->id;
            }
            $requestData['start_time'] = $shift['start_time'];
            $requestData['end_time'] = $shift['end_time'];
            $bookingCreated = Booking::create($requestData);
            if ($bookingCreated) {
                $objBookingSpeciality = new BookingSpeciality();
                $objBookingSpeciality->addSpeciality($requestData['speciality'], $bookingCreated['id'], false);

                $objBooking = new Booking();
                $bookings = $objBooking->getMetchByBookingId($bookingCreated['id']);
                //print_r($bookings);exit;
                $objBookingMatch = new BookingMatch();
                $bookingMatch = $objBookingMatch->addBookingMatch($bookings, $bookingCreated['id']);

                //$orgDetail = User::where('id', $requestData['user_id'])->first()->toArray();
                //print_r($orgDetail);exit;
                //$comArray = array_merge($signeeMatch->toArray(), $orgDetail);

                return response()->json(['status' => true, 'message' => 'Booking added Successfully', 'data' => $bookingCreated], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Booking added failed!', 'status' => false], 409); //424 failed
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);   //400 not found
        }
    }


    /**
     * Used to show booking/shifts by id
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $objBooking = new Booking();
        $booking = $objBooking->getBooking($id);
        $orgId = (Auth::user()->role == 'ORGANIZATION') ? Auth::user()->id : Auth::user()->parent_id;

        if ($booking && $orgId == $booking['user_id']) {
            if ($booking['date'] < date('Y-m-d')) {
                $booking['is_past'] = true;
            } else {
                $booking['is_past'] = false;
            }
            $obj = new BookingSpeciality();
            $booking['speciality'] = $obj->getBookingSpeciality($id);
            $booking['matching'] = $objBooking->getMatchByBooking($id, 'Matching', $booking['status']);
            $booking['interested'] = $objBooking->getMatchByBooking($id, 'Interested', $booking['status']);
            $booking['confirmed'] = $objBooking->getMatchByBooking($id, 'CONFIRMED', $booking['status']);
            if ($booking) {
                return response()->json(['status' => true, 'message' => 'booking get Successfully', 'data' => $booking], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, booking not available!', 'status' => false], 200);
            }
        } else {
            return response()->json(['message' => 'Sorry, booking not available!', 'status' => false], 200);
        }
        // $shift['speciality'] = BookingSpeciality::where('booking_id', $id)->get()->toArray();

    }

    /**
     * Used to edit booking/shifts
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit(Request $request)
    {
        //print_r($this->userId);exit;
        $requestData = $request->all();

        $validator = Validator::make($request->all(), [
            // 'reference_id' => 'required',
            'trust_id' => 'required',
            'id' => 'required',
            'ward_id' => 'required',
            'grade_id' => 'required',
            'date' => 'required',
            'hospital_id' => 'required',
            'shift_type_id' => 'required',
            'shift_id' => 'required',
            'speciality' => 'required:speciality,[]',
            'rate' => 'required',
            'commission' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $booking = Booking::findOrFail($requestData['id']);

            // if($requestData['date'] < date('Y-m-d'))
            // {
            //     return response()->json(['message' => 'booking date must be greater then or equal to today\'s date', 'status' => false], 200);
            // }

            if ($booking) {
                $bookingShift = OrganizationShift::findOrFail($requestData['shift_id']);
                //print_r($bookinghift);exit();
                $requestData['start_time'] = $bookingShift['start_time'];
                $requestData['end_time'] = $bookingShift['end_time'];
                if (Auth::user()->role == 'ORGANIZATION') {
                    $requestData['updated_by'] = $this->userId;
                } else {
                    $requestData['updated_by'] = $this->userId;
                }
                $booking->update($requestData);
                $objBookingSpeciality = new BookingSpeciality();
                $objBookingSpeciality->addSpeciality($requestData['speciality'], $requestData['id'], true);

                //added by me
                $objBooking = new Booking();
                $bookings = $objBooking->getMetchByBookingId($booking['id']);
                //print_r($bookings);exit();
                $objBookingMatch = new BookingMatch();
                $bookingMatch = $objBookingMatch->addBookingMatch($bookings, $requestData['id']);

                return response()->json(['status' => true, 'message' => 'Booking update Successfully.', 'data' => $booking], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Booking update failed!', 'status' => false], 409);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */

    /**
     * Used to delete booking/shifts.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {   
        $bookingObj = new Booking();
        $booking = Booking::find($id);
        
        $bookingMatch = $bookingObj->getMetchByBookingId($booking->id);
        if ($booking) {
            if(!empty($bookingMatch)){
                $objNotification = new Notification();
                foreach($bookingMatch as $key=>$val)
                {
                    $objNotification->addNotificationV2($val, 'shift_delete');
                }
            }
            // Booking::where('id', $id)->delete();
            $booking->delete();
            return response()->json(['status' => true, 'message' => 'Booking deleted succssfully!'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking not deleted!', 'status' => false], 409);
        }
    }

    /**
     * Used to show list of bookings by status.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function bookingStatus(Request $request, $status = NULL)
    {
        try {
            $objBooking = new Booking();
            $booking = $objBooking->getBookingByFilter($request, $status);
            if (count($booking) > 0) {
                return response()->json(['status' => true, 'message' => 'Booking Successfully get by status', 'data' => $booking], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 200);
        }
    }

    /**
     * Used to get matching candidate by booking id.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function getMetchByBookingId($bookingId)
    {
        $objBooking = new Booking();
        $booking = $objBooking->getMetchByBookingId($bookingId);
        // print_r($booking);
        // exit;
        try {
            $objBookingMatch = new BookingMatch();
            $bookingMatch = $objBookingMatch->addBookingMatch($booking, $bookingId);
            // print_r($bookingMatch);
            // exit;
            if ($bookingMatch) {
                return response()->json(['status' => true, 'message' => 'Booking Successfully get by status', 'data' => $booking], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * Used to update matching candidate by candidate id.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function updateMatchBySignee($signeeId)
    {
        //print_r($signeeId);exit;
        try {
            $objBookingSignee = new Booking();
            $booking = $objBookingSignee->editMetchBySigneeId($signeeId);
            $objBookingMatch = new BookingMatch();
            $bookingMatch = $objBookingMatch->editBookingMatchByUser($booking, $signeeId);
            if ($bookingMatch) {
                return response()->json(['status' => true, 'message' => 'Booking Successfully get by status', 'data' => $booking], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * Used to get candidate by candidate id and booking id.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function getSigneeByIdAndBookingId(Request $request)
    {
        $booking = new Booking();
        $bookingId = $request->get('bookingId');
        $signeeId = $request->get('signeeId');
        $signeeDetails = $booking->getSigneeByIdAndBookingId($bookingId, $signeeId);
        if ($signeeDetails) {
            return response()->json(['status' => true, 'message' => 'Candidate get successfully', 'data' => $signeeDetails], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Candidate not available!', 'status' => false], 404);
        }
    }

    /**
     * Used to get list of candidate whose speciality is matching with booking speciality by booking id.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function getBookingSignee($bookingId)
    {
        //echo $bookingId;
        $objBooking = new Booking();
        $booking = $objBooking->getBookingSignee($bookingId);

        // $objBooking = new Booking();
        // $bookingNew = $objBooking->getMetchByBookingId($bookingId);

        if ($booking) {
            return response()->json(['status' => true, 'message' => 'Booking get successfully', 'data' => $booking], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 200);
        }
    }

    /**
     * Used to get ward list by hospital and trust.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function getWardByHospital(Request $request)
    {
        //echo $hospitalId;
        $hospitalId = $request->get('hospitalId');
        $trustId = $request->get('trustId');
        if ($hospitalId == null) {
            $wardAll = Ward::where('hospital_id', $hospitalId)->get();
            if ($wardAll) {
                return response()->json(['status' => true, 'message' => 'wards get successfully', 'data' => $wardAll], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Wards not available!', 'status' => false], 404);
            }
        } else {
            $ward = Ward::where(['hospital_id' => $hospitalId])->get();
            if ($ward) {
                return response()->json(['status' => true, 'message' => 'Ward get successfully', 'data' => $ward], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Ward not available!', 'status' => false], 404);
            }
        }
    }

    /**
     * Used to get list of hospitals.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function hospitallist(Request $request, $trustId)
    {
        $ward = Hospital::where(['trust_id' => $trustId])->get();
        if (count($ward)) {
            return response()->json(['status' => true, 'message' => 'hospital get successfully', 'data' => $ward], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, hospital not available!', 'status' => false], 404);
        }
    }

    /**
     * Used to get list of grades.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function gradelist(Request $request)
    {
        $grade = Grade::all();
        if (count($grade)) {
            return response()->json(['status' => true, 'message' => 'grade get successfully', 'data' => $grade], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, grade not available!', 'status' => false], 404);
        }
    }

    /**
     * Used to generate reference id.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function reference(Request $request)
    {
        $time = [];
        $time['reference_id'] = date("ymdHis");
        if ($time) {
            return response()->json(['status' => true, 'message' => 'reference get successfully', 'data' => $time], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, reference not available!', 'status' => false], 404);
        }
    }

    /**
     * Used to show report list of bookings by status completed.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function reportCompletedBooking(Request $request)
    {
        try {
            $objBooking = new Booking();
            $booking = $objBooking->getCompletedBookingByDate($request);
            
            if (count($booking) > 0) {
                return response()->json(['status' => true, 'message' => 'Completed Booking Successfully. ', 'data' => $booking], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Completed Booking not available!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 200);
        }
    }

    /**
     * Used to show report list of bookings by status completed.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    
    public function downloadReportCompletedBooking(Request $request)
    {
        //echo "innnnnn"; exit;
        $user = Auth::user();
        $path = storage_path('app/public/test');
        // echo '<pre>';
        // print_r($path); exit;
        $fileName = date('Ymdhms').'-bookings.csv';
        //$tasks = Task::all();

        try {
            $objBooking = new Booking();
            $booking = $objBooking->getCompletedBookingByDate($request);
            // print_r($booking);
            // exit;
            //echo storage_path(); exit;
       
            if (count($booking) > 0) {
                $headers = array(
                    "Content-type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=$fileName",
                    "Pragma"              => "no-cache",
                    "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                    "Expires"             => "0"
                );
    
                $columns = array('Trust Name', 'Hospital Name', 'Ward Name', 'Grade', 'Date', 'Shift Time','Amount Payable','Amount Charge','Trust Code', 'Candidate Name');
    
                //$callback = function() use($booking, $columns) {
                    $uploadFile = $fileName;
                    $file = fopen($uploadFile, 'w');
                    fputcsv($file, $columns);
                    
                    foreach ($booking as $task) {
                        $row['Trust Name']  = $task->name;
                        $row['Hospital Name']    = $task->hospital_name;
                        $row['Ward Name']    = $task->ward_name;
                        $row['Grade']  = $task->grade_name;
                        $row['Date']  = $task->date;
                        $row['Shift Time']  = $task->start_time.' '.$task->end_time;
                        $row['Amount Payable']  = $task->payableAmont;
                        $row['Amount Charge']  = $task->rate;
                        $row['Trust Code']  = $task->code;
                        // $row['candidate']  = $task->candidate;
                        $explodeResult = explode(',',$task->candidate);
                        foreach($explodeResult as $key => $val){
                            $row['candidate'.$key]  = $task->created_shift_org_name ." / ".trim($val);
                        }
                        fputcsv($file, $row);
                        // fputcsv($file, array($row['Trust Name'], $row['Hospital Name'], $row['Ward Name'], $row['Grade'], 
                        // $row['Date'], $row['Shift Time'], $row['Amount Payable'],$row['Amount Charge'],$row['Trust Code'], $row['candidate']));
                    }
                    fclose($file);
                    $filePath = url('/'.$uploadFile);
                    rename(public_path() .'/'. $uploadFile, public_path() .'/uploads/org_csv/'. $uploadFile);
                    $filePath = public_path() .'/uploads/org_csv/'. $uploadFile;
                    $filePath =  url('/').'/uploads/org_csv/'. $uploadFile;
                //};
    
                //return response()->stream($callback, 200, $headers);

                return response()->json(['status' => true, 'message' => 'Completed Booking Report Generated Successfully. ', 'data' => $filePath], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Report could not be Generated!', 'status' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 200);
        }
    }
}
