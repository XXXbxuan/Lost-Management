<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redemption extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}