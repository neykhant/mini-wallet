<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferFormValidateRequest;
use App\Http\Resources\NotificationDetailResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\TransactionDetailResource;
use App\Http\Resources\TransactionResource;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class PageController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        $data = new ProfileResource($user);
        return success('Success', $data);
    }

    public function transaction(Request $request)
    {
        $authUser = auth()->user();
        $transactions = Transaction::where('user_id', $authUser->id)
            ->with('user', 'source')
            ->orderBy('created_at', 'DESC');

        if ($request->date) {
            $transactions = $transactions->whereDate('created_at', $request->date);
        }

        if ($request->type) {
            $transactions = $transactions->where('type', $request->type);
        }

        $transactions = $transactions->paginate(5);

        $data = TransactionResource::collection($transactions)->additional(['result' => 1, 'message' => 'success']);

        return $data;
    }

    public function transactionDetail($trx_id)
    {
        $authUser = auth()->user();
        $transaction = Transaction::with('user', 'source')->where('user_id', $authUser->id)->where('trx_id', $trx_id)->firstOrFail();

        $data = new TransactionDetailResource($transaction);

        return success('success', $data);
    }

    public function notification()
    {
        $authUser = auth()->user();
        $notifications = $authUser->notifications()->paginate(5);

        return NotificationResource::collection($notifications)->additional(['result' => 1, 'message' => 'success']);
    }

    public function notificationDetail($id)
    {
        $authUser = auth()->user();
        $notification = $authUser->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        $data = new NotificationDetailResource($notification);
        return success('success', $data);
    }

    public function toAccountVerify(Request $request)
    {

        if ($request->phone) {

            $authUser = auth()->user();

            if ($authUser->phone != $request->phone) {

                $user = User::where('phone', $request->phone)->first();
                if ($user) {
                    return success('success', ['name' => $user->name, 'phone' => $user->phone]);
                }
            }
        }

        return fail('Invalid Data', null);
    }

    public function transferConfirm(TransferFormValidateRequest $request)
    {
        $authUser = auth()->user();

        $from_account = $authUser;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac('sha256', $str, 'magicpay123!@#');
        // console.log($hash_value2);

        if ($hash_value !== $hash_value2) {

            // return $hash_value2;
            // b88d8f2c3284483a4d3039bae36f38405cd88ad87555fc12fab2866219765774

            // return $hash_value;
            // eb1bf6e88591e99cd714267883e6e545aa74ca52a71ac9807808bb84bcf58ca7

            return fail('The given data is invalidm.', null);
        }

        if ($amount < 1000) {
            return fail('The amount must be at least 1000 MMK.', null);
        }

        if ($from_account->phone == $to_phone) {
            return fail('To account is invalide.', null);
        }

        $to_account = User::where('phone', $request->to_phone)->first();
        if (!$to_account) {
            return fail('To account is invalide.', null);
        }

        if (!$from_account->wallet || !$to_account->wallet) {
            return fail('The given data is invalid.', null);
        }

        if ($from_account->wallet->amount < $amount) {
            return fail('The amount is not enought.', null);
        }

        return success('success', [
            'from_name' => $from_account->name,
            'from_phone' => $from_account->phone,

            'to_name' => $to_account->name,
            'to_phone' => $to_account->phone,

            'amount' => $amount,
            'description' => $description,
            'hash_value' => $hash_value,
        ]);
    }
}
