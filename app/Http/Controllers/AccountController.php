<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\BalanceCalculatorService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $balanceCalculator;

    public function __construct(BalanceCalculatorService $balanceCalculator)
    {
        $this->balanceCalculator = $balanceCalculator;
    }
    public function balance(Account $account, Request $request)
    {
        $currency = $request->get('currency');

        if ($currency) {
            $balance = $this->balanceCalculator->convertBalanceToCurrency($account, $currency);
            return response()->json(['balance' => $balance, 'currency' => $currency]);
        }

        $balances = $account->transactions()
            ->selectRaw('currency, SUM(amount) as total')
            ->groupBy('currency')
            ->get();

        return response()->json($balances);
    }

    
}
