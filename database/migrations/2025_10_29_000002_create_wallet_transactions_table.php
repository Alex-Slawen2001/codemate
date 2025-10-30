<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\WalletTransaction;
return new class extends Migration {
    public function up(): void {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                WalletTransaction::TYPE_DEPOSIT,
                WalletTransaction::TYPE_WITHDRAW,
                WalletTransaction::TYPE_TRANSFER_IN,
                WalletTransaction::TYPE_TRANSFER_OUT,
            ]);

            $table->decimal('amount', 20, 2);
            $table->string('comment', 255)->nullable();
            $table->foreignId('related_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'type']);
            $table->index('created_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('wallet_transactions');
    }
};
