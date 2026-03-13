<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItemReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'passenger_name',
        'passenger_email',
        'passenger_phone',

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

    protected $casts = [
        'lost_time' => 'datetime',
    ];
}