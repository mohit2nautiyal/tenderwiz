<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class UserCheck
// {
//     public function handle(Request $request, Closure $next)
//     {
//         if (Auth::check() && Auth::user()->role === 'user') {
//             return $next($request);
//         }

//         return redirect()->route('user.login')->with('error', 'Unauthorized access');
//     }
// }