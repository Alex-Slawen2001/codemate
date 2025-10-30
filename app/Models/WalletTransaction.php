<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class WalletTransaction extends Model
{
    use HasFactory;
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';
    public const TYPE_TRANSFER_IN = 'transfer_in';
    public const TYPE_TRANSFER_OUT = 'transfer_out';
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'comment',
        'related_user_id',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function relatedUser()
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }
}
