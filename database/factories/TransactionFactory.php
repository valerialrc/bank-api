<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Account;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accountIds = Account::pluck('id')->toArray();
        $currencies = ['AUD', 'CAD', 'CHF', 'DKK', 'EUR', 'GBP', 'JPY', 'NOK', 'SEK', 'USD'];
        $type = $this->faker->randomElement(['deposit', 'withdrawal']);
        $amount = $this->faker->randomFloat(2, 1, 10000);

        if ($type === 'withdrawal') {
            $amount = -$amount;
        }

        return [
            'amount' => $amount,
            'currency' => $this->faker->randomElement($currencies),
            'account_id' => $this->faker->randomElement($accountIds),
            'type' => $type,
        ];
    }
}
