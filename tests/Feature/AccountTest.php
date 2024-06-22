<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Account;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_create_account()
    {
        $account = Account::factory()->create();

        $this->assertNotNull($account);

        $this->assertMatchesRegularExpression('/\d{4}-\d{2}/', $account->account_number);
    }
}
