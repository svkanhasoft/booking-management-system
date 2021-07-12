<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Ward;
use Illuminate\Http\Request;

class WardController extends Controller
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
            $ward = Ward::where('trust_id', 'LIKE', "%$keyword%")
                ->orWhere('ward_name', 'LIKE', "%$keyword%")
                ->orWhere('ward_type', 'LIKE', "%$keyword%")
                ->orWhere('ward_number', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $ward = Ward::latest()->paginate($perPage);
        }

        return view('organization.ward.index', compact('ward'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.ward.create');
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
        
        Ward::create($requestData);

        return redirect('organization/ward')->with('flash_message', 'Ward added!');
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
        $ward = Ward::findOrFail($id);

        return view('organization.ward.show', compact('ward'));
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
        $ward = Ward::findOrFail($id);

        return view('organization.ward.edit', compact('ward'));
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
        
        $ward = Ward::findOrFail($id);
        $ward->update($requestData);

        return redirect('organization/ward')->with('flash_message', 'Ward updated!');
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
        Ward::destroy($id);

        return redirect('organization/ward')->with('flash_message', 'Ward deleted!');
    }
}
