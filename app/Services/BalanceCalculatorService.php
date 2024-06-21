<?php

namespace App\Services;

use App\Models\Account;
use App\Services\ExchangeRateService;
use Carbon\Carbon;

class BalanceCalculatorService
{
    protected $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }
    public function convertBalanceToCurrency(Account $account, $targetCurrency)
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
