<?php

namespace App\Http\Controllers\Fronted;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(){
        $user = auth()->guard('web')->user();
        $notifications = $user->notifications()->paginate(5);

        return view('frontend.notification', compact('notifications'));
    }
}
