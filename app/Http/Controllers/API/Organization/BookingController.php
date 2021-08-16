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
use App\Models\BookingSpeciality;

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
            'speciality' => 'required:speciality,[]',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        $requestData['user_id'] = $this->userId;
        $bookingCreated = Booking::create($requestData);
        if ($bookingCreated) {
            $objBookingSpeciality = new BookingSpeciality();
            $objBookingSpeciality->addSpeciality($requestData['speciality'], $bookingCreated['id'], false);
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
            'reference_id' => 'required',
            'trust_id' => 'required',
            'booking_id' => 'required',
            'ward_id' => 'required',
            'grade_id' => 'required',
            'date' => 'required',
            'shift_id' => 'required',
            'speciality' => 'required:speciality,[]',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $shift = Booking::findOrFail($requestData["booking_id"]);
        $shiftUpdated = $shift->update($requestData);
        if ($shiftUpdated) {
            $objBookingSpeciality = new BookingSpeciality();
            $objBookingSpeciality->addSpeciality($requestData['speciality'], $requestData["booking_id"], true);
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
    public function bookingStatus($status)
    {
        $objBooking = new Booking();
        $booking = $objBooking->getBookingByFilter($status);

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
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'booking_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $objBooking = Booking::find($request->post('booking_id'));
        $objBooking['status'] = $request->post('status');
        $res = $objBooking->save();
       // echo $res;exit();
        if ($res) {
            return response()->json(['status' => true, 'message' => 'Status changed successfully'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, status not change.', 'status' => false], 200);
        }

    }
}
