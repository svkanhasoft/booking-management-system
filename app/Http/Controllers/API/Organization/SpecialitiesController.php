<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\Organization;
use Hash;
use Config;
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
        $perPage = Config::get('constants.pagination.perPage');

        if (!empty($keyword)) {
            $specialities = Speciality::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('speciality_name', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $specialities = Speciality::latest()->paginate($perPage);
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

        $res = Speciality::withTrashed()->whereNotNull('deleted_at')->where(['speciality_name' => $request->all('speciality_name'), 'user_id' => $this->userId])->restore();
        // $res = Speciality::withTrashed()->where(['speciality_name' => $request->all('speciality_name'),'user_id' => $this->userId])->restore();
        if ($res == 1) {
            return response()->json(['status' => true, 'message' => 'Speciality added Successfully', 'data' => $request->all()], $this->successStatus);
        }
        $validator = Validator::make($request->all(), [
            // "speciality_name" => 'required',
            'speciality_name' => 'unique:specialities,speciality_name,NULL,id,user_id,' . $this->userId
            // 'speciality_name' => 'unique:specialities,speciality_name,NULL,id,user_id,'.$this->userId,'deleted_at,NULL'

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
    public function showAll(Request $request)
    {
        $perPage = Config::get('constants.pagination.perPage');
        $keyword = $request->get('search');
        $showPagination = $request->get('showPagination');
        $query = Speciality::select("specialities.*",);
        $query->where('specialities.user_id',  $this->userId);
        if (!empty($keyword)) {
            // echo $keyword;exit;
            $query->Where('specialities.speciality_name',  'LIKE', "%$keyword%");
        }
        // if ($showPagination == 0) {
        //     $speciality =  $query->latest('specialities.created_at')->get();
        //     if (!empty($speciality)) {
        //         return response()->json(['status' => true, 'message' => 'get speciality Successfully', 'data' => $speciality], $this->successStatus);
        //     } else {
        //         return response()->json(['message' => 'Sorry, speciality not available!', 'status' => false], 200);
        //     }
        // } else {
        $speciality =  $query->latest('specialities.created_at')->paginate($perPage);
        $count =  $query->latest('specialities.created_at')->paginate($perPage)->count();
        if ($count > 0) {
            return response()->json(['status' => true, 'message' => 'get speciality Successfully', 'data' => $speciality], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, speciality not available!', 'status' => false], 200);
        }
        // }
    }

    public function AllSpeciality(Request $request)
    {
        $query = Speciality::where('user_id', $this->userId)->get()->toArray();
        if (!empty($query)) {
            return response()->json(['status' => true, 'message' => 'Speciality Get Successfully', 'data' => $query], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, No Speciality Available!', 'status' => false], 200);
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
        $requestData = $request->all();
        $validator = Validator::make($request->all(), [
            // "speciality_name" => 'required',
            'speciality_name' => 'unique:specialities,speciality_name,' . $requestData['speciality_id'] . 'NULL,id,user_id,' . $this->userId,
            "speciality_id" => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }

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
