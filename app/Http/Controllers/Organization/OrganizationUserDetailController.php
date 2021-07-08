<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\OrganizationUserDetail;
use Illuminate\Http\Request;

class OrganizationUserDetailController extends Controller
{
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
            $organizationuserdetail = OrganizationUserDetail::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('contact_number', 'LIKE', "%$keyword%")
                ->orWhere('role_id', 'LIKE', "%$keyword%")
                ->orWhere('designation_id', 'LIKE', "%$keyword%")
                ->orWhere('created_by', 'LIKE', "%$keyword%")
                ->orWhere('updated_by', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $organizationuserdetail = OrganizationUserDetail::latest()->paginate($perPage);
        }

        return view('organization.organization-user-detail.index', compact('organizationuserdetail'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.organization-user-detail.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        
        $requestData = $request->all();
        
        OrganizationUserDetail::create($requestData);

        return redirect('organization/organization-user-detail')->with('flash_message', 'OrganizationUserDetail added!');
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
        $organizationuserdetail = OrganizationUserDetail::findOrFail($id);

        return view('organization.organization-user-detail.show', compact('organizationuserdetail'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $organizationuserdetail = OrganizationUserDetail::findOrFail($id);

        return view('organization.organization-user-detail.edit', compact('organizationuserdetail'));
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
        
        $organizationuserdetail = OrganizationUserDetail::findOrFail($id);
        $organizationuserdetail->update($requestData);

        return redirect('organization/organization-user-detail')->with('flash_message', 'OrganizationUserDetail updated!');
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
        OrganizationUserDetail::destroy($id);

        return redirect('organization/organization-user-detail')->with('flash_message', 'OrganizationUserDetail deleted!');
    }
}
