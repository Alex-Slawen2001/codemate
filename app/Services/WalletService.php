<?php
namespace App\Services;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
class WalletService
{
    public function deposit(int $userId, float|string $amount, ?string $comment = null): array
    {
        $amount = (float) $amount;
        $user = User::find($userId);
        if (!$user) {
            throw new UserNotFoundException('Пользователь не найден');
        }
        return DB::transaction(function () use ($user, $amount, $comment) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                ]);
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();
            }
            $wallet->balance = bcadd((string)$wallet->balance, (string)$amount, 2);
            $wallet->save();
            $tx = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => WalletTransaction::TYPE_DEPOSIT,
                'amount' => $amount,
                'comment' => $comment,
                'related_user_id' => null,
            ]);
            return [
                'user_id' => $user->id,
                'balance' => (float) $wallet->balance,
                'transaction_id' => $tx->id,
            ];
        });
    }
    public function withdraw(int $userId, float|string $amount, ?string $comment = null): array
    {
        $amount = (float) $amount;
        $user = User::find($userId);
        if (!$user) {
            throw new UserNotFoundException('Пользователь не найден');
        }
        return DB::transaction(function () use ($user, $amount, $comment) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet) {
                throw new InsufficientFundsException('Недостаточно средств');
            }
            if ((float)$wallet->balance < $amount) {
                throw new InsufficientFundsException('Недостаточно средств');
            }
            $wallet->balance = bcsub((string)$wallet->balance, (string)$amount, 2);
            $wallet->save();
            $tx = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => WalletTransaction::TYPE_WITHDRAW,
                'amount' => $amount,
                'comment' => $comment,
                'related_user_id' => null,
            ]);
            return [
                'user_id' => $user->id,
                'balance' => (float) $wallet->balance,
                'transaction_id' => $tx->id,
            ];
        });
    }
    public function transfer(int $fromUserId, int $toUserId, float|string $amount, ?string $comment = null): array
    {
        $amount = (float) $amount;
        $from = User::find($fromUserId);
        $to   = User::find($toUserId);
        if (!$from || !$to) {
            throw new UserNotFoundException('Пользователь не найден');
        }
        if ($from->id === $to->id) {
            abort(422, 'Нельзя переводить самому себе');
        }
        return DB::transaction(function () use ($from, $to, $amount, $comment) {
            $firstId  = min($from->id, $to->id);
            $secondId = max($from->id, $to->id);
            $firstWallet  = Wallet::where('user_id', $firstId)->lockForUpdate()->first();
            $secondWallet = Wallet::where('user_id', $secondId)->lockForUpdate()->first();
            $fromWallet = $from->id === $firstId ? $firstWallet : $secondWallet;
            $toWallet   = $to->id   === $firstId ? $firstWallet : $secondWallet;
            if (!$fromWallet) {
                throw new InsufficientFundsException('Недостаточно средств');
            }
            if ((float)$fromWallet->balance < $amount) {
                throw new InsufficientFundsException('Недостаточно средств');
            }
            if (!$toWallet) {
                $toWallet = Wallet::create([
                    'user_id' => $to->id,
                    'balance' => 0,
                ]);
                $toWallet = Wallet::where('id', $toWallet->id)->lockForUpdate()->first();
            }
            $fromWallet->balance = bcsub((string)$fromWallet->balance, (string)$amount, 2);
            $toWallet->balance   = bcadd((string)$toWallet->balance,   (string)$amount, 2);
            $fromWallet->save();
            $toWallet->save();
            $outTx = WalletTransaction::create([
                'user_id' => $from->id,
                'type' => WalletTransaction::TYPE_TRANSFER_OUT,
                'amount' => $amount,
                'comment' => $comment,
                'related_user_id' => $to->id,
            ]);
            $inTx = WalletTransaction::create([
                'user_id' => $to->id,
                'type' => WalletTransaction::TYPE_TRANSFER_IN,
                'amount' => $amount,
                'comment' => $comment,
                'related_user_id' => $from->id,
            ]);
            return [
                'from_user_id' => $from->id,
                'to_user_id'   => $to->id,
                'from_balance' => (float) $fromWallet->balance,
                'to_balance'   => (float) $toWallet->balance,
                'out_id' => $outTx->id,
                'in_id'  => $inTx->id,
            ];
        });
    }
    public function getBalance(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            throw new UserNotFoundException('Пользователь не найден');
        }
        $wallet = Wallet::where('user_id', $user->id)->first();
        return [
            'balance' => (float) ($wallet?->balance ?? 0.00),
        ];
    }
}
