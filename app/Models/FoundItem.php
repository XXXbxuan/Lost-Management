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
        'removed_at',
        'removal_reason',
    ];


    protected $casts = [
        'found_time' => 'datetime',
        'created_at' => 'datetime',
    ];


    public function staff()
{
    return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
}

 
    public function matches()
    {
        return $this->hasMany(MatchRecord::class, 'foundId');
    }


    public function auditLogs()
    {
        return AdminActionLog::where('target_name', 'like', "%#{$this->id}%")
            ->orWhere('target_name', 'like', "%{$this->item_name}%")
            ->orderBy('created_at', 'asc')
            ->get();
    }
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function storageSlot()
    {
        return $this->belongsTo(StorageSlot::class, 'storage_location', 'full_code');
    }
}