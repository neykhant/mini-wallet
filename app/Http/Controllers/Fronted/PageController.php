<?php

namespace App\Http\Controllers\Fronted;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function home()
    {
        return view('frontend.home');
    }
    public function profile()
    {
        $user = Auth::guard('web')->user();
        return view('frontend.profile', compact('user'));
    }
}
