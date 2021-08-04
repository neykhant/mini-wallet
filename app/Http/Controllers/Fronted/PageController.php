<?php

namespace App\Http\Controllers\Fronted;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferFormValidateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PageController extends Controller
{
    public function home()
    {
        $user = Auth::guard('web')->user();

        return view('frontend.home', compact('user'));
    }
    public function profile()
    {
        $user = Auth::guard('web')->user();
        return view('frontend.profile', compact('user'));
    }

    public function updatePassword(){
        return view('frontend.update_password');
    }

    public function updatePasswordStore(UpdatePasswordRequest $request){
        
        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $user = Auth::guard('web')->user();

        if(Hash::check($old_password, $user->password)){
            $user->password = Hash::make($new_password);
            $user->update();

            return redirect()->route('profile')->with('update', 'Successfuly updated.');
        }

        return back()->withErrors(['old_password' => 'The old password is not correct.'])->withInput();
    }

    public function wallet(){
        $authUser = auth()->guard('web')->user();
        return view('frontend.wallet', compact('authUser'));
    }

    public function transfer(){
        $authUser = auth()->guard('web')->user();
        return view('frontend.transfer', compact('authUser'));
    }

    public function transferConfirm(TransferFormValidateRequest $request){

        if($request->amount < 1000){
            return back()
                ->withErrors(['amount' => 'The amount must be at least 1000 MMK.'])
                ->withInput();
        }

        $authUser = auth()->guard('web')->user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        return view('frontend.transfer_confirm', compact('authUser','to_phone','amount','description'));
    }
}
