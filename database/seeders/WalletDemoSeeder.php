<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Services\WalletService;

class WalletDemoSeeder extends Seeder
{
    public function run(): void
    {
        /** @var WalletService $service */
        $service = app(WalletService::class);

        $alice = User::firstOrCreate(
            ['email' => 'alice@example.com'],
            ['name' => 'Alice']
        );

        $bob = User::firstOrCreate(
            ['email' => 'bob@example.com'],
            ['name' => 'Bob']
        );

        $charlie = User::firstOrCreate(
            ['email' => 'charlie@example.com'],
            ['name' => 'Charlie']
        );

        $service->deposit($alice->id, 1000.00, 'Стартовый депозит');
        $service->deposit($bob->id, 300.00, 'Стартовый депозит');

        $service->withdraw($alice->id, 120.00, 'Покупка подписки');
        $service->transfer($alice->id, $bob->id, 150.00, 'Перевод другу');
        $service->transfer($bob->id, $charlie->id, 50.00, 'Чаевые');


        $this->command?->info('WalletDemoSeeder: сгенерированы кошельки и транзакции для Alice, Bob, Charlie.');
    }
}
