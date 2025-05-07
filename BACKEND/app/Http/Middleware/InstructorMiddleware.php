<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('instructor_api')->check()) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized. You must be an instructor to access this resource.'], 403);
    }
}
