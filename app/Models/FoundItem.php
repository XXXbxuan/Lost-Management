<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundItem extends Model
{
    use HasFactory;

    // 指定表名
    protected $table = 'found_items';

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
        'staff_id', 
        'registered_by_name',
    ];

    // 🔥 [新增] 关键设置：告诉 Laravel found_time 是时间格式
    // 如果没有这行，Controller 里的 ->format() 就会报错！
    protected $casts = [
        'found_time' => 'datetime',
    ];

    // 关联 Staff
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }
}