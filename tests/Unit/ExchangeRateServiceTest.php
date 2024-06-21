<?php

namespace Tests\Unit\Services;

use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    public function test_get_exchange_rate_http_failure()
    {
        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $service = new ExchangeRateService();
        $currency = 'USD';
        $date = '2024-06-20';

        $exchangeRate = $service->getExchangeRate($currency, $date);

        $this->assertNull($exchangeRate);
    }
}
