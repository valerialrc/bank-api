<?php

namespace Tests\Unit;

use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use App\Models\Account;
use App\Services\ExchangeRateService;
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
        // Criar uma instância mock de Account e Request
        $account = new Account();
        $request = new Request([
            'amount' => 100.00,
            'currency' => 'USD',
        ]);

        // Mock do ExchangeRateService para retornar null
        $mockExchangeRateService = $this->createMock(ExchangeRateService::class);
        $mockExchangeRateService->method('getExchangeRate')->willReturn(null);

        // Instanciar o TransactionController com o mock do ExchangeRateService
        $controller = new TransactionController($mockExchangeRateService);

        // Chamar o método withdraw
        $response = $controller->withdraw($account, $request);

        // Verificar se a resposta é um JSON com sucesso false e mensagem de funds insuficientes
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'message' => 'Insufficient funds']),
            $response->getContent()
        );
    }
}
