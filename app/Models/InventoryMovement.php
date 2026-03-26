<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'found_item_id',
        'from_location',
        'to_location',
        'action_type',
        'remarks',
        'performed_by',
    ];

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}