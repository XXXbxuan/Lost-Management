<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundItem extends Model
{
    use HasFactory;

    // 1. 指定表名
    protected $table = 'found_items';

    // 2. 統一可填充欄位
    protected $fillable = [
        'item_name', 
        'category', 
        'brand', 
        'color', 
        'serial_number',
        'found_location', 
        'flight_number', 
        'found_time', 
        'description',
        'storage_location', 
        'image_path', 
        'status', 
        'staff_id',           // 鏈接至登記人 ID
        'registered_by_name', // 備份登記人姓名 (冗餘存儲，增加讀取效率)
    ];

    /**
     * 🌟 3. 數據類型轉換 (Casts)
     * 確保 Phase 1 在 Timeline 顯示時，時間格式化絕對不會報錯。
     */
    protected $casts = [
        'found_time' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * 🌟 4. 核心關聯：登記此物品的 Staff (Phase 1)
     * 確保 Timeline 的「By: Admin」能精準抓到登記人的名字。
     */
    public function staff()
    {
        // 如果你的 Staff 表主鍵是 id，請改為 'id'；如果真的是 'staff_id' 則保持不變
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }

    /**
     * 🌟 5. 大一統關聯：鏈接至配對紀錄 (Match)
     * 從遺失物看它被配對到了哪筆報失單。
     */
    public function matches()
    {
        return $this->hasMany(MatchRecord::class, 'foundId');
    }

    /**
     * 🌟 6. 鏈接原始審計日誌 (Grand Unified Connection)
     * 讓這個 Model 可以直接抓取 AdminActionLog 裡關於這件物品的「黑歷史」。
     * 無論是 REGISTER_FOUND_ITEM 還是未來的 BLOCK_ITEM，通通在這裡。
     */
    public function auditLogs()
    {
        return AdminActionLog::where('target_name', 'like', "%#{$this->id}%")
            ->orWhere('target_name', 'like', "%{$this->item_name}%")
            ->orderBy('created_at', 'asc')
            ->get();
    }
}