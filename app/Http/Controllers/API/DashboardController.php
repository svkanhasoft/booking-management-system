<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function totalUser()
    {
        $signeeCount = User::where(['role' => 'SIGNEE', 'status'=>'Active'])->get()->count();
        return $signeeCount;

        // $orgCount = User::where('role', 'ORGANIZATION')->get()->count();
        // return $orgCount;
    }
}
