<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Trust;
use Illuminate\Http\Request;

class TrustsController extends Controller
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
            $trusts = Trust::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('trust_id', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('code', 'LIKE', "%$keyword%")
                ->orWhere('preference_invoide_mathod', 'LIKE', "%$keyword%")
                ->orWhere('email_address', 'LIKE', "%$keyword%")
                ->orWhere('address_line_1', 'LIKE', "%$keyword%")
                ->orWhere('address_line_2', 'LIKE', "%$keyword%")
                ->orWhere('address_line_3', 'LIKE', "%$keyword%")
                ->orWhere('city', 'LIKE', "%$keyword%")
                ->orWhere('post_code', 'LIKE', "%$keyword%")
                ->orWhere('trust_portal_url', 'LIKE', "%$keyword%")
                ->orWhere('portal_email', 'LIKE', "%$keyword%")
                ->orWhere('portal_password', 'LIKE', "%$keyword%")
                ->orWhere('first_name', 'LIKE', "%$keyword%")
                ->orWhere('first_name', 'LIKE', "%$keyword%")
                ->orWhere('last_name', 'LIKE', "%$keyword%")
                ->orWhere('email_address', 'LIKE', "%$keyword%")
                ->orWhere('phone_number', 'LIKE', "%$keyword%")
                ->orWhere('client', 'LIKE', "%$keyword%")
                ->orWhere('department', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $trusts = Trust::latest()->paginate($perPage);
        }

        return view('organization.trusts.index', compact('trusts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.trusts.create');
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
        
        Trust::create($requestData);

        return redirect('organization/trusts')->with('flash_message', 'Trust added!');
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
        $trust = Trust::findOrFail($id);

        return view('organization.trusts.show', compact('trust'));
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
        $trust = Trust::findOrFail($id);

        return view('organization.trusts.edit', compact('trust'));
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
        
        $trust = Trust::findOrFail($id);
        $trust->update($requestData);

        return redirect('organization/trusts')->with('flash_message', 'Trust updated!');
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
        Trust::destroy($id);

        return redirect('organization/trusts')->with('flash_message', 'Trust deleted!');
    }
}
