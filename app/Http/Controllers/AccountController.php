<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller
{
    public function balance(Account $account, Request $request)
    {
        $currency = $request->get('currency');

        if ($currency) {
            $balance = $this->convertBalanceToCurrency($account, $currency);
            return response()->json(['balance' => $balance, 'currency' => $currency]);
        }

        $balances = $account->transactions()
            ->selectRaw('currency, SUM(amount) as total')
            ->groupBy('currency')
            ->get();

        return response()->json($balances);
    }

    private function convertBalanceToCurrency($account, $currency)
    {
        // TODO
        // Implementar a lógica de conversão utilizando a API do Banco Central
    }
}