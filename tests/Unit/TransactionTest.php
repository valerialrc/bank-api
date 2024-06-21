<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\Account;
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
}
