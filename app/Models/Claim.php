<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
    'match_id',
    'lostId',
    'foundId',
    'processedBy',
    'processed_by_name',
    'claimerName',
    'claimerIcPassport',
    'handover_photo',
    'claimerPhone',
    'claimedAt',
    'receipt_no',
    'handover_notes',
];

    protected $casts = [
        'claimedAt' => 'datetime',
    ];


    public function matchRecord()
    {
        return $this->belongsTo(MatchRecord::class, 'match_id');
    }


    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class, 'foundId');
    }


    public function lostItem()
    {
        return $this->belongsTo(LostItemReport::class, 'lostId');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'processedBy');
    }
}