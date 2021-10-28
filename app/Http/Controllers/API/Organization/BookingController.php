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
use App\Models\OrganizationShift;
use DB;

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
            if($requestData['date'] < date('Y-m-d'))
            {
                return response()->json(['message' => 'booking date must be greater then or equal to today\'s date', 'status' => false], 200);
            }
            $requestData['user_id'] = $this->userId;
            $requestData['start_time'] = $shift['start_time'];
            $requestData['end_time'] = $shift['end_time'];
            $requestData['created_by'] = $this->userId;
            $bookingCreated = Booking::create($requestData);
            if ($bookingCreated) {
                $objBookingSpeciality = new BookingSpeciality();
                $objBookingSpeciality->addSpeciality($requestData['speciality'], $bookingCreated['id'], false);

                $objBooking = new Booking();
                $bookings = $objBooking->getMetchByBookingId($bookingCreated['id']);

                $objBookingMatch = new BookingMatch();
                $bookingMatch = $objBookingMatch->addBookingMatch($bookings, $bookingCreated['id']);

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
        if($booking){
            $obj = new BookingSpeciality();
            $booking['speciality'] = $obj->getBookingSpeciality($id);
            $booking['matching'] = $objBooking->getMatchByBooking($id,'Matching');
            $booking['interested'] = $objBooking->getMatchByBooking($id,'Interested');
        }
        // $shift['speciality'] = BookingSpeciality::where('booking_id', $id)->get()->toArray();
        if ($booking) {
            return response()->json(['status' => true, 'message' => 'booking get Successfully', 'data' => $booking], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, booking not available!', 'status' => false], 200);
        }
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
        ]);
        if ($validator->fails()) {
            $error = $validator->messages();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        try {
            $booking = Booking::findOrFail($requestData["id"]);
            // if($requestData['date'] < date('Y-m-d'))
            // {
            //     return response()->json(['message' => 'booking date must be greater then or equal to today\'s date', 'status' => false], 200);
            // }

            if ($booking) {
                $bookingShift = OrganizationShift::findOrFail($requestData["shift_id"]);
                //print_r($bookinghift);exit();
                $requestData['start_time'] = $bookingShift['start_time'];
                $requestData['end_time'] = $bookingShift['end_time'];
                $booking->updated_by = $this->userId;
                $booking->update($requestData);
                $objBookingSpeciality = new BookingSpeciality();
                $objBookingSpeciality->addSpeciality($requestData['speciality'], $requestData["id"], true);

                //added by me
                $objBooking = new Booking();
                $bookings = $objBooking->getMetchByBookingId($booking['id']);

                $objBookingMatch = new BookingMatch();
                $bookingMatch = $objBookingMatch->addBookingMatch($bookings, $requestData["id"]);

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
        //print_r($id);exit();
        $booking = Booking::where('id', $id)->delete();
        //print_r($booking);exit();
        // $shift = BookingMatch::where(['booking_id' => $id])->delete();
        // $booking = BookingSpeciality::where(['booking_id' => $id])->delete();
        if ($booking) {
            return response()->json(['status' => true, 'message' => 'Booking deleted!'], $this->successStatus);
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
    public function bookingStatus(Request $request, $status = null)
    {
        //print_r(Auth::user()->role);exit();
        $objBooking = new Booking();
        $booking = $objBooking->getBookingByFilter($request, $status);

        if (count($booking) > 0) {
            return response()->json(['status' => true, 'message' => 'Booking Successfully get by status', 'data' => $booking], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 200);
        }
    }

    /**
     * Used to get matching signee by booking id.
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
                return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * Used to update matching signee by signee id.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function updateMatchBySignee($signeeId)
    {
        try {
            $objBookingSignee = new Booking();
            $booking = $objBookingSignee->editMetchBySigneeId($signeeId);
            $objBookingMatch = new BookingMatch();
            $bookingMatch = $objBookingMatch->editBookingMatchByUser($booking, $signeeId);
            if ($bookingMatch) {
                return response()->json(['status' => true, 'message' => 'Booking Successfully get by status', 'data' => $booking], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * Used to get signee by signee id and booking id.
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
            return response()->json(['status' => true, 'message' => 'Signee get successfully', 'data' => $signeeDetails], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Signee not available!', 'status' => false], 404);
        }
    }

    /**
     * Used to get list of signee whose speciality is matching with booking speciality by booking id.
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
            return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 404);
        }

    }

    /**
     * Used to get ward list by hospital and trust.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function getWardByHospitalAndTrust(Request $request)
    {
        //echo $hospitalId;
        $hospitalId = $request->get('hospitalId');
        $trustId = $request->get('trustId');
        if ($hospitalId == null) {
            $wardAll = Ward::where('trust_id', $trustId)->get();
            if ($wardAll) {
                return response()->json(['status' => true, 'message' => 'wards get successfully', 'data' => $wardAll], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Wards not available!', 'status' => false], 404);
            }
        } else {
            $ward = Ward::where(['hospital_id' => $hospitalId, 'trust_id' => $trustId])->get();
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
}
