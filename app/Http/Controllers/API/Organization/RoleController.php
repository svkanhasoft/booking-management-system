<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests;
use App\Models\User;
use Hash;
use App\Models\Role;

class RoleController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $role = Role::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('role_name', 'LIKE', "%$keyword%")
                ->orWhere('created_by', 'LIKE', "%$keyword%")
                ->orWhere('updated_by', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $role = Role::latest()->paginate($perPage);
        }

        return view('organization.role.index', compact('role'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            // 'role_name' => 'required|unique:roles',
            'role_name' => 'required|unique:roles,user_id,' . $this->userId,
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $requestData = $request->all();
        $requestData['user_id'] = $this->userId;
        $roleCreated = Role::create($requestData);
        if ($roleCreated) {
            return response()->json(['status' => true, 'message' => 'Role added Successfully', 'data' => $roleCreated], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Role added failed!', 'status' => false], 200);
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
        $role = Role::findOrFail($id);
        if ($role) {
            return response()->json(['status' => true, 'message' => 'Role get Successfully', 'data' => $role], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Role not available!', 'status' => false], 200);
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
            // 'role_name' => 'required',
            'role_id' => 'required',
            // 'role_name' => 'required|unique:roles,id,' . $requestData["role_id"],
            'role_name' => 'required|unique:roles,role_name,'.$requestData["role_id"],

        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        
        $role = Role::findOrFail($requestData["role_id"]);
        $roleUpdated = $role->update($requestData);
        if ($roleUpdated) {
            return response()->json(['status' => true, 'message' => 'Role update Successfully.', 'data' => $role], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Role update failed!', 'status' => false], 200);
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
        // $role = Role::where('user_id',$this->userId)->where('user_id', 1)->get()->toArray();
        $role = Role::whereIn('user_id', array($this->userId, 1))->get()->toArray();
        if ($role) {
            return response()->json(['status' => true, 'message' => 'Role get Successfully', 'data' => $role], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Role not available!', 'status' => false], 200);
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
    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $role = Role::findOrFail($id);
        $role->update($requestData);
        return redirect('organization/role')->with('flash_message', 'Role updated!');
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
        $role = Role::where(['user_id' => $this->userId,'id'=> $id])->where('id', $id)->delete();
        if ($role) {
            return response()->json(['status' => true, 'message' => 'Role deleted!', 'data' => $role], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Role not deleted!', 'status' => false], 200);
        }
    }
}
