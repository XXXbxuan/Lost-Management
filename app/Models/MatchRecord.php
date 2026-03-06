<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'lostId', 'foundId', 'notes', 'status', 'verifiedBy', 'verifiedAt', 
        'similarityScore', 'appointment_at', 'appointment_venue', 
        'verification_token', 'is_confirmed', 'confirmed_at',
        'suggested_time_1', 'suggested_time_2', 'suggested_remarks', 'rejected_at',
    ];

    protected $casts = [
        'appointment_at'   => 'datetime',
        'confirmed_at'     => 'datetime',
        'verifiedAt'       => 'datetime',
        'rejected_at'      => 'datetime',
        'suggested_time_1' => 'datetime',
        'suggested_time_2' => 'datetime',
        'is_confirmed'     => 'boolean',
    ];

    // --- 核心關聯 ---

    public function lostItem()
    {
        return $this->belongsTo(LostItemReport::class, 'lostId');
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'foundId');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifiedBy');
    }

    public function claim()
    {
        return $this->hasOne(Claim::class, 'match_id');
    }

    /**
     * 🌟 快捷關聯：直接抓取「登記這筆報失單」的 Staff
     * 這樣你在 Part 02 就可以直接用 $match->lostItemRegistrar->name 拿名字
     */
    public function lostItemRegistrar()
    {
        // 透過 lostItem 找到對應的 Staff (假設 LostItemReport 裡關聯是 staff)
        return $this->lostItem->belongsTo(User::class, 'staff_id');
    }

    // --- 數據提取 ---

    public function getAuditLogsAttribute()
    {
        // 這裡維持原樣，這是你最強的證據提取器
        return AdminActionLog::where('target_name', 'like', "%Match #{$this->id}%")
            ->orWhere('target_name', 'like', "%#{$this->lostId}%")
            ->orWhere('target_name', 'like', "%#{$this->foundId}%")
            ->orderBy('created_at', 'asc')
            ->get();
    }
}