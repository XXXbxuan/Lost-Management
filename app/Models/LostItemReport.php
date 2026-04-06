<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItemReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'passenger_name',
        'passenger_email',
        'passenger_phone',
        'item_name',
        'category',
        'brand',
        'color',
        'serial_number',
        'image_path',
        'lost_location',
        'flight_number',
        'lost_time',
        'description',
        'status',
    ];

    protected $casts = [
        'lost_time' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    public function matchRecords()
    {
        return $this->hasMany(MatchRecord::class, 'lostId', 'id');
    }
}