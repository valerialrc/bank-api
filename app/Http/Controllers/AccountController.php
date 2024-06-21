<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountController extends Controller
{
    protected $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

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

    private function convertBalanceToCurrency($account, $targetCurrency)
    {
        // Usa o dia anterior como fechamento
        $date = Carbon::now()->subDay()->format('m-d-Y');

        $totalBalanceInBRL = 0;

        $balances = $account->transactions()
            ->selectRaw('currency, SUM(amount) as total')
            ->groupBy('currency')
            ->get();

        foreach ($balances as $balance) {
            if ($balance->currency === 'BRL') {
                $totalBalanceInBRL += $balance->total;
            } else {
                $exchangeRate = $this->exchangeRateService->getExchangeRate($balance->currency, $date);

                if ($exchangeRate) {
                    $totalBalanceInBRL += $balance->total * $exchangeRate['cotacaoCompra'];
                }
            }
        }

        if ($targetCurrency === 'BRL') {
            return $totalBalanceInBRL;
        }
    
        $targetExchangeRate = $this->exchangeRateService->getExchangeRate($targetCurrency, $date);

        if ($targetExchangeRate) {
            return $totalBalanceInBRL / $targetExchangeRate['cotacaoVenda'];
        }

        return 0;
    }
}
