<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Requests\TransferRequest;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
class WalletController extends Controller
{
    public function __construct(private WalletService $service) {}
    public function deposit(DepositRequest $request): JsonResponse
    {
        $res = $this->service->deposit(
            $request->validated('user_id'),
            $request->validated('amount'),
            $request->validated('comment') ?? null
        );
        return response()->json([
            'user_id' => $res['user_id'],
            'balance' => $res['balance'],
            'transaction_id' => $res['transaction_id'],
        ], 200);
    }
    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        $res = $this->service->withdraw(
            $request->validated('user_id'),
            $request->validated('amount'),
            $request->validated('comment') ?? null
        );
        return response()->json([
            'user_id' => $res['user_id'],
            'balance' => $res['balance'],
            'transaction_id' => $res['transaction_id'],
        ], 200);
    }
    public function transfer(TransferRequest $request): JsonResponse
    {
        $res = $this->service->transfer(
            $request->validated('from_user_id'),
            $request->validated('to_user_id'),
            $request->validated('amount'),
            $request->validated('comment') ?? null
        );
        return response()->json([
            'from_user_id' => $res['from_user_id'],
            'to_user_id'   => $res['to_user_id'],
            'from_balance' => $res['from_balance'],
            'to_balance'   => $res['to_balance'],
            'transactions' => [
                'out_id' => $res['out_id'],
                'in_id'  => $res['in_id'],
            ]
        ], 200);
    }
    public function balance(int $user_id): JsonResponse
    {
        $res = $this->service->getBalance($user_id);
        return response()->json([
            'user_id' => $user_id,
            'balance' => $res['balance'],
        ], 200);
    }
    public function forms() {
        return view('forms');
    }
}
