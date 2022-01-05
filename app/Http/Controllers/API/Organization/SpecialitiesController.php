<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BookingSpeciality;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\Organization;
use App\Models\SigneeSpecialitie;
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
     * Create new speciality.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {
        try
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
            if(Auth::user()->role == 'ORGANIZATION')
            {
                $requestData['user_id'] = Auth::user()->id;
                $requestData['created_by'] = Auth::user()->id;
            }else{
                $requestData['user_id'] = Auth::user()->parent_id;
                $requestData['created_by'] = Auth::user()->id;
            }
            // $requestData['user_id'] = $this->userId;
            $addResult =  Speciality::create($requestData);
            if ($addResult) {
                return response()->json(['status' => true, 'message' => 'Speciality added Successfully', 'data' => $addResult], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, speciality added failed!', 'status' => false], 409);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);   //400 not found
        }
    }

    /**
     * Show specialities by id.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try{
            $speciality = Speciality::where('id', $id)->first();
            if ($speciality) {
                return response()->json(['status' => true, 'message' => 'get speciality Successfully', 'data' => $speciality], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, speciality not available!', 'status' => false], 404);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);   //400 not found
        }

    }
    /**
     * Display list of specialities.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function showAll(Request $request)
    {
        try
        {
            $perPage = Config::get('constants.pagination.perPage');
            $keyword = $request->get('search');
            //$showPagination = $request->get('showPagination');
            $speciality = Speciality::select("specialities.*",);
            $speciality->where('specialities.user_id',  $this->userId);

            if(Auth::user()->role == 'ORGANIZATION'){
                $staff = User::select('id')->where('parent_id', $this->userId)->get()->toArray();
                $staffIdArray = array_column($staff, 'id');
                $staffIdArray[] = Auth::user()->id;
                $query2 = Speciality::whereIn('user_id', $staffIdArray);
            }else{
                $query2 = Speciality::whereIn('user_id',array(Auth::user()->id,Auth::user()->parent_id));
            }

            if (!empty($keyword)) {
                // echo $keyword;exit;
                $query2->Where('specialities.speciality_name',  'LIKE', "%$keyword%");
            }

            //$speciality = Speciality::where('user_id', $this->userId)->get()->toArray();
            $speciality = $query2->latest()->paginate($perPage);
            $count =  $speciality->count();
            if ($count > 0) {
                return response()->json(['status' => true, 'message' => 'get speciality Successfully', 'data' => $speciality], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, speciality not available!', 'status' => false], 404);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);   //400 not found
        }
    }

    /**
     * Display list of specialities.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    // public function AllSpeciality(Request $request)
    // {
    //     //$query = Speciality::where('user_id', $this->userId)->get()->toArray();
    //     if (!empty($query)) {
    //         return response()->json(['status' => true, 'message' => 'Speciality Get Successfully', 'data' => $query], $this->successStatus);
    //     } else {
    //         return response()->json(['message' => 'Sorry, No Speciality Available!', 'status' => false], 404);
    //     }
    // }

    /**
     * Update the speciality.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        try
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
            if(Auth::user()->role == 'ORGANIZATION')
            {
                $speciality->updated_by = Auth::user()->id;
            }else{
                $speciality->updated_by = Auth::user()->id;
            }
            $addResult =  $speciality->update($requestData);
            if ($addResult) {
                return response()->json(['status' => true, 'message' => 'Speciality updated Successfully', 'data' => $speciality], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, speciality update failed!', 'status' => false], 409);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);   //400 not found
        }
    }

    /**
     * Delete the speciality.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        //echo $id;exit;
        try
        {
            $signee = SigneeSpecialitie::where(['speciality_id' => $id])->get();
            $booking = BookingSpeciality::where(['speciality_id' => $id])->get();
            if(count($signee) > 0 || count($booking) > 0)
            {
                return response()->json(['message' => 'Sorry, You can\'t delete this speciality it\'s already assigned', 'status' => false], 404);
            } else {
                $speciality =  Speciality::destroy($id);
                if ($speciality) {
                    return response()->json(['status' => true, 'message' => 'Speciality delete Successfully', 'data' => $speciality], $this->successStatus);
                } else {
                    return response()->json(['message' => 'Sorry, Speciality delete failed!', 'status' => false], 404);
                }
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['message' => $e->getMessage(), 'status' => false], 400);   //400 not found
        }
    }

    public function getSpecialtyWithoutPagination()
    {
        $speciality = Speciality::select("specialities.*",);
        $speciality->where('specialities.user_id',  $this->userId);
        if(Auth::user()->role == 'ORGANIZATION'){
            $staff = User::select('id')->where('parent_id', $this->userId)->get()->toArray();
            $staffIdArray = array_column($staff, 'id');
            $staffIdArray[] = Auth::user()->id;
            $query2 = Speciality::whereIn('user_id', $staffIdArray);
        }else{
            //print(Auth::user()->role);exit;
            $query2 = Speciality::whereIn('user_id',array(Auth::user()->id,Auth::user()->parent_id));
        }
        $res = $query2->get()->toArray();
        if ($res) {
            return response()->json(['status' => true, 'message' => 'get speciality Successfully', 'data' => $res], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, speciality not available!', 'status' => false], 200);
        }

    }
}
