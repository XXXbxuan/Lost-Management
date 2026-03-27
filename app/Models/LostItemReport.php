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

    /**
     * 關聯：登記這筆 lost report 的 staff
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    /**
     * 關聯：這筆 lost report 的 match records
     */
    public function matchRecords()
    {
        return $this->hasMany(MatchRecord::class, 'lostId', 'id');
    }

    /**
     * 關聯：操作日誌
     */
    public function logs()
    {
        return $this->hasMany(AdminActionLog::class, 'target_name', 'item_name');
    }
}