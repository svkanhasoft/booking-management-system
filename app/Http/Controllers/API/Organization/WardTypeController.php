<?php

namespace App\Http\Controllers\API\Organization;

use Illuminate\Http\Request;
use App\Models\WardType;
use App\Http\Controllers\Controller;

class WardTypeController extends Controller
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
     * Show ward by id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ward = WardType::where('id', $id)->first();
        if ($ward) {
            return response()->json(['status' => true, 'message' => 'Ward type get Successfully', 'data' => $ward], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Role not available!', 'status' => false], 404);
        }
    }

    /**
     * Show ward list.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAll()
    {
        $wardTypeList = WardType::select('id as ward_type_id', 'ward_type')->get()->toArray();
        if ($wardTypeList) {
            return response()->json(['status' => true, 'message' => 'Ward type get Successfully', 'data' => $wardTypeList], $this->successStatus);
        } else {
            return response()->json(['message' => 'Sorry, Role not available!', 'status' => false], 404);
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
