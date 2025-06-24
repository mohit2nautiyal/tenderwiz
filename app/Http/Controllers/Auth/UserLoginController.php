<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    public function index()
    {
        return view('auth.user.login');
    }

    // public function check(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => ['required', 'email'],
    //         'password' => ['required'],
    //     ]);

    //     if (Auth::attempt(array_merge($credentials, ['role' => 'user']))) {
    //         $request->session()->regenerate();
    //         return redirect()->intended('user/tenders');
    //     }

    //     return back()->withErrors(['email' => 'Invalid credentials']);
    // }



 public function check(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt(array_merge($credentials, ['role' => 'user']))) {
        $user = Auth::user();

        // Check subscription end date
        if ($user->subscription_end_date && $user->subscription_end_date < now()->toDateString()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your subscription has expired.']);
        }

        $request->session()->regenerate();
        return redirect()->intended('user/tenders');
    }

    return back()->withErrors(['email' => 'Invalid credentials']);
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}