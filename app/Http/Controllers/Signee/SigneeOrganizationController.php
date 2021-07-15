<?php

namespace App\Http\Controllers\Signee;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\SigneeOrganization;
use Illuminate\Http\Request;

class SigneeOrganizationController extends Controller
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
            $signeeorganization = SigneeOrganization::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $signeeorganization = SigneeOrganization::latest()->paginate($perPage);
        }

        return view('signee.signee-organization.index', compact('signeeorganization'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('signee.signee-organization.create');
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
        
        SigneeOrganization::create($requestData);

        return redirect('signee/signee-organization')->with('flash_message', 'SigneeOrganization added!');
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
        $signeeorganization = SigneeOrganization::findOrFail($id);

        return view('signee.signee-organization.show', compact('signeeorganization'));
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
        $signeeorganization = SigneeOrganization::findOrFail($id);

        return view('signee.signee-organization.edit', compact('signeeorganization'));
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
        
        $signeeorganization = SigneeOrganization::findOrFail($id);
        $signeeorganization->update($requestData);

        return redirect('signee/signee-organization')->with('flash_message', 'SigneeOrganization updated!');
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
        SigneeOrganization::destroy($id);

        return redirect('signee/signee-organization')->with('flash_message', 'SigneeOrganization deleted!');
    }
}
