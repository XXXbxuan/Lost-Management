<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',          // 🌟 這是你加的：核心連結
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
     */
    public function matchRecord()
    {
        return $this->belongsTo(MatchRecord::class, 'match_id');
    }

    /**
     * 2. 關聯：找到對應的 Found Item
     */
    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'foundId');
    }

    /**
     * 3. 關聯：找到對應的 Lost Report
     */
    public function lostItem()
    {
        return $this->belongsTo(LostItemReport::class, 'lostId');
    }

    /**
     * 4. 關聯：找到經手的員工
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'processedBy');
    }
}