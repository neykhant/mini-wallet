<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\TransactionDetailResource;
use App\Http\Resources\TransactionResource;
use App\Transaction;
use Illuminate\Http\Request;

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
}
