<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\BookingSpeciality;
use Illuminate\Http\Request;

class BookingSpecialityController extends Controller
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
            $bookingspeciality = BookingSpeciality::where('booking_id', 'LIKE', "%$keyword%")
                ->orWhere('speciality_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $bookingspeciality = BookingSpeciality::latest()->paginate($perPage);
        }

        return view('organization.booking-speciality.index', compact('bookingspeciality'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.booking-speciality.create');
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
        
        BookingSpeciality::create($requestData);

        return redirect('organization/booking-speciality')->with('flash_message', 'BookingSpeciality added!');
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
        $bookingspeciality = BookingSpeciality::findOrFail($id);

        return view('organization.booking-speciality.show', compact('bookingspeciality'));
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
        $bookingspeciality = BookingSpeciality::findOrFail($id);

        return view('organization.booking-speciality.edit', compact('bookingspeciality'));
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
        
        $bookingspeciality = BookingSpeciality::findOrFail($id);
        $bookingspeciality->update($requestData);

        return redirect('organization/booking-speciality')->with('flash_message', 'BookingSpeciality updated!');
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
        BookingSpeciality::destroy($id);

        return redirect('organization/booking-speciality')->with('flash_message', 'BookingSpeciality deleted!');
    }
}
