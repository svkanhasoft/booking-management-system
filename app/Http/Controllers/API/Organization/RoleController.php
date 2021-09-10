<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Requests;
use App\Models\OrganizationUserDetail;
use App\Models\User;
use Hash;
use App\Models\Role;
use Illuminate\Validation\Rule;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    { 
        $res = Role::withTrashed()->whereNotNull('deleted_at')->where(['role_name' => $request->all('role_name'), 'user_id' => $this->userId])->restore();
        if ($res == 1) {
            return response()->json(['status' => true, 'message' => 'Role added Successfully', 'data' => $request->all()], $this->successStatus);
        }
        $validator = Validator::make($request->all(), [
            'role_name' => 'unique:roles,role_name,NULL,id,user_id,'.$this->userId
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
        $role = Role::where('id', $id)->first();
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
            'id' => 'required',
            'role_name' => 'unique:roles,role_name,'.$requestData['id'].'NULL,id,user_id,'.$this->userId
        ]);
        if ($validator->fails()) {
            $error = $validator->messages()->first();
            return response()->json(['status' => false, 'message' => $error], 200);
        }
        $role = Role::findOrFail($requestData["id"]);
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
        if (Auth::user()->role == 'ORGANIZATION') {
            $staff = User::select('id')->where('parent_id', $this->userId)->get()->toArray();
            $staffIdArray = array_column($staff, 'id');
            $staffIdArray[] = Auth::user()->id;
            $staffIdArray[] = 1;
            $role = Role::whereIn('user_id', $staffIdArray);
        } else {
            $role = Role::whereIn('user_id', array(Auth::user()->id,1, Auth::user()->parent_id));
        }
        // $role = Role::whereIn('user_id', array($this->userId, 1))->get()->toArray();
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
        $staff = OrganizationUserDetail::where(['role_id' => $id])->get();
        if(count($staff) > 0)
        {
            return response()->json(['message' => 'Sorry, This role already assign to the staff', 'status' => false], 200);
        }
        else
        {
            $role = Role::where(['user_id' => $this->userId, 'id'=> $id])->where('id', $id)->delete();
            if ($role) {
                return response()->json(['status' => true, 'message' => 'Role deleted!', 'data' => $role], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Role not deleted!', 'status' => false], 200);
            }
        }
    }
}
