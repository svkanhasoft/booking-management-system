<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Speciality;
use Illuminate\Http\Request;

class SpecialitiesController extends Controller
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
            $specialities = Speciality::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('speciality_name', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $specialities = Speciality::latest()->paginate($perPage);
        }

        return view('organization.specialities.index', compact('specialities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.specialities.create');
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
        
        Speciality::create($requestData);

        return redirect('organization/specialities')->with('flash_message', 'Speciality added!');
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
        $speciality = Speciality::findOrFail($id);

        return view('organization.specialities.show', compact('speciality'));
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
        $speciality = Speciality::findOrFail($id);

        return view('organization.specialities.edit', compact('speciality'));
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
        
        $speciality = Speciality::findOrFail($id);
        $speciality->update($requestData);

        return redirect('organization/specialities')->with('flash_message', 'Speciality updated!');
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
        Speciality::destroy($id);

        return redirect('organization/specialities')->with('flash_message', 'Speciality deleted!');
    }
}
