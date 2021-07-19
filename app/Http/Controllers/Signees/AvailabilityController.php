<?php

namespace App\Http\Controllers\Signees;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Availability;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
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
            $availability = Availability::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('day_name', 'LIKE', "%$keyword%")
                ->orWhere('shift_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $availability = Availability::latest()->paginate($perPage);
        }

        return view('signees.availability.index', compact('availability'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('signees.availability.create');
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
        
        Availability::create($requestData);

        return redirect('signees/availability')->with('flash_message', 'Availability added!');
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
        $availability = Availability::findOrFail($id);

        return view('signees.availability.show', compact('availability'));
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
        $availability = Availability::findOrFail($id);

        return view('signees.availability.edit', compact('availability'));
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
        
        $availability = Availability::findOrFail($id);
        $availability->update($requestData);

        return redirect('signees/availability')->with('flash_message', 'Availability updated!');
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
        Availability::destroy($id);

        return redirect('signees/availability')->with('flash_message', 'Availability deleted!');
    }
}
