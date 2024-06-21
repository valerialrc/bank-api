<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\BalanceCalculatorService;

class BalanceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $balanceCalculator;

    public function setUp(): void
    {
        parent::setUp();

        $this->balanceCalculator = app(BalanceCalculatorService::class);
    }

    public function test_account_balance_single_currency()
    {
        $account = Account::factory()->create();

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 100.50,
            'currency' => 'USD',
            'type' => 'deposit',
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => -50.75,
            'currency' => 'USD',
            'type' => 'withdrawal',
        ]);

        $response = $this->get("/api/accounts/{$account->id}/balance");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'currency',
                'total',
            ]
        ]);

        $responseData = $response->json();

        $usdBalance = collect($responseData)->firstWhere('currency', 'USD')['total'];
        $this->assertEquals(49.75, $usdBalance);
    }

    public function test_account_balance_multiple_currencies()
    {
        $account = Account::factory()->create();

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 100.00,
            'currency' => 'USD',
            'type' => 'deposit',
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 200.00,
            'currency' => 'AUD',
            'type' => 'deposit',
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => -50.00,
            'currency' => 'USD',
            'type' => 'withdrawal',
        ]);

        $response = $this->get("/api/accounts/{$account->id}/balance");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'currency',
                'total',
            ]
        ]);

        $responseData = $response->json();

        $usdBalance = collect($responseData)->firstWhere('currency', 'USD')['total'];
        $this->assertEquals(50.00, $usdBalance);

        $audBalance = collect($responseData)->firstWhere('currency', 'AUD')['total'];
        $this->assertEquals(200.00, $audBalance);
    }

    public function test_account_balance_selected_currency()
    {
        $account = Account::factory()->create();

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 100.00,
            'currency' => 'USD',
            'type' => 'deposit',
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 200.00,
            'currency' => 'AUD',
            'type' => 'deposit',
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => -50.00,
            'currency' => 'USD',
            'type' => 'withdrawal',
        ]);

        $response = $this->get("/api/accounts/{$account->id}/balance?currency=USD");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'currency',
            'balance',
        ]);

        $responseData = $response->json();
        $this->assertEquals('USD', $responseData['currency']);

        $convertedBalance = $this->balanceCalculator->convertBalanceToCurrency($account, 'USD');
        $this->assertEquals($convertedBalance, $responseData['balance']);
    }
}
