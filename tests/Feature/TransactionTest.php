<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_transaction()
    {
        $transaction = Transaction::factory()->create();

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
        ]);
    }
}
