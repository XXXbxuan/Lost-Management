<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'lostId',
        'foundId', 
        'notes', 
        'status', 
        'verifiedBy', 
        'verifiedAt', 
        'similarityScore',
        'appointment_at',
        'appointment_venue',
        'verification_token',
        'is_confirmed',
        'confirmed_at'
    ];

    protected $casts = [
        'appointment_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'is_confirmed' => 'boolean',
    ];

    // 如果你想关联 LostItemReport
    public function lostItem()
    {
        return $this->belongsTo(LostItemReport::class, 'lostId');
    }

    // 如果你想关联 FoundItem
    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'foundId');
    }
}