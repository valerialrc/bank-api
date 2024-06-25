<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel Swagger API Documentation",
 *      description="API Documentation for Laravel Application",
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="Laravel API Server"
 * )
 */
class TransactionController extends Controller
{
     /**
     * @OA\Post(
     *      path="/api/accounts/{account}/deposit",
     *      operationId="deposit",
     *      tags={"Transactions"},
     *      summary="Deposit to an account",
     *      description="Deposit money to an account",
     *      @OA\Parameter(
     *          name="account",
     *          description="Account ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"amount","currency"},
     *              @OA\Property(property="amount", type="number", example=100.50),
     *              @OA\Property(property="currency", type="string", example="USD")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="transaction", type="object")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function deposit(Account $account, Request $request)
    {
        $transaction = $account->transactions()->create([
            'amount' => $request->amount,
            'currency' => $request->currency,
            'type' => 'deposit',
        ]);

        return response()->json(['success' => true, 'transaction' => $transaction]);
    }

    /**
     * @OA\Post(
     *      path="/api/accounts/{account}/withdraw",
     *      operationId="withdraw",
     *      tags={"Transactions"},
     *      summary="Withdraw from an account",
     *      description="Withdraw money from an account",
     *      @OA\Parameter(
     *          name="account",
     *          description="Account ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"amount","currency"},
     *              @OA\Property(property="amount", type="number", example=100.50),
     *              @OA\Property(property="currency", type="string", example="USD")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="transaction", type="object")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function withdraw(Account $account, Request $request)
    {
        $transaction = $account->transactions()->create([
            'amount' => -$request->amount,
            'currency' => $request->currency,
            'type' => 'withdrawal',
        ]);

        if (!$transaction->save()) {
            return response()->json(['success' => false, 'message' => 'Insufficient funds'], 400);
        }

        return response()->json(['success' => true, 'transaction' => $transaction]);
    }
}