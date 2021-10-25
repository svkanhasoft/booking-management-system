<?php

namespace App\Http\Controllers\API\Signees;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\SigneesDetail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Availability;
use Hash;
use Validator;
use Illuminate\Support\Facades\Auth;

class ScriptController extends Controller
{
    public function __construct()
    {
    }

    public function addsignee(Request $request){
        echo "Fsdsd";
    }
}
