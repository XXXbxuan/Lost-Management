<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'lostId', 'foundId', 'notes', 'status', 'verifiedBy', 
        'verifiedAt', 'similarityScore', 'appointment_at',
        'appointment_venue', 'verification_token', 'is_confirmed', 'confirmed_at','suggested_time_1',
    'suggested_time_2',
    'suggested_remarks',
    'rejected_at',
    ];

    protected $casts = [
        'appointment_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'is_confirmed' => 'boolean',
    ];

    // ✅ 統一使用 lostItem
    public function lostItem()
    {
        return $this->belongsTo(LostItemReport::class, 'lostId');
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'foundId');
    }
}