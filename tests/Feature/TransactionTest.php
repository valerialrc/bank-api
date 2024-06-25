<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Http\Response;

class TransactionTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_creates_a_transaction()
    {
        Account::factory()->create();

        $transaction = Transaction::factory()->create(['type' => 'deposit']);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
        ]);
    }

    public function test_deposit()
    {
        $account = Account::factory()->create();

        $data = [
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'USD',
        ];

        $response = $this->postJson("/api/accounts/{$account->id}/deposit", $data);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'success' => true,
            'transaction' => [
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'type' => 'deposit',
            ],
        ]);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'type' => 'deposit',
        ]);
    }

    public function test_withdrawal()
    {
        $account = Account::factory()->create();

        $depositData = [
            'amount' => 100,
            'currency' => 'USD',
        ];

        $this->postJson("/api/accounts/{$account->id}/deposit", $depositData);

        $withdrawalData = [
            'amount' => 50,
            'currency' => 'USD',
        ];

        $response = $this->postJson("/api/accounts/{$account->id}/withdraw", $withdrawalData);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'success' => true,
            'transaction' => [
                'amount' => -$withdrawalData['amount'],
                'currency' => $withdrawalData['currency'],
                'type' => 'withdrawal',
            ],
        ]);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'amount' => -$withdrawalData['amount'],
            'currency' => $withdrawalData['currency'],
            'type' => 'withdrawal',
        ]);
    }

    public function test_withdraw_returns_insufficient_funds_message()
    {
        $account = Account::factory()->create();
        $request = new Request([
            'amount' => 100.00,
            'currency' => 'USD',
        ]);

        $controller = new TransactionController();

        $response = $controller->withdraw($account, $request);

        $this->assertEquals(400, $response->status());

        $this->assertJsonStringEqualsJsonString(
            json_encode(['success' => false, 'message' => 'Insufficient funds']),
            $response->getContent()
        );
    }
}
