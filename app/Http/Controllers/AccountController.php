<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\BalanceCalculatorService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $balanceCalculator;

    public function __construct(BalanceCalculatorService $balanceCalculator)
    {
        $this->balanceCalculator = $balanceCalculator;
    }

    /**
     * @OA\Get(
     *      path="/api/accounts/{account}/balance",
     *      operationId="getBalance",
     *      tags={"Accounts"},
     *      summary="Get account balance",
     *      description="Get the balance of an account in a specific currency or all currencies",
     *      @OA\Parameter(
     *          name="account",
     *          description="Account ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="currency",
     *          description="Currency code (optional)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              oneOf={
     *                  @OA\Schema(
     *                      type="object",
     *                      @OA\Property(property="balance", type="number", example=100.50),
     *                      @OA\Property(property="currency", type="string", example="USD")
     *                  ),
     *                  @OA\Schema(
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="currency", type="string", example="USD"),
     *                          @OA\Property(property="total", type="number", example=100.50)
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Account not found")
     * )
     */
    public function balance(Account $account, Request $request)
    {
        $currency = $request->get('currency');

        if ($currency) {
            $balance = $this->balanceCalculator->convertBalanceToCurrency($account, $currency);
            return response()->json(['balance' => $balance, 'currency' => $currency]);
        }

        $balances = $account->transactions()
            ->selectRaw('currency, SUM(amount) as total')
            ->groupBy('currency')
            ->get();

        return response()->json($balances);
    }

    
}
