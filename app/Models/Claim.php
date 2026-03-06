<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'lostId', 
        'foundId', 
        'processedBy', 
        'claimerName', 
        'claimerIcPassport', 
        'claimerPhone', 
        'claimedAt',
        'handover_photo' // 🌟 補上這個！允許存入現場結案照片
    ];

    protected $casts = [
        'claimedAt' => 'datetime',
    ];

    // 1. 關聯：找到對應的 Found Item (顯示物品名字)
    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'foundId');
    }

    // 2. 🌟 關聯：找到對應的 Lost Report (顯示乘客信息)
    public function lostItem()
    {
        return $this->belongsTo(LostItemReport::class, 'lostId');
    }

    // 3. 關聯：找到經手的員工 (顯示 User 表裡的 name)
    public function handler()
    {
        return $this->belongsTo(User::class, 'processedBy');
    }
}