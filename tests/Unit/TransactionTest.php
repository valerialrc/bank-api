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
            'type' => 'deposit',
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'account_id' => $account->id,
        ]);

        $this->assertInstanceOf(Account::class, $transaction->account);
        $this->assertEquals($account->id, $transaction->account->id);
    }

    public function test_withdraw_returns_insufficient_funds_when_balance_is_low()
    {
        $account = Account::factory()->create();

        Transaction::factory()->create([
            'amount' => 50.00,
            'currency' => 'USD',
            'type' => 'deposit',
            'account_id' => $account->id,
        ]);

        $transaction = new Transaction([
            'amount' => -100.00,
            'currency' => 'USD',
            'type' => 'withdrawal',
            'account_id' => $account->id,
        ]);

        $result = $transaction->save();

        $this->assertFalse($result);

        $this->assertDatabaseMissing('transactions', [
            'account_id' => $account->id,
            'amount' => -100.00,
            'currency' => 'USD',
            'type' => 'withdrawal',
        ]);
    }
}
