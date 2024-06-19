<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class TransactionController extends Controller
{
    public function deposit(Account $account, Request $request)
    {
        $transaction = $account->transactions()->create([
            'amount' => $request->amount,
            'currency' => $request->currency,
            'type' => 'deposit',
        ]);

        return response()->json(['success' => true, 'transaction' => $transaction]);
    }

    public function withdraw(Account $account, Request $request)
    {
        $totalBalance = $this->calculateTotalBalance($account, $request->currency);
        
        if ($totalBalance < $request->amount) {
            return response()->json(['success' => false, 'message' => 'Insufficient funds'], 400);
        }

        $transaction = $account->transactions()->create([
            'amount' => -$request->amount,
            'currency' => $request->currency,
            'type' => 'withdrawal',
        ]);

        return response()->json(['success' => true, 'transaction' => $transaction]);
    }

    private function calculateTotalBalance($account, $currency)
    {
        // TODO
        // Implementar a lógica para calcular o saldo total utilizando a API do Banco Central
    }
}