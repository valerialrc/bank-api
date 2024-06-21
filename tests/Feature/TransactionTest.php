<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $transaction = Transaction::factory()->create();

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
        ]);
    }

    public function testDeposit()
    {
        $account = Account::factory()->create();

        $data = [
            'amount' => $this->faker->randomFloat(2, 10, 1000), // Valor aleatório entre 10 e 1000
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

    public function testWithdrawal()
    {
        $account = Account::factory()->create();

        // Primeiro, depositamos um valor para garantir saldo na conta
        $depositData = [
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'USD',
        ];

        $this->postJson("/api/accounts/{$account->id}/deposit", $depositData);

        // Dados para o saque
        $withdrawalData = [
            'amount' => $this->faker->randomFloat(2, 1, $depositData['amount']), // Valor aleatório entre 1 e o valor depositado
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
}
