<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'lostId', 'foundId', 'processedBy', 
        'claimerName', 'claimerIcPassport', 'claimerPhone', 'claimedAt'
    ];

    protected $casts = [
        'claimedAt' => 'datetime',
    ];

    // 1. 关联：找到对应的 Found Item (显示物品名字)
    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'foundId');
    }

    // 2. 关联：找到对应的 Lost Report (显示乘客信息)
    public function lostReport()
    {
        return $this->belongsTo(LostItemReport::class, 'lostId');
    }

    // 3. 关联：找到经手的员工 (显示 User 表里的 name)
    public function handler()
    {
        return $this->belongsTo(User::class, 'processedBy');
    }
}