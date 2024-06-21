<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Services\ExchangeRateService;

class TransactionController extends Controller
{
    protected $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }
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
        $exchangeRate = $this->exchangeRateService->getExchangeRate($currency, now()->format('m-d-Y'));
        
        if ($exchangeRate) {
            $balanceInCurrency = $account->transactions()
                ->where('currency', $currency)
                ->sum('amount');
            
                $totalBalance = $balanceInCurrency * $exchangeRate['cotacaoVenda'];
                return $totalBalance;
        }

        return 0;
    }
}