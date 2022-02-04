<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public $successStatus = 200;

    public function dashboard(Request $request,$year)
    {


        $objUser = new User();
        $data['monthly_details'] = $objUser->getDashboard('monthly_details',$year);
        $data['yearly_details'] = $objUser->getDashboard('yearly_details');

        $data['today'] = $objUser->getDashboard('today');
        $data['week'] = $objUser->getDashboard('week');
        $data['month'] = $objUser->getDashboard('month');
        $data['year'] = $objUser->getDashboard('year');
        $data['total_user'] = $objUser->getDashboard('total_user');
        $data['block_user'] = $objUser->getDashboard('block_user');

        return response()->json(['message' => 'Dashboard detaild get successfully', 'status' => true, 'data' => $data], $this->successStatus);
    }
}
