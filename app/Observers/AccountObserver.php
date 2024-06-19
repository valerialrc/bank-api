<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    /**
     * Gera um número de conta aleatório no formato NNNN-NN.
     *
     * @return string
     */
    protected function generateAccountNumber()
    {
        do {
            $accountNumber = sprintf('%04d-%02d', mt_rand(0, 9999), mt_rand(0, 99));
        } while (Account::where('account_number', $accountNumber) -> exists());

        return $accountNumber;
    }

    /**
     * Handle the Account "creating" event.
     */
    public function creating(Account $account): void
    {
        $account->account_number = $this->generateAccountNumber();
    }

    /**
     * Handle the Account "updated" event.
     */
    public function updated(Account $account): void
    {
        //
    }

    /**
     * Handle the Account "deleted" event.
     */
    public function deleted(Account $account): void
    {
        //
    }

    /**
     * Handle the Account "restored" event.
     */
    public function restored(Account $account): void
    {
        //
    }

    /**
     * Handle the Account "force deleted" event.
     */
    public function forceDeleted(Account $account): void
    {
        //
    }
}
