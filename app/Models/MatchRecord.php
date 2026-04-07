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
        'confirmed_at',
        'suggested_time_1',
        'suggested_time_2',
        'suggested_remarks',
        'rejected_at',
    ];

    protected $casts = [
        'appointment_at'   => 'datetime',
        'confirmed_at'     => 'datetime',
        'verifiedAt'       => 'datetime',
        'rejected_at'      => 'datetime',
        'suggested_time_1' => 'datetime',
        'suggested_time_2' => 'datetime',
        'is_confirmed'     => 'boolean',
    ];

    public function lostItem()
    {
        return $this->belongsTo(LostItemReport::class, 'lostId', 'id');
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'foundId', 'id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifiedBy');
    }

    public function claim()
    {
        return $this->hasOne(Claim::class, 'match_id');
    }

    public function getRegistrarAttribute()
    {
        return $this->lostItem ? $this->lostItem->staff : null;
    }

    public function getAuditLogsAttribute()
    {
        return AdminActionLog::where('target_name', 'like', "%Match #{$this->id}%")
            ->orWhere('target_name', 'like', "%#{$this->lostId}%")
            ->orWhere('target_name', 'like', "%#{$this->foundId}%")
            ->orderBy('created_at', 'asc')
            ->get();
    }
}