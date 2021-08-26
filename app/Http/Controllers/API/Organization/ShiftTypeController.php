<?php

namespace App\Http\Controllers\API\Organization;

use App\Models\ShiftType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShiftTypeController extends Controller
{
    public $successStatus = 200;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shift = ShiftType::where('id', $id)->first();
        if ($shift) {
            return response()->json(['status' => true, 'message' => 'Shift type get Successfully', 'data' => $shift], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Role not available!', 'status' => false], 200);
        }
    }

    public function showAll()
    {
        $shiftTypeList = ShiftType::all()->toArray();
        if ($shiftTypeList) {
            return response()->json(['status' => true, 'message' => 'Shift type get Successfully', 'data' => $shiftTypeList], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Role not available!', 'status' => false], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
