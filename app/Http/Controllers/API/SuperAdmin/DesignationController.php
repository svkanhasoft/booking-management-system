<?php

namespace App\Http\Controllers\API\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Validator;
use Session;
use App\Models\User;
use App\Models\Designation;
use Hash;
use Config;
class DesignationController extends Controller
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
            $this->userId= Auth::user()->id;
            return $next($request);
        });
    }


    /**
     * Show list of designations.
     *
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {
        $perPage = Config::get('constants.pagination.perPage');
        $designationList = Designation::select('id','designation_name')->latest()->paginate($perPage);
        if ($designationList) {
            return response()->json(['status' => true, 'message' => 'Designation get Successfully', 'data' => $designationList], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Designation added failed!', 'status' => false], 409);
        }
    }

    /**
     * Add new designation.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function add(Request $request)
    {
        try{
            $requestData = $request->all();
            $requestData['user_id'] = $this->userId;
            $checkRecord =  Designation::create($requestData);
            if ($checkRecord) {
                return response()->json(['status' => true, 'message' => 'Designation added Successfully', 'data' => $checkRecord], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Designation added failed!', 'status' => false], 409);
            }
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);
        }
    }

    /**
     * how designation by id.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $designation = Designation::findOrFail($id);
        if ($designation) {
            return response()->json(['status' => true, 'message' => 'Designation get Successfully', 'data' => $designation], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Designation not found!', 'status' => false], 404);
        }
    }

    /**
     * Update designation.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Request $request)
    {
        $requestData = $request->all();
        $designation = Designation::findOrFail($requestData['designation_id']);
        $designation->update($requestData);
        if ($designation) {
            return response()->json(['status' => true, 'message' => 'Designation updated Successfully', 'data' => $designation], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Designation updated failed!', 'status' => false], 409);
        }
    }

    /**
     * Delete designation.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $designationDelete = Designation::destroy($id);
        if ($designationDelete) {
            return response()->json(['status' => true, 'message' => 'Designation delete Successfully'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Designation delete failed!', 'status' => false], 409);
        }
    }
}
