<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class UserDashboardController extends Controller
{
    public function dashboard()
    {
        return view('auth.user.dashboard');
    }
}