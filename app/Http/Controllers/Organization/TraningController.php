<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\Traning;
use Illuminate\Http\Request;

class TraningController extends Controller
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
            $traning = Traning::where('trust_id', 'LIKE', "%$keyword%")
                ->orWhere('traning_name', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $traning = Traning::latest()->paginate($perPage);
        }

        return view('organization.traning.index', compact('traning'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.traning.create');
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
        
        Traning::create($requestData);

        return redirect('organization/traning')->with('flash_message', 'Traning added!');
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
        $traning = Traning::findOrFail($id);

        return view('organization.traning.show', compact('traning'));
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
        $traning = Traning::findOrFail($id);

        return view('organization.traning.edit', compact('traning'));
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
        
        $traning = Traning::findOrFail($id);
        $traning->update($requestData);

        return redirect('organization/traning')->with('flash_message', 'Traning updated!');
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
        Traning::destroy($id);

        return redirect('organization/traning')->with('flash_message', 'Traning deleted!');
    }
}
