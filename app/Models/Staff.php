<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'staff_id';

    protected $fillable = [
        'user_id',
        'name',
        'contact_number',
        'status',
        'department'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}