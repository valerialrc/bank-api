<?php

namespace Tests\Unit;

use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use App\Models\Account;
use App\Services\BalanceCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TransactionTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_an_account()
    {
        $account = Account::factory()->create();

        $transaction = Transaction::factory()->create([
            'account_id' => $account->id,
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'account_id' => $account->id,
        ]);

        $this->assertInstanceOf(Account::class, $transaction->account);
        $this->assertEquals($account->id, $transaction->account->id);
    }

    public function test_withdraw_returns_insufficient_funds_when_exchange_rate_null()
    {
        $account = new Account();
        $request = new Request([
            'amount' => 100.00,
            'currency' => 'USD',
        ]);

        $mockBalanceCalculatorService = $this->createMock(BalanceCalculatorService::class);
        $mockBalanceCalculatorService->method('convertBalanceToCurrency')->willReturn(null);

        $controller = new TransactionController($mockBalanceCalculatorService);

        $response = $controller->withdraw($account, $request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'message' => 'Insufficient funds']),
            $response->getContent()
        );
    }
}
