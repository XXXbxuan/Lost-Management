<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'points',
        'description',
    ];

    public function redemptions()
    {
        return $this->hasMany(Redemption::class);
    }
}