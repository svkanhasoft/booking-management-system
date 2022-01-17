<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public $successStatus = 200;

    public function dashboard()
    {
        // $signeeCount = User::where(['role' => 'SIGNEE', 'status'=>'Active'])->get()->count();
        // return $signeeCount;
        $toDay = date('Y-m-d');
        $objUser = new User();

        $data['today'] = $objUser->getDashboard('today');
        $data['week'] = $objUser->getDashboard('week');
        $data['month'] = $objUser->getDashboard('month');
        $data['year'] = $objUser->getDashboard('year');
        // $orgCount = User::where('role', 'ORGANIZATION')->get()->count();
        // return $orgCount;
        return response()->json(['message' => 'Dashboard get successfully', 'status' => true, 'data' => $data], $this->successStatus);

    }
}
