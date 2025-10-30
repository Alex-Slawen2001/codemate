<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 20, 2)->default(0);
            $table->timestamps();
        });
        try {
            DB::statement('ALTER TABLE wallets ADD CONSTRAINT chk_wallets_balance_nonnegative CHECK (balance >= 0)');
        } catch (\Throwable $e) {

        }
    }
    public function down(): void {
        Schema::dropIfExists('wallets');
    }
};
