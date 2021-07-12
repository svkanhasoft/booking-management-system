<?php

namespace App\Http\Controllers\API\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Traning;
use Illuminate\Http\Request;

class TraningController extends Controller
{
    public $successStatus = 200;
    protected $userId;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!empty(Auth::user())) {
                $this->userId = Auth::user()->id;
            }
            return $next($request);
        });
    }
}
