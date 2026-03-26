<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageSlot extends Model
{
    protected $fillable = [
        'zone_code',
        'shelf_code',
        'slot_code',
        'full_code',
        'slot_status',
        'remark',
    ];
}