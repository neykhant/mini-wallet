<?php

namespace App\Http\Controllers\Fronted;

use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferFormValidateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Stmt\TryCatch;

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

    public function updatePassword()
    {
        return view('frontend.update_password');
    }

    public function updatePasswordStore(UpdatePasswordRequest $request)
    {

        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $user = Auth::guard('web')->user();

        if (Hash::check($old_password, $user->password)) {
            $user->password = Hash::make($new_password);
            $user->update();

            return redirect()->route('profile')->with('update', 'Successfuly updated.');
        }

        return back()->withErrors(['old_password' => 'The old password is not correct.'])->withInput();
    }

    public function wallet()
    {
        $authUser = auth()->guard('web')->user();
        return view('frontend.wallet', compact('authUser'));
    }

    public function transfer()
    {
        $authUser = auth()->guard('web')->user();
        return view('frontend.transfer', compact('authUser'));
    }

    public function transferConfirm(TransferFormValidateRequest $request)
    {
        if ($request->amount < 1000) {
            return back()
                ->withErrors(['amount' => 'The amount must be at least 1000 MMK.'])
                ->withInput();
        }

        $authUser = auth()->guard('web')->user();

        if ($authUser->phone == $request->to_phone) {
            return back()
                ->withErrors(['to_phone' => 'To account is invalide.'])
                ->withInput();
        }

        $to_account = User::where('phone', $request->to_phone)->first();

        if (!$to_account) {
            return back()
                ->withErrors(['to_phone' => 'To account is invalide.'])
                ->withInput();
        }

        $from_account = $authUser;
        // $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        return view('frontend.transfer_confirm', compact('from_account', 'to_account', 'amount', 'description'));
    }

    public function transferComplete(TransferFormValidateRequest $request)
    {
        // return $request->all();

        if ($request->amount < 1000) {
            return back()
                ->withErrors(['amount' => 'The amount must be at least 1000 MMK.'])
                ->withInput();
        }
        $authUser = auth()->guard('web')->user();

        if ($authUser->phone == $request->to_phone) {
            return back()
                ->withErrors(['to_phone' => 'To account is invalide.'])
                ->withInput();
        }

        $to_account = User::where('phone', $request->to_phone)->first();

        if (!$to_account) {
            return back()
                ->withErrors(['to_phone' => 'To account is invalide.'])
                ->withInput();
        }

        $from_account = $authUser;
        // $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        if (!$from_account->wallet || !$to_account->wallet) {
            return back()
                ->withErrors(['fail' => 'Something worng. The given data is invalid.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount', $amount);
            $from_account_wallet->update();


            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount', $amount);
            $to_account_wallet->update();

            // transaction for tables

            $ref_no = UUIDGenerate::refNumber();

            $from_account_transaction = new Transaction();
            $from_account_transaction->ref_no = $ref_no;
            $from_account_transaction->trx_id = UUIDGenerate::trxId();
            $from_account_transaction->user_id = $from_account->id;
            $from_account_transaction->type = 2;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description;
            $from_account_transaction->save();


            $to_account_transaction = new Transaction();
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id =  $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount;
            $to_account_transaction->source_id = $from_account->id;
            $to_account_transaction->description = $description;
            $to_account_transaction->save();

            // transaction table

            DB::commit();
            return redirect('/')->with('transfer_success', 'Successfuly transfered.');
        } catch (\Exception $error) {
            DB::rollBack();

            return back()
                ->withErrors(['fail', 'Something wrong.' + $error->getMessage()])
                ->withInput();
        }
    }


    public function transaction(){
        $authUser = auth()->guard('web')->user();
        $transactions = Transaction::where('user_id', $authUser->id)->with('user', 'source')
                                    ->orderBy('created_at','DESC')->paginate(5);

        return view('frontend.transaction', compact('transactions'));
    }


    public function transactionDetails($trx_id){
        $authUser = auth()->guard('web')->user();
        $transaction = Transaction::with('user', 'source')->where('user_id', $authUser->id)
                                    ->where('trx_id', $trx_id)->first();

        return view('frontend.transaction_detail', compact('transaction'));
    }


    public function toAccountVerify(Request $request)
    {
        $authUser = auth()->guard('web')->user();
        if ($authUser->phone != $request->phone) {

            $user = User::where('phone', $request->phone)->first();
            if ($user) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'success',
                    'data' => $user
                ]);
            }
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'Invalid data',
        ]);
    }

    public function passwordCheck(Request $request)
    {
        if (!$request->password) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Please fill your password.',
            ]);
        }

        $authUser = auth()->guard('web')->user();
        if (Hash::check($request->password, $authUser->password)) {
            return response()->json([
                'status' => 'success',
                'message' => 'The password is correct',
            ]);
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'The password is incorrect',
        ]);
    }
}
