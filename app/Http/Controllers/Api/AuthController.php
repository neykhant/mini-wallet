<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'string', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->ip = $request->ip();
        $user->user_agent = $request->server('HTTP_USER_AGENT');
        $user->login_at = date('Y-m-d H:m:i');
        $user->save();


        $token = $user->createToken('Magic Pay')->accessToken;

        return success('Successfully Registered.', ['token' => $token]);

    }

    public function login(Request $request){
        $request->validate(
            [
                'phone' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]
        );

        if(Auth::attempt(['phone' => $request->phone, 'password' => $request->password])){
            $user = auth()->user();
            $token = $user->createToken('Magic Pay')->accessToken;

            return success('Successfuly logined.', ['token' => $token]);
        }

        return fail('These credentials do not match our records.', '');
    }
}
