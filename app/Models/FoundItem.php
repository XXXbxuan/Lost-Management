<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundItem extends Model
{
    use HasFactory;

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

    protected $casts = [
        'found_time' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }
}