<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',          // 🌟 核心：存入與 MatchRecord 的對應連結
        'lostId', 
        'foundId', 
        'processedBy', 
        'claimerName', 
        'claimerIcPassport', 
        'claimerPhone', 
        'claimedAt',
        'handover_photo' 
    ];

    protected $casts = [
        'claimedAt' => 'datetime',
    ];

    /**
     * 🌟 1. 核心關聯：找到對應的匹配紀錄 (MatchRecord)
     * 讓你可以直接用 $claim->matchRecord->status 查詢匹配狀態
     */
    public function matchRecord()
    {
        return $this->belongsTo(MatchRecord::class, 'match_id');
    }

    /**
     * 2. 關聯：找到對應的 Found Item (顯示物品名字)
     */
    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'foundId');
    }

    /**
     * 3. 關聯：找到對應的 Lost Report (顯示乘客信息)
     */
    public function lostItem()
    {
        return $this->belongsTo(LostItemReport::class, 'lostId');
    }

    /**
     * 4. 關聯：找到經手的員工 (顯示 User 表裡的 name)
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'processedBy');
    }
}