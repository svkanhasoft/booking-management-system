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
     * Show the form for creating a new resource.
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
        $requestData = $request->all();
        $requestData['user_id'] = $this->userId;
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
            return response()->json(['message' => 'Sorry, Booking added failed!', 'status' => false], 200);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $objBooking = new Booking();
        $booking = $objBooking->getBooking($id);
        $obj = new BookingSpeciality();
        $booking['speciality'] = $obj->getBookingSpeciality($id);
        // $shift['speciality'] = BookingSpeciality::where('booking_id', $id)->get()->toArray();
        if ($booking) {
            return response()->json(['status' => true, 'message' => 'booking get Successfully', 'data' => $booking], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, booking not available!', 'status' => false], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
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

        $shift = Booking::findOrFail($requestData["id"]);
        $shiftUpdated = $shift->update($requestData);
        if ($shiftUpdated) {
            $objBookingSpeciality = new BookingSpeciality();
            $objBookingSpeciality->addSpeciality($requestData['speciality'], $requestData["id"], true);
            return response()->json(['status' => true, 'message' => 'Booking update Successfully.', 'data' => $shift], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking update failed!', 'status' => false], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */

    

    public function updates(Request $request)
    {
        
        // $validator = Validator::make($request->all(), [
        //     "trust_id" => 'required',
             
        // ]);
        // if ($validator->fails()) {
        //     $error = $validator->messages();
        //     return response()->json(['status' => false, 'message' => $error], 200);
        // }

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
        echo "Hiiiii ";
        exit;
        $requestData = $request->all();
        // dd($requestData);
        // exit;
        $shift = Booking::findOrFail($requestData["id"]);
        $shiftUpdated = $shift->update($requestData);
        if ($shiftUpdated) {
            $objBookingSpeciality = new BookingSpeciality();
            $objBookingSpeciality->addSpeciality($requestData['speciality'], $requestData["id"], true);
            return response()->json(['status' => true, 'message' => 'Booking update Successfully.', 'data' => $shift], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking update failed!', 'status' => false], 200);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $shift = Booking::where(['user_id' => $this->userId, 'id' => $id])->delete();
        $shift = BookingMatch::where(['booking_id' => $id])->delete();
        $booking = BookingSpeciality::where(['booking_id' => $id])->delete();
        if ($booking) {
            return response()->json(['status' => true, 'message' => 'Booking deleted!'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking not deleted!', 'status' => false], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function bookingStatus(Request $request, $status = null)
    {
        $objBooking = new Booking();
        $booking = $objBooking->getBookingByFilter($request, $status);

        if (count($booking) > 0) {
            return response()->json(['status' => true, 'message' => 'Booking Successfully get by status', 'data' => $booking], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 200);
        }
    }

    /**
     * change booking status.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function changeBookingStatus(Request $request)
    {
        $requestData = $request->all();
        //print_r($requestData);exit();
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'signee_id' => 'required',
            'booking_id' => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $booking = booking::findOrFail($requestData['booking_id']);
        $bookingUpdate = $booking->update($requestData);

        $objBookingMatch = BookingMatch::firstOrNew(['signee_id' => $requestData['signee_id'], 'booking_id' => $requestData['booking_id']]);
        $objBookingMatch->booking_status = "OPEN";

        $objBookingMatch->save();

        if ($objBookingMatch) {
            return response()->json(['status' => true, 'message' => 'Status changed successfully'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, status not change.', 'status' => false], 200);
        }
    }

    /**
     * add signee by match with speciality.
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
        $objBookingMatch = new BookingMatch();
        $bookingMatch = $objBookingMatch->addBookingMatch($booking, $bookingId);
        // print_r($bookingMatch);
        // exit;
        if ($bookingMatch) {
            return response()->json(['status' => true, 'message' => 'Booking Successfully get by status', 'data' => $booking], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 200);
        }
    }

    /**
     * Display the update Match By Signee user Id.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function updateMatchBySignee($signeeId)
    {
        $objBookingSignee = new Booking();
        $booking = $objBookingSignee->editMetchBySigneeId($signeeId);
        $objBookingMatch = new BookingMatch();
        $bookingMatch = $objBookingMatch->editBookingMatchByUser($booking, $signeeId);
        if ($bookingMatch) {
            return response()->json(['status' => true, 'message' => 'Booking Successfully get by status', 'data' => $booking], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Booking not available!', 'status' => false], 200);
        }
    }

    public function getSigneeByIdAndBookingId(Request $request)
    {
        $booking = new Booking();
        $bookingId = $request->get('bookingId');
        $signeeId = $request->get('signeeId');
        $signeeDetails = $booking->getSigneeByIdAndBookingId($bookingId, $signeeId);
        if ($signeeDetails) {
            return response()->json(['status' => true, 'message' => 'Signee get successfully', 'data' => $signeeDetails], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Signee not available!', 'status' => false], 200);
        }
    }

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
                return response()->json(['message' => 'Sorry, Wards not available!', 'status' => false], 200);
            }
        } else {
            $ward = Ward::where(['hospital_id' => $hospitalId, 'trust_id' => $trustId])->get();
            if ($ward) {
                return response()->json(['status' => true, 'message' => 'Ward get successfully', 'data' => $ward], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Ward not available!', 'status' => false], 200);
            }
        }
    }

    public function hospitallist(Request $request, $trustId)
    {
        $ward = Hospital::where(['trust_id' => $trustId])->get();
        if (count($ward)) {
            return response()->json(['status' => true, 'message' => 'hospital get successfully', 'data' => $ward], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, hospital not available!', 'status' => false], 200);
        }
    }

    public function gradelist(Request $request)
    {
        $grade = Grade::all();
        if (count($grade)) {
            return response()->json(['status' => true, 'message' => 'grade get successfully', 'data' => $grade], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, grade not available!', 'status' => false], 200);
        }
    }

    public function reference(Request $request)
    {
        $time = [];
        $time['reference_id'] = date("ymdHis");
        if ($time) {
            return response()->json(['status' => true, 'message' => 'reference get successfully', 'data' => $time], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, reference not available!', 'status' => false], 200);
        }
    }
}
