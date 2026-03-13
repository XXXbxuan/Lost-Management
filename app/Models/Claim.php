<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'lostId', 'foundId', 'processedBy', 
        'claimerName', 'claimerIcPassport', 'claimerPhone', 'claimedAt'
    ];

    protected $casts = [
        'claimedAt' => 'datetime',
    ];

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