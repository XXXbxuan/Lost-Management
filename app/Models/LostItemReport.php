<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItemReport extends Model
{
    use HasFactory;

    protected $fillable = [
        // 🌟 1. 管理信息：紀錄是哪位 Staff 幫忙登記的
        'staff_id', 

        // 2. 乘客信息
        'passenger_name',
        'passenger_email',
        'passenger_phone',

        // 3. 物品詳情
        'item_name',
        'category',
        'brand',
        'color',
        'serial_number',
        'image_path',

        // 4. 遺失地點與時間
        'lost_location',
        'flight_number',
        'lost_time',
        'description',

        // 5. 狀態系統 (LOST / Matched / Claimed / Rejected)
        'status',
    ];

    /**
     * 🌟 數據類型轉換 (Casts)
     * 確保 lost_time 被視為 Carbon 對象，方便格式化顯示。
     */
    protected $casts = [
        'lost_time' => 'datetime',
    ];

    /**
     * 🌟 關聯：找到負責登記此報失單的員工
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    /**
     * 🌟 關聯：獲取關於此報失單的所有操作日誌
     * 方便查看此單據何時被建立、何時被修改。
     */
    public function logs()
    {
        return $this->hasMany(AdminActionLog::class, 'target_name', 'item_name');
    }
}