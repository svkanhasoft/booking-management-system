<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\Organization;
use Hash;

use App\Models\Speciality; 

class SpecialitiesController extends Controller
{
    public $successStatus = 200;
    protected $userId;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!empty(Auth::user())) {
                $this->userId = Auth::user()->id;
            }
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $specialities = Speciality::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('speciality_name', 'LIKE', "%$keyword%")
                ->latest()->simplePaginate($perPage);
        } else {
            $specialities = Speciality::latest()->simplePaginate($perPage);
        }

        return view('organization.specialities.index', compact('specialities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "speciality_name" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        $requestData['user_id'] = $this->userId;
        $addResult =  Speciality::create($requestData);
        if ($addResult) {
            return response()->json(['status' => true, 'message' => 'Speciality added Successfully', 'data' => $addResult], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, speciality added failed!', 'status' => false], 200);
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
        $speciality = Speciality::where('id', $id)->first();
        if ($speciality) {
            return response()->json(['status' => true, 'message' => 'get speciality Successfully', 'data' => $speciality], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, speciality not available!', 'status' => false], 200);
        }
    }
    /**
     * All Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function showAll()
    {
        $speciality = Speciality::all();
        if ($speciality) {
            return response()->json(['status' => true, 'message' => 'get speciality Successfully', 'data' => $speciality], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, speciality not available!', 'status' => false], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "speciality_name" => 'required',
            "speciality_id" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        $speciality = Speciality::findOrFail($requestData['speciality_id']);
        $addResult =  $speciality->update($requestData);
        if ($addResult) {
            return response()->json(['status' => true, 'message' => 'Speciality update Successfully', 'data' => $speciality], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, speciality update failed!', 'status' => false], 200);
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
        $speciality =  Speciality::destroy($id);
        if ($speciality) {
            return response()->json(['status' => true, 'message' => 'Speciality delete Successfully', 'data' => $speciality], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Speciality delete failed!', 'status' => false], 200);
        }
    }
}
