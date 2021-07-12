<?php

namespace App\Http\Controllers\Signees;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\SigneesDetail;
use Illuminate\Http\Request;

class SigneesDetailController extends Controller
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
            $signeesdetail = SigneesDetail::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('candidate_id', 'LIKE', "%$keyword%")
                ->orWhere('phone_number', 'LIKE', "%$keyword%")
                ->orWhere('mobile_number', 'LIKE', "%$keyword%")
                ->orWhere('address_line_1', 'LIKE', "%$keyword%")
                ->orWhere('address_line_2', 'LIKE', "%$keyword%")
                ->orWhere('address_line_3', 'LIKE', "%$keyword%")
                ->orWhere('city', 'LIKE', "%$keyword%")
                ->orWhere('post_code', 'LIKE', "%$keyword%")
                ->orWhere('date_of_birth', 'LIKE', "%$keyword%")
                ->orWhere('nationality', 'LIKE', "%$keyword%")
                ->orWhere('candidate_referred_from', 'LIKE', "%$keyword%")
                ->orWhere('date_registered', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $signeesdetail = SigneesDetail::latest()->paginate($perPage);
        }

        return view('signees.signees-detail.index', compact('signeesdetail'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('signees.signees-detail.create');
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
        
        SigneesDetail::create($requestData);

        return redirect('signees/signees-detail')->with('flash_message', 'SigneesDetail added!');
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
        $signeesdetail = SigneesDetail::findOrFail($id);

        return view('signees.signees-detail.show', compact('signeesdetail'));
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
        $signeesdetail = SigneesDetail::findOrFail($id);

        return view('signees.signees-detail.edit', compact('signeesdetail'));
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
        
        $signeesdetail = SigneesDetail::findOrFail($id);
        $signeesdetail->update($requestData);

        return redirect('signees/signees-detail')->with('flash_message', 'SigneesDetail updated!');
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
        SigneesDetail::destroy($id);

        return redirect('signees/signees-detail')->with('flash_message', 'SigneesDetail deleted!');
    }
}
