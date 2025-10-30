<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);
        User::factory()->create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
        ]);
    }
}
