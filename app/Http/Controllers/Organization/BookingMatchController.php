<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\BookingMatch;
use Illuminate\Http\Request;

class BookingMatchController extends Controller
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
            $bookingmatch = BookingMatch::where('organization_id', 'LIKE', "%$keyword%")
                ->orWhere('candidate_id', 'LIKE', "%$keyword%")
                ->orWhere('booking_id', 'LIKE', "%$keyword%")
                ->orWhere('trust_id', 'LIKE', "%$keyword%")
                ->orWhere('match_count', 'LIKE', "%$keyword%")
                ->orWhere('booking_date', 'LIKE', "%$keyword%")
                ->orWhere('booking_status', 'LIKE', "%$keyword%")
                ->orWhere('shift_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $bookingmatch = BookingMatch::latest()->paginate($perPage);
        }

        return view('organization.booking-match.index', compact('bookingmatch'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.booking-match.create');
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
        
        BookingMatch::create($requestData);

        return redirect('organization/booking-match')->with('flash_message', 'BookingMatch added!');
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
        $bookingmatch = BookingMatch::findOrFail($id);

        return view('organization.booking-match.show', compact('bookingmatch'));
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
        $bookingmatch = BookingMatch::findOrFail($id);

        return view('organization.booking-match.edit', compact('bookingmatch'));
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
        
        $bookingmatch = BookingMatch::findOrFail($id);
        $bookingmatch->update($requestData);

        return redirect('organization/booking-match')->with('flash_message', 'BookingMatch updated!');
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
        BookingMatch::destroy($id);

        return redirect('organization/booking-match')->with('flash_message', 'BookingMatch deleted!');
    }
}
