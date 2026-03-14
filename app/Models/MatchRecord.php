<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        // 核心配對資訊
        'lostId', 'foundId', 'notes', 'status', 'verifiedBy', 'verifiedAt', 
        'similarityScore', 

        // 預約與領取系統 (Stage 02)
        'appointment_at', 'appointment_venue', 
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

    // --- 🌟 核心關聯 ---

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
     * 用法：$match->registrar->name
     */
    public function getRegistrarAttribute()
    {
        return $this->lostItem ? $this->lostItem->staff : null;
    }

    // --- 🌟 數據提取 (偵探功能) ---

    /**
     * 抓取關於這筆配對的所有「黑歷史」
     * 無論是匹配、拒絕還是預約紀錄，通通抓出來
     */
    public function getAuditLogsAttribute()
    {
        return AdminActionLog::where('target_name', 'like', "%Match #{$this->id}%")
            ->orWhere('target_name', 'like', "%#{$this->lostId}%")
            ->orWhere('target_name', 'like', "%#{$this->foundId}%")
            ->orderBy('created_at', 'asc')
            ->get();
    }
}