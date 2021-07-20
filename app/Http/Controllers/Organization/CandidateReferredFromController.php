<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\CandidateReferredFrom;
use Illuminate\Http\Request;

class CandidateReferredFromController extends Controller
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
            $candidatereferredfrom = CandidateReferredFrom::where('name', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $candidatereferredfrom = CandidateReferredFrom::latest()->paginate($perPage);
        }

        return view('organization.candidate-referred-from.index', compact('candidatereferredfrom'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.candidate-referred-from.create');
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
        
        CandidateReferredFrom::create($requestData);

        return redirect('organization/candidate-referred-from')->with('flash_message', 'CandidateReferredFrom added!');
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
        $candidatereferredfrom = CandidateReferredFrom::findOrFail($id);

        return view('organization.candidate-referred-from.show', compact('candidatereferredfrom'));
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
        $candidatereferredfrom = CandidateReferredFrom::findOrFail($id);

        return view('organization.candidate-referred-from.edit', compact('candidatereferredfrom'));
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
        
        $candidatereferredfrom = CandidateReferredFrom::findOrFail($id);
        $candidatereferredfrom->update($requestData);

        return redirect('organization/candidate-referred-from')->with('flash_message', 'CandidateReferredFrom updated!');
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
        CandidateReferredFrom::destroy($id);

        return redirect('organization/candidate-referred-from')->with('flash_message', 'CandidateReferredFrom deleted!');
    }
}
