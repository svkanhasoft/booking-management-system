<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\OrganizationShift;
use Illuminate\Http\Request;

class OrganizationShiftController extends Controller
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
            $organizationshift = OrganizationShift::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('start_time', 'LIKE', "%$keyword%")
                ->orWhere('end_time', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $organizationshift = OrganizationShift::latest()->paginate($perPage);
        }

        return view('organization.organization-shift.index', compact('organizationshift'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.organization-shift.create');
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
        
        OrganizationShift::create($requestData);

        return redirect('organization/organization-shift')->with('flash_message', 'OrganizationShift added!');
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
        $organizationshift = OrganizationShift::findOrFail($id);

        return view('organization.organization-shift.show', compact('organizationshift'));
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
        $organizationshift = OrganizationShift::findOrFail($id);

        return view('organization.organization-shift.edit', compact('organizationshift'));
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
        
        $organizationshift = OrganizationShift::findOrFail($id);
        $organizationshift->update($requestData);

        return redirect('organization/organization-shift')->with('flash_message', 'OrganizationShift updated!');
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
        OrganizationShift::destroy($id);

        return redirect('organization/organization-shift')->with('flash_message', 'OrganizationShift deleted!');
    }
}
