<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItem extends Model
{
    use HasFactory;

    // 白名单：允许被批量写入的字段
    protected $fillable = [
        'item_name',
        'category',
        'brand',          // New
        'color',          // New
        'serial_number',  // New
        'description',
        'found_location',
        'found_time',
        'image_path',
        'status',
        'staff_id',
        'registered_by_name',
    ];

    /**
     * 关联：一个物品属于一个 Staff (登记人)
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}