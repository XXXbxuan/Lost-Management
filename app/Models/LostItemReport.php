<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItemReport extends Model
{
    use HasFactory;

    // 这一行保护数据不被恶意修改，同时允许这些字段被存入
    protected $fillable = [
        // 乘客信息
        'staff_id', // 🌟 補上這行
        'passenger_name',
        'passenger_email',
        'passenger_phone',


        // 物品详情 (来自组件)
        'item_name',
        'category',
        'brand',
        'color',
        'serial_number',
        'image_path',

        // 丢失地点
        'lost_location',
        'flight_number',
        'lost_time',
        'description',

        // 状态
        'status',
    ];
    protected $guarded = [];

    // 🔥 [新增] 告诉 Laravel 这些字段是时间，不是文字！
    protected $casts = [
        'lost_time' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }
    public function logs()
    {
        return $this->hasMany(AdminActionLog::class, 'target_name', 'item_name');
    }
}