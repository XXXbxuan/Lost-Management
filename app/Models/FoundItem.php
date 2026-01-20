<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundItem extends Model
{
    use HasFactory;

    // 这一行很重要，告诉 Laravel 用 found_items 表
    protected $table = 'found_items';

    protected $fillable = [
        'item_name', 'category', 'brand', 'color', 'serial_number',
        'found_location', 'flight_number', 'found_time', 'description',
        'storage_location', 'image_path', 'status', 'staff_id', 'registered_by_name',
    ];

    // 关联 Staff
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }
}