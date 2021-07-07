<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!empty(trim($request->header('authorization')))) {
            $is_exists = User::where('id', Auth::guard('api')->id())->exists();
            if ($is_exists) {
                return $next($request);
            }
        }
        return response()->json(['status' => false, 'message' => "Invalid Token"], 401);
    }
}
