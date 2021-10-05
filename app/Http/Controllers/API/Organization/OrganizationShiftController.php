<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests;
use App\Models\User;
use Hash;
use App\Models\OrganizationShift;

class OrganizationShiftController extends Controller
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
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_time' => 'unique:organization_shift,start_time,NULL,id,user_id,' . $this->userId,
            'end_time' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        // echo "fsdds";
        // exit;
        $requestData = $request->all();
        $requestData['user_id'] = $this->userId;
        $shiftCreated = OrganizationShift::create($requestData);
        if ($shiftCreated) {
            return response()->json(['status' => true, 'message' => 'Shift added Successfully', 'data' => $shiftCreated], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Shift added failed!', 'status' => false], 424);
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
        $shift = OrganizationShift::where('id', $id)->first();
        if ($shift) {
            return response()->json(['status' => true, 'message' => 'Shift get Successfully', 'data' => $shift], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Shift not available!', 'status' => false], 404);
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
            'shift_id' => 'required',
            'start_time' => 'required|unique:organization_shift,start_time,' . $requestData["shift_id"],
            'end_time' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

        $shift = OrganizationShift::findOrFail($requestData["shift_id"]);
        $shiftUpdated = $shift->update($requestData);
        if ($shiftUpdated) {
            return response()->json(['status' => true, 'message' => 'Shift update Successfully.', 'data' => $shift], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Shift update failed!', 'status' => false], 424);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function showAll()
    {
        $shift = OrganizationShift::select('id','start_time','end_time')->get()->toArray();
        // $shift = OrganizationShift::where('user_id', $this->userId)->get()->toArray();
        if ($shift) {
            return response()->json(['status' => true, 'message' => 'Shift get Successfully', 'data' => $shift], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Shift not available!', 'status' => false], 404);
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
        $shift = OrganizationShift::where(['user_id' => $this->userId, 'id' => $id])->delete();
        if ($shift) {
            return response()->json(['status' => true, 'message' => 'Shift deleted!', 'data' => $shift], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Shift not deleted!', 'status' => false], 409);
        }
    }
}
