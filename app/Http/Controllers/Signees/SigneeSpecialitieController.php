<?php

namespace App\Http\Controllers\Signees;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\SigneeSpecialitie;
use Illuminate\Http\Request;

class SigneeSpecialitieController extends Controller
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
            $signeespecialitie = SigneeSpecialitie::where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('speciality_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $signeespecialitie = SigneeSpecialitie::latest()->paginate($perPage);
        }

        return view('signees.signee-specialitie.index', compact('signeespecialitie'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('signees.signee-specialitie.create');
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
        
        SigneeSpecialitie::create($requestData);

        return redirect('signees/signee-specialitie')->with('flash_message', 'SigneeSpecialitie added!');
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
        $signeespecialitie = SigneeSpecialitie::findOrFail($id);

        return view('signees.signee-specialitie.show', compact('signeespecialitie'));
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
        $signeespecialitie = SigneeSpecialitie::findOrFail($id);

        return view('signees.signee-specialitie.edit', compact('signeespecialitie'));
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
        
        $signeespecialitie = SigneeSpecialitie::findOrFail($id);
        $signeespecialitie->update($requestData);

        return redirect('signees/signee-specialitie')->with('flash_message', 'SigneeSpecialitie updated!');
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
        SigneeSpecialitie::destroy($id);

        return redirect('signees/signee-specialitie')->with('flash_message', 'SigneeSpecialitie deleted!');
    }
}
