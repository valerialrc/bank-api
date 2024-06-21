<?php

namespace Tests\Unit\Services;

use App\Models\Account;
use App\Services\BalanceCalculatorService;
use App\Services\ExchangeRateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BalanceCalculatorServiceTest extends TestCase
{
    public function test_convert_balance_to_currency_returns_zero_when_currency_is_null()
    {
        $account = Account::factory()->create();

        $exchangeRateService = $this->createMock(ExchangeRateService::class);

        $balanceCalculatorService = new BalanceCalculatorService($exchangeRateService);

        $targetCurrency = 'ZZZ';
        $result = $balanceCalculatorService->convertBalanceToCurrency($account, $targetCurrency);

        $this->assertEquals(0, $result);
    }
}
