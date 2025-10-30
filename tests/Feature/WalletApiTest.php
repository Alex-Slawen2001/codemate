<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class WalletApiTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed(\Database\Seeders\UserSeeder::class);
    }
    public function test_deposit_creates_wallet_and_increases_balance(): void
    {
        $user = User::first();
        $res = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => 500.00,
            'comment' => 'Пополнение через карту',
        ])->assertStatus(200)
            ->assertJsonStructure(['user_id','balance','transaction_id']);
        $this->assertEquals(500.00, $res->json('balance'));
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
        ]);
    }
    public function test_withdraw_fails_when_no_wallet(): void
    {
        $user = User::first();
        $this->postJson('/api/withdraw', [
            'user_id' => $user->id,
            'amount' => 100.00,
        ])->assertStatus(409);
    }
    public function test_withdraw_after_deposit(): void
    {
        $user = User::first();
        $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => 300.00,
        ])->assertStatus(200);
        $res = $this->postJson('/api/withdraw', [
            'user_id' => $user->id,
            'amount' => 100.00,
        ])->assertStatus(200);
        $this->assertEquals(200.00, $res->json('balance'));
    }
    public function test_transfer_between_users(): void
    {
        $from = User::first();
        $to   = User::where('id','!=',$from->id)->first();
        $this->postJson('/api/deposit', [
            'user_id' => $from->id,
            'amount' => 500.00,
        ])->assertStatus(200);
        $res = $this->postJson('/api/transfer', [
            'from_user_id' => $from->id,
            'to_user_id' => $to->id,
            'amount' => 150.00,
            'comment' => 'Перевод другу',
        ])->assertStatus(200);
        $this->assertEquals(350.00, $res->json('from_balance'));
        $balFrom = $this->getJson('/api/balance/'.$from->id)->json('balance');
        $balTo   = $this->getJson('/api/balance/'.$to->id)->json('balance');
        $this->assertEquals(350.00, $balFrom);
        $this->assertEquals(150.00, $balTo);
    }
    public function test_balance_for_user_without_wallet_is_zero(): void
    {
        $user = User::first();
        $res = $this->getJson('/api/balance/'.$user->id)
            ->assertStatus(200);
        $this->assertEquals(0.00, $res->json('balance'));
    }
    public function test_validation_errors(): void
    {
        $this->postJson('/api/deposit', [
            'user_id' => 999999,
            'amount' => -10,
        ])->assertStatus(422);
    }
}
