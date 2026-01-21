<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'lostId', 'foundId', 'notes', 'status', 'verifiedBy', 'verifiedAt', 'similarityScore'
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