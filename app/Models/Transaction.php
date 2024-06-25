<?php

namespace App\Models;

use App\Services\BalanceCalculatorService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['account_id', 'amount', 'currency', 'type'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if ($transaction->type === 'withdrawal') {
                $account = $transaction->account;
                $balanceCalculator = resolve(BalanceCalculatorService::class);
                $totalBalance = $balanceCalculator->convertBalanceToCurrency($account, $transaction->currency);

                if ($totalBalance < abs($transaction->amount)) {
                    return false;
                }
            }
        });
    }
}
